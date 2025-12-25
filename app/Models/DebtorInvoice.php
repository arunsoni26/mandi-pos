<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtorInvoice extends Model
{
    protected $fillable = [
        'customer_name',
        'invoice_date',
        'total_amount',
        'total_wage',
        'grand_total',
        'status',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    // One debtor invoice → many line items
    public function items()
    {
        return $this->hasMany(DebtorInvoiceItem::class);
    }
}
