@extends('admin.layouts.app')
@section('title', 'Order Invoice')

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
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                @if(auth()->user()->id == 1)
                                                    <th>Taxi Company</th>
                                                @endif
                                                <th>@lang('view_pages.description')</th>
                                                <th>@lang('view_pages.price')</th>
                                                <th>@lang('view_pages.status')</th>
                                                <th>@lang('view_pages.payment_method')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $key => $result)
                                                @php
                                                    $owner = App\Models\Admin\Owner::whereHas('user', function ($query) use   ($result) {
                                                        $query->where('user_id', $result->user_id);
                                                    })->first();
                                                @endphp
                                                <tr>
                                                    @if(auth()->user()->id == 1)
                                                        <td> {{$owner->name}}</td>
                                                    @endif
                                                    <td>{{ $result->description }}</td>
                                                    <td>{{ $result->amount }}</td>
                                                    @if($result->status == "paid")
                                                        <td><span class="label label-success">Paid</span></td>
                                                    @else
                                                        <td><span class="label label-danger">Unpaid</span></td>
                                                    @endif
                                                    <td>{{ $result->payment_method }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
