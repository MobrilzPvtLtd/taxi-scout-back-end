<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Invoice;
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
        if (is_array($response)) {

            if (isset($response['status']) && $response['status'] === 'ACTIVE') {
                $invoice = Invoice::find($request->invoice_id);
                $invoice->transaction_id = $subscriptionId;
                $invoice->payment_method = "paypal";
                $invoice->status = "paid";
                $invoice->save();

                return view('admin.order.payment-success', compact('response', 'invoice', 'page', 'main_menu', 'sub_menu'));
                // ['subscription' => $response,'invoice_id' => $request->invoice_id, 'main_menu ' => $main_menu]);
            } else {
                return view('payment.failure', ['message' => 'Subscription is not active.']);
            }
        }

        return view('payment.failure', ['message' => 'Failed to retrieve subscription details.']);
    }

    public function paymentCancel()
    {
        return "Payment canceled!";
    }
}
