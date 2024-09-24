@extends('admin.layouts.app')
@section('title', 'Order Invoice Show')

@section('content')
    {{session()->get('errors')}}

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
                            <tr>
                                <td colspan="11">
                                    <p id="no_data" class="lead no-data text-center">
                                        <img src="{{asset('assets/img/dark-data.svg')}}" style="width:150px;margin-top:25px;margin-bottom:25px;" alt="">
                                        <h4 class="text-center" style="color:#d33838;font-size:25px;">{{ $message }}</h4>
                                    </p>
                                </td>
                            </tr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
