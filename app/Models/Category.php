<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    protected $table = 'category';

    protected $appends = ['products_count'];

    function products()
    {
        return $this->hasMany(Product::class);
    }

    function getProductsCountAttribute()
    {
        return $this->products()->count() ?? 0;
    }
    
}
