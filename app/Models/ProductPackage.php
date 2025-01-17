<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPackage extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        //'business_id','business_Address','business_TIN',
    
        'product_package_id',

        'product_id',
        'product_name',
        'product_quantity',
    ];

    public function packageName()
    {
        return $this->belongsTo(ProductPackageName::class, 'product_package_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
}

