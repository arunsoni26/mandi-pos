<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditorInvoiceItem extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'creditor_invoice_id',
        'product_name',
        'pieces',
        'weight',
        'rate',
        'total',
        'debtor_customer_id',
        'status',
        'updated_by',
    ];

    // Line item → belongs to creditor invoice
    public function creditorInvoice()
    {
        return $this->belongsTo(CreditorInvoice::class, 'creditor_invoice_id');
    }

    // Line item → belongs to creditor invoice
    public function debtorCustomer()
    {
        return $this->belongsTo(Customer::class, 'debtor_customer_id');
    }
}
