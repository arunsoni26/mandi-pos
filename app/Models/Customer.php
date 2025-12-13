<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name','user_id','gst_name','father_name','pan','pan_doc','client_type_status',
        'code','mobile_no','email','city','group_id','dob','gst','gst_doc','aadhar','aadhar_doc',
        'address','status','hide_dashboard'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
