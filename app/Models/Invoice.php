<?php

namespace App\Models;

use Session;
use App\Traits\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory, DateTime, SoftDeletes;

    protected $appends = ['paid'];

    protected $fillable = [
        'invoice_number',
        'order_number',
        'invoice_status_code',
        'invoiced_at',
        'due_at',
        'amount',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_tax_number',
        'customer_phone',
        'customer_address',
        'notes',
        'parent_id',
        'attachment',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function histories()
    {
        return $this->hasMany(InvoiceHistory::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function totals()
    {
        return $this->hasMany(InvoiceTotal::class);
    }

    public function scopeDue($query, $date)
    {
        return $query->whereDate('due_at', '=', $date);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('paid_at', 'desc');
    }

    public function scopeAccrued($query)
    {
        return $query->where('invoice_status_code', '<>', 'draft');
    }

    public function scopePaid($query)
    {
        return $query->where('invoice_status_code', '=', 'paid');
    }

    public function scopeNotPaid($query)
    {
        return $query->where('invoice_status_code', '<>', 'paid');
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (double) $value;
    }

    public function getPaidAttribute()
    {
        $paid = 0;
        if ($this->payments->count()) {
            foreach ($this->payments as $item) {
                $amount = (double) $item->amount;
                $paid += $amount;
            }
        }
        return $paid;
    }
}
