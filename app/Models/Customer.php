<?php

namespace App\Models;

use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'email',
        'tax_number',
        'phone',
        'address',
        'website',
        'currency_code',
        'enabled',
        'reference'
    ];

    public $sortable = [
        'name',
        'email',
        'phone',
        'enabled'
    ];
}
