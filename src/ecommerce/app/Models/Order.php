<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_maintenance_id',
        'user_id',
        'maintenance_date',
    ];

    public function productMaintenance(): BelongsTo
    {
        return $this->belongsTo('products_maintenances');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array {
        return [
            'maintenance_date' => 'date',
        ];
    }
}
