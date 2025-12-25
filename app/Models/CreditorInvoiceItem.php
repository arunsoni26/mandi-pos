<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditorInvoiceItem extends Model
{
    protected $fillable = [
        'creditor_invoice_id',
        'product_name',
        'pieces',
        'weight',
        'rate',
        'total',
        'customer_name',
        'status',
        'updated_by',
    ];

    // Line item → belongs to creditor invoice
    public function creditorInvoice()
    {
        return $this->belongsTo(CreditorInvoice::class);
    }
}
