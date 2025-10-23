<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /* =============================
     |  Scopes
     |============================= */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    public function formattedRate(int $precision = 4): string
    {
        return number_format((float) $this->exchange_rate, $precision);
    }

    /* =============================
     |  Caching Helpers
     |============================= */
    public static function cacheKeyDefault(): string
    {
        return 'currency.default';
    }

    public static function cacheKeyDefaultSymbol(): string
    {
        return 'currency.default.symbol';
    }

    public static function clearCache(): void
    {
        Cache::forget(static::cacheKeyDefault());
        Cache::forget(static::cacheKeyDefaultSymbol());
    }

    public static function getDefault(): ?self
    {
        return Cache::remember(static::cacheKeyDefault(), 3600, fn () => static::default()->first());
    }

    public static function defaultSymbol(): string
    {
        return Cache::remember(static::cacheKeyDefaultSymbol(), 3600, fn () => static::getDefault()?->symbol ?? '$');
    }

    /* =============================
     |  Business Logic
     |============================= */
    public function setAsDefault(): bool
    {
        return DB::transaction(function () {
            // Reset existing default
            static::where('is_default', true)->where('id', '!=', $this->id)->update(['is_default' => false]);
            $updated = $this->update(['is_default' => true, 'is_active' => true]);
            static::clearCache();

            return $updated;
        });
    }

    public function convertTo(float $amount, Currency $targetCurrency, int $precision = 2): float
    {
        if ($targetCurrency->id === $this->id) {
            return round($amount, $precision);
        }
        if ((float) $this->exchange_rate <= 0.0) {
            return 0.0; // defensive: invalid rate
        }
        $baseAmount = $amount / (float) $this->exchange_rate; // to base (assumed default)
        $converted = $baseAmount * (float) $targetCurrency->exchange_rate;

        return round($converted, $precision);
    }

    public function convertFrom(float $amount, Currency $sourceCurrency, int $precision = 2): float
    {
        return $sourceCurrency->convertTo($amount, $this, $precision);
    }

    public static function convertAmount(float $amount, string $fromCode, string $toCode, int $precision = 2): ?float
    {
        $from = static::active()->code($fromCode)->first();
        $to = static::active()->code($toCode)->first();
        if (! $from || ! $to) {
            return null;
        }

        return $from->convertTo($amount, $to, $precision);
    }

    /* =============================
     |  Accessors / Mutators
     |============================= */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim($value))
        );
    }

    protected function symbol(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim($value)
        );
    }

    /* =============================
     |  Model Events
     |============================= */
    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if ($model->is_default) {
                // Ensure no duplicate defaults in same request before commit
                static::where('id', '!=', $model->id)->where('is_default', true)->update(['is_default' => false]);
            }
        });

        static::saved(function (): void {
            static::clearCache();
        });

        static::deleted(function (): void {
            static::clearCache();
        });
    }
}
