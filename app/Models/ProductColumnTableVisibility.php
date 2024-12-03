<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColumnTableVisibility extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        //FOR FINANCE CATEGORY
        'column_Table',
        'is_visible',
    ];
}
