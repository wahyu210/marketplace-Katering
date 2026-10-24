<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'company_name', 'slug', 'city', 'address', 'phone', 'description', 'logo'])]
class Merchant extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Merchant $merchant) {
            if (blank($merchant->slug)) {
                $merchant->slug = static::uniqueSlug($merchant->company_name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'katering';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }

    /** Two-letter monogram used when no logo is uploaded. */
    public function initials(): string
    {
        return Str::of($this->company_name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))
            ->implode('');
    }

    /**
     * Search filters for the customer catering directory.
     *
     * @param  array{q?: ?string, city?: ?string, category?: ?string, max_price?: ?string}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $term) {
                $like = '%'.mb_strtolower($term).'%';
                $q->where(function (Builder $q) use ($like) {
                    $q->whereRaw('LOWER(company_name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(description) LIKE ?', [$like])
                        ->orWhereHas('menus', fn (Builder $m) => $m->whereRaw('LOWER(name) LIKE ?', [$like]));
                });
            })
            ->when($filters['city'] ?? null, fn (Builder $q, string $city) => $q->where('city', $city))
            ->when($filters['category'] ?? null, fn (Builder $q, string $slug) => $q->whereHas(
                'menus',
                fn (Builder $m) => $m->where('is_available', true)->whereHas('category', fn (Builder $c) => $c->where('slug', $slug))
            ))
            ->when($filters['max_price'] ?? null, fn (Builder $q, $max) => $q->whereHas(
                'menus',
                fn (Builder $m) => $m->where('is_available', true)->where('price', '<=', (int) $max)
            ));
    }
}
