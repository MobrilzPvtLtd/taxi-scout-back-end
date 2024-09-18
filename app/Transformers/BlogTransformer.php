<?php

namespace App\Transformers;

use App\Models\Admin\Blog;
use App\Base\Constants\Setting\Settings;

class BlogTransformer extends Transformer
{
    /**
     * A Fractal transformer.
     *
     * @param Country $country
     * @return array
     */
    public function transform(Blog $blog)
    {
        $params= [
            'id' => $blog->id,
            'user_id' => $blog->user_id,
            'blog_category_id' => $blog->blog_category_id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'image' => asset('blog/'.$blog->image),
            'description'=>$blog->description,
            'status'=>(bool)$blog->status,
            'created_at'=>$blog->created_at,
            'updated_at' => $blog->updated_at,
            'category_name' => $blog->blogCategory->category_name,
            'user_name' => $blog->user->name,
        ];

        return $params;
    }
}
