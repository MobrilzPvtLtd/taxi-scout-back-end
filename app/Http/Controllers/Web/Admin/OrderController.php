<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Http\Requests\Admin\Order\CreateOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Models\Admin\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $order;

    /**
     * OrderController constructor.
     *
     * @param \App\Models\Admin\Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function index()
    {

        $page = trans('pages_names.order');

        $main_menu = 'manage-order';
        $sub_menu = '';

        return view('admin.order.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->order->leftJoin('users', 'orders.user_id', '=', 'users.id')
                        ->leftJoin('subscriptions', 'orders.package_id', '=', 'subscriptions.id')
                        ->select('orders.*','users.name','subscriptions.package_name');
        if(!access()->hasRole(RoleSlug::SUPER_ADMIN)){
            $query = $query->where('orders.user_id', auth()->user()->id);
        }
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.order._order', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_order');
        $main_menu = 'manage-order';
        $sub_menu = '';

        return view('admin.order.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(CreateOrderRequest $request)
    {
        $created_params = $request->only(['package_id','user_id','start_date','end_date','active']);

        $this->order->create($created_params);

        $message = trans('succes_messages.order_added_succesfully');

        return redirect('order')->with('success', $message);
    }

    public function getById(Order $order)
    {
        $page = trans('pages_names.edit_order');
        $main_menu = 'manage-order';
        $sub_menu = '';
        $item = $order;

        return view('admin.order.update', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $updated_params = $request->all();
        $order->update($updated_params);

        $message = trans('succes_messages.order_updated_succesfully');

        return redirect('order')->with('success', $message);
    }

    public function delete(Order $order)
    {
        $order->delete();

        $message = trans('succes_messages.order_deleted_succesfully');
        return redirect('order')->with('success', $message);
    }
}
