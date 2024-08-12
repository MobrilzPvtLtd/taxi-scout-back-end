<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Http\Requests\Admin\Subscription\CreateSubscriptionRequest;
use App\Http\Requests\Admin\Subscription\UpdateSubscriptionRequest;
use App\Models\Admin\ServiceLocation;
use App\Models\Admin\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends BaseController
{
    protected $sub;

    /**
     * SubscriptionController constructor.
     *
     * @param \App\Models\Admin\Subscription $sub
     */
    public function __construct(Subscription $sub)
    {
        $this->sub = $sub;
    }

    public function index()
    {
        $page = trans('pages_names.subscription');

        $main_menu = 'manage-subscription';
        $sub_menu = '';

        return view('admin.subscription.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->sub->query();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.subscription._subscription', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_subscription');
        $main_menu = 'manage-subscription';
        $sub_menu = '';

        $companies = User::where('company_key', '!=', null)->get();

        return view('admin.subscription.create', compact('companies','page', 'main_menu', 'sub_menu'));
    }

    public function store(CreateSubscriptionRequest $request)
    {
        $created_params = $request->only(['package_name','number_of_drivers','amount','validity','active']);

        $this->sub->create($created_params);

        $message = trans('succes_messages.subscription_added_succesfully');

        return redirect('subscription')->with('success', $message);
    }

    public function getById(Subscription $sub)
    {
        $page = trans('pages_names.edit_subscription');
        $main_menu = 'manage-subscription';
        $sub_menu = '';
        $item = $sub;
        return view('admin.subscription.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $sub)
    {
        $updated_params = $request->all();
        $sub->update($updated_params);

        $message = trans('succes_messages.subscription_updated_succesfully');

        return redirect('subscription')->with('success', $message);
    }

    public function delete(Subscription $sub)
    {
        $sub->delete();

        $message = trans('succes_messages.subscription_deleted_succesfully');
        return redirect('subscription')->with('success', $message);
    }
}
