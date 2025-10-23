<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    public function welcome()
    {
        return view('install.welcome');
    }

    public function requirements()
    {
        $phpVersion = phpversion();
        $extensions = [
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
        ];

        return view('install.requirements', compact('phpVersion', 'extensions'));
    }

    public function databaseForm()
    {
        return view('install.database');
    }

    public function permissions()
    {
        $paths = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('.env'),
        ];

        $results = [];
        foreach ($paths as $p) {
            $results[] = [
                'path' => $p,
                'exists' => file_exists($p),
                'writable' => is_writable($p),
            ];
        }

        return response()->json(['success' => true, 'paths' => $results]);
    }

    public function testDb(Request $request)
    {
        $data = $request->validate([
            'DB_CONNECTION' => 'required|string',
            'DB_HOST' => 'required|string',
            'DB_PORT' => 'nullable|string',
            'DB_DATABASE' => 'required|string',
            'DB_USERNAME' => 'required|string',
            'DB_PASSWORD' => 'nullable|string',
            'write_test' => 'sometimes|boolean',
        ]);

        // Temporarily set config and attempt a connection
        config(['database.connections.install_test' => [
            'driver' => $data['DB_CONNECTION'],
            'host' => $data['DB_HOST'],
            'port' => $data['DB_PORT'] ?? null,
            'database' => $data['DB_DATABASE'],
            'username' => $data['DB_USERNAME'],
            'password' => $data['DB_PASSWORD'] ?? null,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ],
        ]);

        try {
            DB::connection('install_test')->getPdo();

            // optional lightweight write test: create/insert/drop a temp table to ensure write permissions
            $doWrite = $request->input('write_test', true);
            if ($doWrite) {
                $rand = substr(Str::uuid()->toString(), 0, 8);
                $tname = 'temp_install_' . $rand;
                try {
                    $conn = DB::connection('install_test');
                    // create table
                    $conn->statement(
                        "CREATE TABLE {$tname} (id INT PRIMARY KEY AUTO_INCREMENT, created_at TIMESTAMP NULL);"
                    );
                    // insert
                    $conn->statement("INSERT INTO {$tname} (created_at) VALUES (NOW());");
                    // drop
                    $conn->statement("DROP TABLE {$tname};");
                } catch (\Throwable $we) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Connection OK but write test failed: ' . $we->getMessage(),
                        'code' => $we->getCode(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Connection successful' . ($doWrite ? ' (including write test)' : ''),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    public function saveDb(Request $request)
    {
        $data = $request->validate([
            'DB_CONNECTION' => 'required|string',
            'DB_HOST' => 'required|string',
            'DB_PORT' => 'nullable|string',
            'DB_DATABASE' => 'required|string',
            'DB_USERNAME' => 'required|string',
            'DB_PASSWORD' => 'nullable|string',
        ]);

        try {
            $this->writeEnv([
                'DB_CONNECTION' => $data['DB_CONNECTION'],
                'DB_HOST' => $data['DB_HOST'],
                'DB_PORT' => $data['DB_PORT'] ?? '',
                'DB_DATABASE' => $data['DB_DATABASE'],
                'DB_USERNAME' => $data['DB_USERNAME'],
                'DB_PASSWORD' => $data['DB_PASSWORD'] ?? '',
            ]);

            // Clear config cache so new DB settings take effect
            $cacheNote = null;
            try {
                Artisan::call('config:clear');
                Artisan::call('config:cache');
                $cacheNote = 'config:cache succeeded';
            } catch (\Throwable $ce) {
                $cacheNote = 'config cache failed: ' . $ce->getMessage();
            }

            return response()->json(['success' => true, 'message' => 'DB settings saved', 'note' => $cacheNote]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function createAdmin(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Ensure migrations ran and DB is writable; rely on normal behavior but catch errors
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Database migrations failed: ' . $e->getMessage()]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Give admin role if role system exists; best-effort
        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('admin');
            } catch (\Throwable $e) {
                /* ignore */
            }
        }

        // optionally run db:seed
        try {
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $e) {
            // log but allow continuation
        }

        // create marker file
        try {
            Storage::put('installed', now()->toDateTimeString());
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Could not write installed marker: ' . $e->getMessage()]);
        }

        return redirect()->route('install.complete');
    }

    public function complete()
    {
        return view('install.complete');
    }

    protected function writeEnv(array $vars): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            // copy from .env.example if available
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envPath);
            } else {
                file_put_contents($envPath, "\n");
            }
        }

        $env = file_get_contents($envPath);
        foreach ($vars as $key => $value) {
            $escaped = preg_quote('=' . $this->envValue($key, $env), '/');
            if (Str::contains($env, $key . '=')) {
                $env = preg_replace('/^' . $key . '=.*/m', $key . '="' . addslashes($value) . '"', $env);
            } else {
                $env .= "\n{$key}=\"" . addslashes($value) . '\"';
            }
        }

        file_put_contents($envPath, $env);
    }

    protected function envValue($key, $envContent)
    {
        if (preg_match('/^' . preg_quote($key) . '=(.*)$/m', $envContent, $m)) {
            return trim($m[1]);
        }

        return '';
    }
}
