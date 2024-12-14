<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'brand',
        'price',
        'category',
        'total_stock',
        'stock',
        'sold',
        'status',
        'description',
        'expDate',
        'image',
        'on_sale',
        'on_sale_price',
        'featured',
    ];

    protected $attributes = [
        'sold' => 0,
    ];

    public function getSoldAttribute($value)
    {
        return $value ?? 0;
    }
}