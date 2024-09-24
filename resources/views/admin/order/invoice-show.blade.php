@extends('admin.layouts.app')
@section('title', 'Order Invoice Show')

@section('content')
    {{-- {{session()->get('errors')}} --}}

    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{!! asset('assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') !!}">

    <!-- Start Page content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <a href="{{ url('order') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form method="post" class="form-horizontal" action="{{ route('paypal.payment') }}">
                                @csrf
                                <input type="hidden" name="invoice_id" value="{{ $item->id }}">
                                <input type="hidden" name="trial_days" value="{{ $item->subscription->validity }}">
                                <input type="hidden" name="start_date" value="{{ $item->order->start_date }}">
                                <input type="hidden" name="end_date" value="{{ $item->order->end_date }}">

                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>@lang('view_pages.description')</th>
                                                    <th>@lang('view_pages.price')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="hidden" value="{{ $item->description }}" name="description" id="description" class="form-control">
                                                        <span id="descriptionText">{{ $item->description }}</span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" value="{{ $item->amount }}" name="amount" id="amount" class="form-control">
                                                        <span id="packageAmountText">$ {{ $item->amount }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method<span class="text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method" class="form-control" required>
                                            <option value="">-- Select a Payment Method --</option>
                                            <option value="paypal" selected>Paypal</option>
                                        </select>
                                        <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm pull-right m-5" type="submit">
                                            Pay with PayPal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
