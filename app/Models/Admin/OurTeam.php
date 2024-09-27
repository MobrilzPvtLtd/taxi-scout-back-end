<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurTeam extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title','name','mobile','email','image','description','status'
    ];
}
