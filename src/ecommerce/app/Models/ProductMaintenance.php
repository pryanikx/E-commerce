<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMaintenance extends Model
{
    protected $table = 'products_maintenances';

    protected $fillable = [
        'product_id',
        'maintenance_id',
        'price'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
