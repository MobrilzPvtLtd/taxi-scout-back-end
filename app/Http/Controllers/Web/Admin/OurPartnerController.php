<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\Partner;
use Illuminate\Http\Request;

class OurPartnerController extends BaseController
{
    protected $partner;

    /**
     * OurPartnerController constructor.
     *
     * @param \App\Models\Admin\Partner $partner
     */
    public function __construct(Partner $partner)
    {
        $this->partner = $partner;
    }

    public function index()
    {
        $page = trans('pages_names.view_partner');

        $main_menu = 'manage-store-front';
        $sub_menu = 'partner';

        return view('admin.partner.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->partner->query();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.partner._partner', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_partner');
        $main_menu = 'manage-store-front';
        $sub_menu = 'partner';

        return view('admin.partner.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(Request $request)
    {
        $created_params = $request->only(['image','active']);
        $created_params['active'] = 1;

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('partner'), $imageName);
            $created_params['image'] = $imageName;
        }

        $this->partner->create($created_params);

        $message = trans('Partner added succesfully.');

        return redirect('our-partner')->with('success', $message);
    }

    public function getById(Partner $partner)
    {
        $page = trans('pages_names.edit_partner');
        $main_menu = 'manage-store-front';
        $sub_menu = 'partner';
        $item = $partner;

        return view('admin.partner.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(Request $request, Partner $partner)
    {
        $updated_params = $request->only(['image','active']);
        $updated_params['active'] = 1;

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('partner'), $imageName);
            $updated_params['image'] = $imageName;
        }

        $partner->update($updated_params);
        $message = trans('Partner updated succesfully.');
        return redirect('our-partner')->with('success', $message);
    }

    public function toggleStatus(Partner $partner)
    {
        $status = $partner->active ? false: true;
        $partner->update(['active' => $status]);

        $message = trans('Partner status changed succesfully.');
        return redirect('our-partner')->with('success', $message);
    }

    public function delete(Partner $partner)
    {
        $partner->delete();

        $message = trans('Partner deleted succesfully.');
        return redirect('our-partner')->with('success', $message);
    }
}
