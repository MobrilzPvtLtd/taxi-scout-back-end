<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Models\Admin\BlogCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id','blog_category_id','title','slug','image','description','status'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function blogCategory(){
        return $this->belongsTo(BlogCategory::class,'blog_category_id','id');
    }


}
