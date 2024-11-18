<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $incrementing = true; 
    protected $keyType = 'int';
    protected $fillable = [
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
    ];
}
