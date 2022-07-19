<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'sale_price',
        'purchase_price',
        'quantity',
        'enabled',
        'picture'
    ];

    protected $sortable = [
        'name',
        'quantity',
        'sale_price',
        'purchase_price',
        'enabled'
    ];
}
