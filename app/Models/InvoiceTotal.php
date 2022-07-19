<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceTotal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'code',
        'name',
        'amount',
        'sort_order'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (double) $value;
    }
}
