<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends BaseController
{
    protected $blogCategory;

    /**
     * BlogCategoryController constructor.
     *
     * @param \App\Models\Admin\BlogCategory $blogCategory
     */
    public function __construct(BlogCategory $blogCategory)
    {
        $this->blogCategory = $blogCategory;
    }

    public function index()
    {
        $page = trans('pages_names.blog-category');

        $main_menu = 'blog-category';
        $sub_menu = '';

        return view('admin.blog-category.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->blogCategory->query();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.blog-category._blogCategory', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_blog_category');
        $main_menu = 'blog-category';
        $sub_menu = '';

        return view('admin.blog-category.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(Request $request)
    {
        $created_params = $request->only(['category_name','category_slug','status']);

        $created_params['category_slug'] = Str::slug($created_params['category_name']);

        $validate_exists_category = $this->blogCategory->where('category_name', $request->category_name)->exists();

        if ($validate_exists_category) {
            return redirect()->back()->withErrors(['category_name'=>'Provided Category Name hs already been taken'])->withInput();
        }
        $this->blogCategory->create($created_params);

        $message = trans('succes_messages.blog_category_added_succesfully');

        return redirect('blog-category')->with('success', $message);
    }

    public function getById(BlogCategory $blogCategory)
    {
        $page = trans('pages_names.edit_blog_category');
        $main_menu = 'blog-category';
        $sub_menu = '';
        $item = $blogCategory;

        return view('admin.blog-category.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $updated_params = $request->all();
        $updated_params['category_slug'] = Str::slug($updated_params['category_name']);

        $blogCategory->update($updated_params);

        $message = trans('succes_messages.blog_category_updated_succesfully');

        return redirect('blog-category')->with('success', $message);
    }

    public function delete(BlogCategory $blogCategory)
    {
        $blogCategory->delete();

        $message = trans('succes_messages.blog_category_deleted_succesfully');
        return redirect('blog-category')->with('success', $message);
    }
}
