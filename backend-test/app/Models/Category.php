<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    protected $collection = 'Category';

    protected $fillable = [
        'name',
        'description',
    ];
}
