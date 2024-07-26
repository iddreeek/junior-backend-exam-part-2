<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Item extends Model
{
    protected $collection = 'Items';
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category_id', // Reference to the category
    ];
}
