<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Maintenance extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_maintenances')
            ->withPivot('price');
    }
}
