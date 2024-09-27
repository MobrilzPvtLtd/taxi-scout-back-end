<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\Gallery;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{
    protected $gallery;

    /**
     * GalleryController constructor.
     *
     * @param \App\Models\Admin\Gallery $gallery
     */
    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function index()
    {
        $page = trans('pages_names.view_gallery');

        $main_menu = 'manage-store-front';
        $sub_menu = 'gallery';

        return view('admin.gallery.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->gallery->query();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.gallery._gallery', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_gallery');
        $main_menu = 'manage-store-front';
        $sub_menu = 'gallery';

        return view('admin.gallery.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(Request $request)
    {
        $created_params = $request->only(['image','active']);
        $created_params['active'] = 1;

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('gallery'), $imageName);
            $created_params['image'] = $imageName;
        }

        $this->gallery->create($created_params);

        $message = trans('Gallery added succesfully.');

        return redirect('galleries')->with('success', $message);
    }

    public function getById(Gallery $gallery)
    {
        $page = trans('pages_names.edit_gallery');
        $main_menu = 'manage-store-front';
        $sub_menu = 'gallery';
        $item = $gallery;

        return view('admin.gallery.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $updated_params = $request->only(['image','active']);
        $updated_params['active'] = 1;

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('gallery'), $imageName);
            $updated_params['image'] = $imageName;
        }

        $gallery->update($updated_params);
        $message = trans('Gallery updated succesfully.');
        return redirect('galleries')->with('success', $message);
    }

    public function toggleStatus(Gallery $gallery)
    {
        $status = $gallery->active ? false: true;
        $gallery->update(['active' => $status]);

        $message = trans('Gallery status changed succesfully.');
        return redirect('galleries')->with('success', $message);
    }

    public function delete(Gallery $gallery)
    {
        $gallery->delete();

        $message = trans('Gallery deleted succesfully.');
        return redirect('galleries')->with('success', $message);
    }
}
