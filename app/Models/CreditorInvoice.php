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

    public function downloads(){
        return $this->morphMany(InvoiceDownload::class, 'invoice');
    }

    protected static function booted()
    {
        // Create
        static::created(function ($invoice) {
            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'action' => 'created',
                'model_type' => self::class,
                'model_id' => $invoice->id,
                'new_values' => $invoice->toArray(),
            ]);
        });

        // Update
        static::updated(function ($invoice) {
            $changes = $invoice->getChanges(); // only changed attributes

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'action' => 'updated',
                'model_type' => self::class,
                'model_id' => $invoice->id,
                'old_values' => array_intersect_key($invoice->getOriginal(), $changes),
                'new_values' => $changes,
            ]);
        });

        // Soft delete
        static::deleted(function ($invoice) {
            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'action' => 'cancelled',
                'model_type' => self::class,
                'model_id' => $invoice->id,
                'old_values' => $invoice->toArray(),
            ]);
        });

        // Restore
        static::restored(function ($invoice) {
            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'action' => 'restored',
                'model_type' => self::class,
                'model_id' => $invoice->id,
                'new_values' => $invoice->toArray(),
            ]);
        });
    }
}
