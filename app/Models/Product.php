<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'promotion_price',
        'promotion_active',
        'promotion_start',
        'promotion_end',
        'image',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'promotion_price' => 'decimal:2',
            'promotion_active' => 'boolean',
            'promotion_start' => 'datetime',
            'promotion_end' => 'datetime',
            'is_active' => 'boolean',
            'stock' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function hasActivePromotion(): bool
    {
        if (!$this->promotion_active || !$this->promotion_price) {
            return false;
        }

        $now = now();

        if ($this->promotion_start && $now->lt($this->promotion_start)) {
            return false;
        }

        if ($this->promotion_end && $now->gt($this->promotion_end)) {
            return false;
        }

        return true;
    }

    public function currentPrice(): float
    {
        return $this->hasActivePromotion() ? $this->promotion_price : $this->price;
    }

    public function promotionPercentage(): ?int
    {
        if (!$this->hasActivePromotion() || $this->price <= 0) {
            return null;
        }

        return (int) round((1 - $this->promotion_price / $this->price) * 100);
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
