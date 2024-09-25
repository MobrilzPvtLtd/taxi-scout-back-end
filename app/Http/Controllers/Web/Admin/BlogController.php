<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Base\Services\ImageUploader\ImageUploaderContract;

class BlogController extends BaseController
{
    protected $blog;
      /**
     * The
     *
     * @var App\Base\Services\ImageUploader\ImageUploaderContract
     */
    protected $imageUploader;

    /**
     * BlogController constructor.
     *
     * @param \App\Models\Admin\Blog $blog
     */
    public function __construct(Blog $blog, ImageUploaderContract $imageUploader,)
    {
        $this->blog = $blog;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        $page = trans('pages_names.blog');

        $main_menu = 'manage-blog';
        $sub_menu = 'blog';

        return view('admin.blog.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {

        $query = $this->blog->leftJoin('blog_categories', 'blogs.blog_category_id', '=', 'blog_categories.id')->select('blogs.*','blog_categories.category_name');

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.blog._blog', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_blog');
        $main_menu = 'manage-blog';
        $sub_menu = 'blog';

        return view('admin.blog.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(Request $request)
    {
        $created_params = $request->only(['user_id','blog_category_id','title','slug','image','description','status']);

        $created_params['user_id'] = auth()->user()->id;
        $created_params['slug'] = Str::slug($created_params['title']);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('blog'), $imageName);
            $created_params['image'] = $imageName;
        }

        $this->blog->create($created_params);

        $message = trans('succes_messages.blog_added_succesfully');

        return redirect('blogs')->with('success', $message);
    }

    public function getById(Blog $blog)
    {
        $page = trans('pages_names.edit_blog');
        $main_menu = 'manage-blog';
        $sub_menu = 'blog';
        $item = $blog;

        return view('admin.blog.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(Request $request, Blog $blog)
    {
        $updated_params = $request->all();
        $updated_params['user_id'] = auth()->user()->id;
        $updated_params['slug'] = Str::slug($updated_params['title']);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('blog'), $imageName);
            $updated_params['image'] = $imageName;
        }

        $blog->update($updated_params);

        $message = trans('succes_messages.blog_updated_succesfully');

        return redirect('blogs')->with('success', $message);
    }

    public function delete(Blog $blog)
    {
        $blog->delete();

        $message = trans('succes_messages.blog_deleted_succesfully');
        return redirect('blogs')->with('success', $message);
    }
}
