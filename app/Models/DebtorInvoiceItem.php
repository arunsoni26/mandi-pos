<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtorInvoiceItem extends Model
{
    protected $fillable = [
        'debtor_invoice_id',
        'product_name',
        'pieces',
        'weight',
        'rate',
        'total',
        'status',
        'updated_by',
    ];

    // Line item → belongs to debtor invoice
    public function debtorInvoice()
    {
        return $this->belongsTo(DebtorInvoice::class);
    }
}
