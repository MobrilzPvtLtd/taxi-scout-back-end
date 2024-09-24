<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Subscription;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id','user_id','start_date','end_date','active'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'package_id', 'id');
    }
}
