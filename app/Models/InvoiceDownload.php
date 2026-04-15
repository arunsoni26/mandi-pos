<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDownload extends Model
{
    protected $fillable = ['user_id', 'downloaded_at'];

    public function invoice()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
