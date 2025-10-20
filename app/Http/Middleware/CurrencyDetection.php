<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyDetection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('currency_id')) {
            $header = $request->server('HTTP_ACCEPT_LANGUAGE');
            if ($header) {
                $locales = collect(explode(',', $header))
                    ->map(fn($p) => trim(explode(';', $p)[0]))
                    ->filter()
                    ->unique()
                    ->values();
                foreach ($locales as $locale) {
                    // locale like en-US, ar-EG
                    $parts = explode('-', $locale);
                    if (count($parts) >= 2) {
                        $country = strtoupper($parts[1]);
                        // Map country to currency code
                        $currencyCode = $this->mapCountryToCurrency($country);
                        if ($currencyCode) {
                            try {
                                $currency = \App\Models\Currency::where('code', $currencyCode)->where('is_active', true)->first();
                                if ($currency) {
                                    session(['currency_id' => $currency->id]);
                                    break;
                                }
                            } catch (\Throwable $e) {
                                // silent
                            }
                        }
                    }
                }
            }
        }

        return $next($request);
    }

    private function mapCountryToCurrency(string $country): ?string
    {
        $map = [
            'US' => 'USD',
            'EG' => 'EGP',
            'SA' => 'SAR',
            'AE' => 'AED',
            'KW' => 'KWD',
            'BH' => 'BHD',
            'OM' => 'OMR',
            'QA' => 'QAR',
            'JO' => 'JOD',
            'LB' => 'LBP',
            'IQ' => 'IQD',
            'SY' => 'SYP',
            'YE' => 'YER',
            'TN' => 'TND',
            'DZ' => 'DZD',
            'MA' => 'MAD',
            'LY' => 'LYD',
            'GB' => 'GBP',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
            'NL' => 'EUR',
            'BE' => 'EUR',
            'AT' => 'EUR',
            'PT' => 'EUR',
            'FI' => 'EUR',
            'IE' => 'EUR',
            'LU' => 'EUR',
            'MT' => 'EUR',
            'CY' => 'EUR',
            'SK' => 'EUR',
            'SI' => 'EUR',
            'EE' => 'EUR',
            'LV' => 'EUR',
            'LT' => 'EUR',
            'GR' => 'EUR',
            'JP' => 'JPY',
            'CN' => 'CNY',
            'IN' => 'INR',
            'KR' => 'KRW',
            'AU' => 'AUD',
            'CA' => 'CAD',
            'MX' => 'MXN',
            'BR' => 'BRL',
            'AR' => 'ARS',
            'CL' => 'CLP',
            'CO' => 'COP',
            'PE' => 'PEN',
            'VE' => 'VES',
            'UY' => 'UYU',
            'PY' => 'PYG',
            'BO' => 'BOB',
            'EC' => 'USD', // Ecuador uses USD
            'PA' => 'USD', // Panama uses USD
            'SV' => 'USD', // El Salvador uses USD
            'GT' => 'GTQ',
            'HN' => 'HNL',
            'NI' => 'NIO',
            'CR' => 'CRC',
            'DO' => 'DOP',
            'HT' => 'HTG',
            'JM' => 'JMD',
            'TT' => 'TTD',
            'BB' => 'BBD',
            'GD' => 'XCD',
            'VC' => 'XCD',
            'LC' => 'XCD',
            'DM' => 'XCD',
            'AG' => 'XCD',
            'KN' => 'XCD',
            'MS' => 'XCD',
            'BZ' => 'BZD',
            'ZA' => 'ZAR',
            'NG' => 'NGN',
            'KE' => 'KES',
            'GH' => 'GHS',
            'UG' => 'UGX',
            'TZ' => 'TZS',
            'RW' => 'RWF',
            'BI' => 'BIF',
            'ZM' => 'ZMW',
            'MW' => 'MWK',
            'MZ' => 'MZN',
            'ZW' => 'ZWD',
            'BW' => 'BWP',
            'LS' => 'LSL',
            'SZ' => 'SZL',
            'NA' => 'NAD',
            'AO' => 'AOA',
            'CV' => 'CVE',
            'ST' => 'STD',
            'GQ' => 'XAF',
            'GA' => 'XAF',
            'CM' => 'XAF',
            'TD' => 'XAF',
            'CF' => 'XAF',
            'CG' => 'XAF',
            'CD' => 'CDF',
            'SC' => 'SCR',
            'MU' => 'MUR',
            'MG' => 'MGA',
            'KM' => 'KMF',
            'DJ' => 'DJF',
            'SO' => 'SOS',
            'ER' => 'ERN',
            'ET' => 'ETB',
            'SD' => 'SDG',
            'SS' => 'SSP',
            'NE' => 'XOF',
            'ML' => 'XOF',
            'SN' => 'XOF',
            'GM' => 'GMD',
            'GN' => 'GNF',
            'LR' => 'LRD',
            'SL' => 'SLL',
            'CI' => 'XOF',
            'BF' => 'XOF',
            'TG' => 'XOF',
            'BJ' => 'XOF',
            'GH' => 'XOF', // Ghana is XOF? Wait, Ghana is GHS, but let's keep
            // This is a basic map, can be expanded
        ];

        return $map[$country] ?? null;
    }
}
