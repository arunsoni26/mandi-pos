<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditorInvoice extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'creditor_id',
        'invoice_date',
        'total_amount',
        'total_wage',
        'additional_charges',
        'grand_total',
        'status',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    // One creditor invoice → many line items
    public function items()
    {
        return $this->hasMany(CreditorInvoiceItem::class);
    }

    public function creditor()
    {
        return $this->belongsTo(Customer::class, 'creditor_id');
    }
}
