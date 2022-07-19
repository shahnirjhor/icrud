<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'invoice_id',
        'status_code',
        'notify',
        'description'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
