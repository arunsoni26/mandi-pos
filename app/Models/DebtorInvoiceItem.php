<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtorInvoiceItem extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
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
