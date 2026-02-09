<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtorInvoice extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'debtor_customer_id',
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

    // One debtor invoice → many line items
    public function items()
    {
        return $this->hasMany(DebtorInvoiceItem::class);
    }

    public function debitor()
    {
        return $this->belongsTo(Customer::class, 'debtor_customer_id');
    }

    public function creditor()
    {
        return $this->belongsTo(Customer::class, 'creditor_id');
    }
}
