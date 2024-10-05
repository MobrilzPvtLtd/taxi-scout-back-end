<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'package_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'user_id', 'id');
    }
}
