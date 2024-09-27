<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\OurTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Base\Services\ImageUploader\ImageUploaderContract;

class OurTeamController extends BaseController
{
    protected $team;
      /**
     * The
     *
     * @var App\Base\Services\ImageUploader\ImageUploaderContract
     */
    protected $imageUploader;

    /**
     * OurTeamController constructor.
     *
     * @param \App\Models\Admin\OurTeam $team
     */
    public function __construct(OurTeam $team, ImageUploaderContract $imageUploader,)
    {
        $this->team = $team;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        $page = trans('pages_names.our-team');

        $main_menu = 'manage-our-team';
        $sub_menu = 'our-team';

        return view('admin.our-team.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->team->query();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.our-team._our-team', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_our_team');
        $main_menu = 'manage-our-team';
        $sub_menu = 'our-team';

        return view('admin.our-team.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(Request $request)
    {
        $created_params = $request->only(['title','name','mobile','email','image','description','status']);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('team'), $imageName);
            $created_params['image'] = $imageName;
        }

        $this->team->create($created_params);

        $message = trans('Our Team added succesfully.');

        return redirect('our-team')->with('success', $message);
    }

    public function getById(OurTeam $team)
    {
        $page = trans('pages_names.our-team');
        $main_menu = 'manage-our-team';
        $sub_menu = 'our-team';
        $item = $team;

        return view('admin.our-team.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(Request $request, OurTeam $team)
    {
        $updated_params = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time().'.'.request()->image->getClientOriginalName();
            request()->image->move(public_path('team'), $imageName);
            $updated_params['image'] = $imageName;
        }

        $team->update($updated_params);

        $message = trans('Our Team updated succesfully.');

        return redirect('our-team')->with('success', $message);
    }

    public function delete(OurTeam $team)
    {
        $team->delete();

        $message = trans('Our Team deleted succesfully.');
        return redirect('our-team')->with('success', $message);
    }
}
