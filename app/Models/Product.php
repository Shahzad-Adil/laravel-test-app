<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\Attributes\Product as ProductAttributes;

class Product extends Model
{
    use
    HasFactory,
    ProductAttributes
    ;

    protected $fillable = [
        'name',
        'price',
        'is_admin'
    ];
}
