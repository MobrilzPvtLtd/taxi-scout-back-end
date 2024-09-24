<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Invoice;
use App\Models\Admin\Order;
use App\Models\Admin\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function createPayment(Request $request)
    {
        $email = auth()->user()->email;
        $name = auth()->user()->name;

        $invoice_id = $request->invoice_id;
        $description = $request->description;
        $amount = $request->amount;
        $trialDays = $request->trial_days;
        $end_date = $request->end_date;

        $start_date = date('Y-m-d', strtotime(' + 1 day'));
        // dd($start_date);

        $provider = new PayPalClient;

        $provider->getAccessToken();

        $response = $provider->addProduct($description, $description, 'SERVICE', 'SOFTWARE')
                ->addCustomPlan($description, $description, $amount, 'DAY', $trialDays)
                ->setReturnAndCancelUrl(route('payment.success', ['invoice_id' => $invoice_id]), route('payment.cancel'))
                ->setupSubscription($name, $email, $start_date);
        // dd($response);
        if (is_array($response)) {
            if (isset($response['status']) && $response['status'] === 'APPROVAL_PENDING') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        // $link = $link['href'] . '&amount=' . $amount . '&invoice_id=' . $invoice_id;
                        // dd($link);
                        return redirect($link['href']);
                    }
                }
            }
        }

        return redirect()->route('payment.cancel')->with('error', 'Failed to create payment.');
    }

    public function paymentSuccess(Request $request)
    {
        $page = trans('pages_names.edit_order');
        $main_menu = 'manage-order';
        $sub_menu = '';

        // dd($request);
        $subscriptionId = $request->query('subscription_id');
        $baToken = $request->query('ba_token');
        $token = $request->query('token');

        $provider = new PayPalClient;

        $provider->getAccessToken();

        $response = $provider->showSubscriptionDetails($subscriptionId);

        // dd($response);
        if (is_array($response)) {

            if (isset($response['status']) && $response['status'] === 'ACTIVE') {
                $invoice = Invoice::find($request->invoice_id);
                $invoice->transaction_id = md5($subscriptionId);
                $invoice->subscription_id = $subscriptionId;
                $invoice->payment_method = "paypal";
                $invoice->status = "paid";
                $invoice->save();

                if($subscriptionId != $invoice->subscription_id){
                    $subscription = Subscription::where('id', $invoice->package_id)->first();
                    if ($subscription) {
                        $start_date = Carbon::now();
                        $end_date = (clone $start_date)->addDays($subscription->validity);
                    }

                    $order = Order::where('id', $invoice->order_id)->first();
                    $order->active = 1;
                    $order->package_id = $invoice->package_id;
                    $order->start_date = $start_date;
                    $order->end_date = $end_date;
                    $order->save();
                }

                return view('admin.order.payment-success', compact('response', 'invoice', 'page', 'main_menu', 'sub_menu'));
            } else {
                return redirect()->route('payment.cancel');

            }
        }

        return redirect()->route('payment.cancel');
    }

    public function paymentCancel()
    {
        $page = trans('pages_names.edit_order');
        $main_menu = 'manage-order';
        $sub_menu = '';

        $message = "Payment canceled!";
        return view('admin.order.payment-failure', compact('message','page', 'main_menu', 'sub_menu'));
    }
}
