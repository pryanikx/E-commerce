<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'date',
        'description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_services')
        ->withPivot('price', 'created_at', 'updated_at');
    }

    protected function casts(): array {
        return [
            'date' => 'date'
        ];
    }
}
