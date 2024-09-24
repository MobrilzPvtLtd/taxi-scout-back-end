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
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction Id</th>
                                        <th>@lang('view_pages.description')</th>
                                        <th>@lang('view_pages.price')</th>
                                        <th>@lang('view_pages.payment_method')</th>
                                        <th>@lang('view_pages.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ $invoice->transaction_id }}
                                        </td>
                                        <td>
                                            {{ $invoice->description }}
                                        </td>
                                        <td>
                                            $ {{ $invoice->amount }}
                                        </td>
                                        <td>
                                            {{ $invoice->payment_method }}
                                        </td>
                                        @if($invoice->status == "paid")
                                            <td><span class="label label-success">Paid</span></td>
                                        @else
                                            <td><span class="label label-warning">Unpaid</span></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
