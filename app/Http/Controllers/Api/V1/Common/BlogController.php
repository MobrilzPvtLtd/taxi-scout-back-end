<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\ApiController;
use App\Models\Admin\Blog;
use App\Transformers\BlogTransformer;

/**
 * @resource blogs
 *
 * Get blogs
 */
class BlogController extends ApiController
{
    /**
     * Get all the blogs.
     *@hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $blogsQuery = Blog::where('blogs.status', true);

        $blogs = filter($blogsQuery, new BlogTransformer)->defaultSort('title')->get();

        return $this->respondOk($blogs);
    }

    /**
     * Get all blogs by state
     *
     *@hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function blogDetails($slug)
    {
        $blogQuery = Blog::where('slug',$slug);
        $blog = filter($blogQuery, new BlogTransformer)->first();

        return $this->respondOk($blog);
    }

}
