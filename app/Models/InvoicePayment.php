<?php

namespace App\Models;

use App\Traits\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoicePayment extends Model
{
    use HasFactory, DateTime, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'account_id',
        'paid_at',
        'amount',
        'description',
        'payment_method',
        'reference'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('paid_at', 'desc');
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (double) $value;
    }

    public function scopePaid($query)
    {
        return $query->sum('amount');
    }
}
