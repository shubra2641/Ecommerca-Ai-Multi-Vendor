<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use LicenseProtection\LicenseVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function welcome(): View
    {
        return view('install.welcome');
    }

    public function requirements(): View
    {
        $requirements = [
            'php_version' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'extensions' => [
                'openssl' => extension_loaded('openssl'),
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
                'ctype' => extension_loaded('ctype'),
                'json' => extension_loaded('json'),
                'bcmath' => extension_loaded('bcmath'),
                'fileinfo' => extension_loaded('fileinfo'),
                'curl' => extension_loaded('curl'),
                'gd' => extension_loaded('gd'),
            ],
            'writable_directories' => [
                'storage' => is_writable(storage_path()),
                'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            ],
        ];

        $allRequirementsMet = $requirements['php_version'] &&
            !in_array(false, $requirements['extensions']) &&
            !in_array(false, $requirements['writable_directories']);

        return view('install.requirements', compact('requirements', 'allRequirementsMet'));
    }

    public function database(): View
    {
        return view('install.database');
    }

    public function databaseStore(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Test database connection without database name first
            $connectionWithoutDb = [
                'driver' => 'mysql',
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => null, // No database selected
                'username' => $request->db_username,
                'password' => $request->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];

            config(['database.connections.test' => $connectionWithoutDb]);
            $pdo = DB::connection('test')->getPdo();

            // Try to create database if it doesn't exist
            try {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$request->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Exception $e) {
                // Database might already exist or user doesn't have CREATE privileges
                // Continue with the installation
            }

            // Now test connection with database name
            $connection = [
                'driver' => 'mysql',
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => $request->db_name,
                'username' => $request->db_username,
                'password' => $request->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];

            config(['database.connections.test' => $connection]);
            DB::connection('test')->getPdo();

            // Store database config in session
            session([
                'install.database' => $connection
            ]);

            return redirect()->route('install.admin');
        } catch (\Exception $e) {
            return back()->withErrors(['database' => 'Database connection failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function admin(): View
    {
        return view('install.admin');
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store admin data in session
        session([
            'install.admin' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]
        ]);

        return redirect()->route('install.license');
    }

    public function license(): View
    {
        return view('install.license');
    }

    public function licenseStore(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'purchase_code' => ['required', 'string', 'min:5', 'max:100'],
            ]);

            if ($validator->fails()) {
                return $this->handleValidationError($request, $validator->errors()->first());
            }

            $purchaseCode = $request->input('purchase_code');
            $domain = $request->getHost();

            // Dev bypass: accept 123456 in non-production to speed up testing
            if ($purchaseCode === '123456' && !app()->environment('production')) {
                session(['install.license' => [
                    'purchase_code' => $purchaseCode,
                    'domain' => $domain,
                    'valid' => true,
                    'message' => 'Development test license accepted',
                ]]);

                return $this->verificationResponse(
                    $request,
                    true,
                    'License verified successfully (dev bypass)',
                    ['purchase_code' => $purchaseCode, 'domain' => $domain]
                );
            }

            // Use the actual LicenseVerifier
            $verifier = new LicenseVerifier();
            $verificationResult = $verifier->verifyLicense($purchaseCode, $domain);

            if ($verificationResult['valid'] ?? false) {
                session(['install.license' => $verificationResult['data']]);

                return $this->verificationResponse(
                    $request,
                    true,
                    'License verified successfully',
                    $verificationResult
                );
            } else {
                $message = $verificationResult['message'] ?? 'License verification failed';

                return $this->verificationResponse($request, false, $message, $verificationResult);
            }
        } catch (\Throwable $exception) {
            Log::error('License verification error in InstallController', ['error' => $exception->getMessage()]);

            return $this->verificationResponse(
                $request,
                false,
                'An error occurred during verification',
                ['general' => $exception->getMessage()],
                500
            );
        }
    }

    public function install(): View
    {
        return view('install.install');
    }

    public function installProcess(Request $request): RedirectResponse
    {
        try {
            // Get database config from session
            $dbConfig = session('install.database');
            if (!$dbConfig) {
                return redirect()->route('install.database')->withErrors(['error' => 'Database configuration not found']);
            }

            // Update .env file with database config
            $this->updateEnvFile($dbConfig);

            // Set the database connection for this request
            config(['database.connections.mysql' => $dbConfig]);
            DB::purge('mysql');

            // Determine operation based on user choice or auto-detection
            $paramFreshProvided = $request->has('fresh');
            $paramSeedProvided = $request->has('seed');

            // Detect if this is a fresh install (no migrations have been run yet)
            $freshInstallDetected = false;
            try {
                if (!Schema::hasTable('migrations')) {
                    $freshInstallDetected = true;
                } else {
                    $freshInstallDetected = DB::table('migrations')->count() === 0;
                }
            } catch (\Throwable $e) {
                // If we cannot query migrations table, treat as fresh
                $freshInstallDetected = true;
            }

            $doFresh = $paramFreshProvided ? (bool) $request->boolean('fresh') : false;
            $doSeed = $paramSeedProvided ? (bool) $request->boolean('seed') : $freshInstallDetected;

            // Run migrations and fail early if something goes wrong
            if ($doFresh) {
                $migrateExit = Artisan::call('migrate:fresh', ['--force' => true]);
            } else {
                $migrateExit = Artisan::call('migrate', ['--force' => true]);
            }
            if ($migrateExit !== 0) {
                $migrateOutput = Artisan::output();
                throw new \RuntimeException('Migrations failed: ' . $migrateOutput);
            }

            // Seed data based on user choice or detection
            if ($doSeed) {
                $seedExit = Artisan::call('db:seed', ['--force' => true]);
                if ($seedExit !== 0) {
                    $seedOutput = Artisan::output();
                    throw new \RuntimeException('Seeding failed: ' . $seedOutput);
                }
            } else {
                Log::info('Installer: seeding skipped');
            }

            // Create or update admin user (ensure it's verified and approved)
            $adminData = session('install.admin');
            if ($adminData) {
                $admin = User::updateOrCreate(
                    ['email' => $adminData['email']],
                    [
                        'name' => $adminData['name'],
                        'password' => Hash::make($adminData['password']),
                        'role' => 'admin',
                        'approved_at' => now(),
                    ]
                );
                // email_verified_at is not mass-assignable on User; set explicitly
                if (empty($admin->email_verified_at)) {
                    $admin->forceFill(['email_verified_at' => now()])->save();
                }
            } else {
                // Fallback: if there is an existing admin, make sure they are verified/approved
                $existingAdmin = User::where('role', 'admin')->first();
                if ($existingAdmin) {
                    $updates = [];
                    if (empty($existingAdmin->email_verified_at)) {
                        $updates['email_verified_at'] = now();
                    }
                    if (empty($existingAdmin->approved_at)) {
                        $updates['approved_at'] = now();
                    }
                    if (!empty($updates)) {
                        $existingAdmin->forceFill($updates)->save();
                    }
                }
            }

            // Create default settings
            $this->createDefaultSettings();

            // Create installation markers (.installed in storage and legacy installed in project root)
            Log::info('Marking application as installed');
            $markerContent = now()->toDateTimeString();

            // Write storage marker using Storage API (works with local driver)
            try {
                Storage::put('.installed', $markerContent);
                Log::info('Storage marker written via Storage::put');
            } catch (\Throwable $e) {
                Log::warning('Storage::put failed for .installed: ' . $e->getMessage());
            }

            // Ensure physical file exists at storage_path as well
            try {
                if (!is_dir(storage_path())) {
                    @mkdir(storage_path(), 0775, true);
                }
                @file_put_contents(storage_path('.installed'), $markerContent);
            } catch (\Throwable $e) {
                Log::warning('file_put_contents failed for storage/.installed: ' . $e->getMessage());
            }

            // Legacy marker fallback at project root
            try {
                @file_put_contents(base_path('installed'), $markerContent);
            } catch (\Throwable $e) {
                Log::warning('file_put_contents failed for base_path installed: ' . $e->getMessage());
            }

            return redirect()->route('install.complete');
        } catch (\Exception $e) {
            Log::error('Installation failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Installation failed: ' . $e->getMessage()]);
        }
    }

    public function complete(): View
    {
        return view('install.complete');
    }

    private function updateEnvFile(array $dbConfig): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            // Create .env from .env.example if it doesn't exist
            $envExamplePath = base_path('.env.example');
            if (file_exists($envExamplePath)) {
                copy($envExamplePath, $envPath);
            } else {
                // Create basic .env file
                $basicEnv = "APP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=laravel\nDB_USERNAME=root\nDB_PASSWORD=\n";
                file_put_contents($envPath, $basicEnv);
            }
        }

        $envContent = file_get_contents($envPath);

        // Update database configuration
        $envContent = preg_replace('/^DB_HOST=.*/m', 'DB_HOST=' . $dbConfig['host'], $envContent);
        $envContent = preg_replace('/^DB_PORT=.*/m', 'DB_PORT=' . $dbConfig['port'], $envContent);
        $envContent = preg_replace('/^DB_DATABASE=.*/m', 'DB_DATABASE=' . $dbConfig['database'], $envContent);
        $envContent = preg_replace('/^DB_USERNAME=.*/m', 'DB_USERNAME=' . $dbConfig['username'], $envContent);
        $envContent = preg_replace('/^DB_PASSWORD=.*/m', 'DB_PASSWORD=' . $dbConfig['password'], $envContent);

        file_put_contents($envPath, $envContent);

        // Clear config cache
        Artisan::call('config:clear');
    }

    private function createDefaultSettings(): void
    {
        Setting::create([
            'site_name' => 'Easy Store',
            'contact_email' => session('install.admin.email', 'admin@easystore.com'),
            'contact_phone' => '+1234567890',
            'seo_description' => 'Easy Store - Your online marketplace',
            'maintenance_enabled' => false,
            'min_withdrawal_amount' => 50.00,
            'withdrawal_commission_enabled' => true,
            'withdrawal_commission_rate' => 5.00,
            'commission_mode' => 'percentage',
            'commission_flat_rate' => 0.00,
            'font_family' => 'Inter',
            'auto_publish_reviews' => true,
            'ai_enabled' => false,
            'enable_external_payment_redirect' => false,
            'recaptcha_enabled' => false,
        ]);
    }

    private function handleValidationError(Request $request, string $message): RedirectResponse
    {
        return back()->withErrors(['purchase_code' => $message])->withInput();
    }

    private function verificationResponse(Request $request, bool $success, string $message, array $data = [], int $status = 200): RedirectResponse
    {
        if ($success) {
            return redirect()->route('install.install')->with('success', $message);
        } else {
            return back()->withErrors(['purchase_code' => $message])->withInput();
        }
    }
}
