<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'name',
        'sku',
        'quantity',
        'price',
        'total'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (double) $value;
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = (double) $value;
    }
}
