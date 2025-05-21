<?php

declare(strict_types=1);

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
     * @var array
     */
    protected $fillable = [
        'name',
        'article',
        'description',
        'release_date',
        'price',
        'image_path',
        'manufacturer_id',
        'category_id',
    ];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    /**
     * @return BelongsToMany
     */
    public function maintenances(): BelongsToMany
    {
        return $this->belongsToMany(Maintenance::class, 'products_maintenances')
        ->withPivot('price');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'price' => 'decimal:2',
        ];
    }
}
