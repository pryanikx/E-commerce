<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'article',
        'description',
        'release_date',
        'price',
        'image_path',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'products_services')
        ->withPivot('price', 'created_at', 'updated_at');
    }

    protected function casts(): array {
        return [
            'release_date' => 'date',
            'price' => 'decimal:2',
        ];
    }
}
