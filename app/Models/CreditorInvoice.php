<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditorInvoice extends Model
{
    protected $fillable = [
        'creditor_id',
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

    // One creditor invoice → many line items
    public function items()
    {
        return $this->hasMany(CreditorInvoiceItem::class);
    }
}
