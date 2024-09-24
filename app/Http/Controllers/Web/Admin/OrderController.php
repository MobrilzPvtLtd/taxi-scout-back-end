<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Http\Requests\Admin\Order\CreateOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Models\Admin\Invoice;
use App\Models\Admin\Order;
use App\Models\Admin\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
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

    public function upgrade(Request $request, Order $order)
    {
        $page = trans('pages_names.edit_order');
        $main_menu = 'manage-order';
        $sub_menu = '';

        $item = $order;

        return view('admin.order.upgrade', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

    public function packageShow(Request $request)
    {
        $package = Subscription::where('id', $request->package_id)->first();
        // dd($package);
        return response()->json($package);
    }

    public function packageUpgrade(Request $request)
    {
        $subscription = Subscription::where('id', $request->package_id)->first();
        if ($subscription) {
            $start_date = Carbon::now();
            $end_date = (clone $start_date)->addDays($subscription->validity);
        }

        $order = $this->order->where('id', $request->order_id)->first();
        $order->active = 1;
        $order->package_id = $request->package_id;
        $order->start_date = $start_date;
        $order->end_date = $end_date;
        $order->save();

        $invoice = new Invoice();
        $invoice->user_id = auth()->user()->id;
        $invoice->order_id = $request->order_id;
        $invoice->package_id = $request->package_id;
        $invoice->transaction_id = $request->transaction_id;
        $invoice->amount = $request->package_amount;
        $invoice->description = $request->description;
        $invoice->payment_method = $request->payment_method;
        $invoice->status = "unpaid";
        $invoice->save();

        // $message = trans('succes_messages.order_updated_succesfully');

        return redirect()->route('order.invoice', $invoice->id);
    }

    public function invoice(Request $request)
    {
        $page = trans('pages_names.edit_order');
        $main_menu = 'invoice';
        $sub_menu = '';

        if (access()->hasRole(RoleSlug::SUPER_ADMIN)) {
            $invoices = Invoice::get();
        } else {
            $invoices = Invoice::where('user_id', auth()->user()->id)->get();
        }

        return view('admin.order.invoice', compact('invoices', 'page', 'main_menu', 'sub_menu'));
    }

    public function orderInvoice(Request $request, $id)
    {
        $page = trans('pages_names.edit_order');
        $main_menu = 'manage-order';
        $sub_menu = '';

        $item = Invoice::find($id);
        return view('admin.order.invoice-show', compact('item', 'page', 'main_menu', 'sub_menu'));
    }

}
