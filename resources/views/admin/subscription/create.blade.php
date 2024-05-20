@extends('admin.layouts.app')
@section('title', 'Main page')

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
                            <a href="{{ url('subscription') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form method="post" class="form-horizontal" action="{{ url('subscription/store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="package_name">@lang('view_pages.package_name') <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="package_name" name="package_name"
                                                value="{{ old('package_name') }}" required
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.package_name')">
                                            <span class="text-danger">{{ $errors->first('package_name') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="number_of_drivers">@lang('view_pages.number_of_drivers') <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="number_of_drivers"
                                                name="number_of_drivers" value="{{ old('number_of_drivers') }}"
                                                required placeholder="@lang('view_pages.enter') @lang('view_pages.number_of_drivers')">
                                            <span class="text-danger">{{ $errors->first('number_of_drivers') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="amount">@lang('view_pages.amount') </label>
                                            <input class="form-control" type="text" id="amount"
                                                name="amount" value="{{ old('amount') }}"
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.amount')">
                                            <span class="text-danger">{{ $errors->first('amount') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="validity">@lang('view_pages.validity') (In Days)<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="validity"
                                                name="validity" value="{{ old('validity') }}"
                                                required="" placeholder="@lang('view_pages.enter') @lang('view_pages.validity')">
                                            <span class="text-danger">{{ $errors->first('validity') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="active">@lang('view_pages.status')
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="active" id="active" class="form-control">
                                                <option value="1" {{ old('active') == 1 ? 'selected' : '' }} >Active</option>
                                                <option value="0" {{ old('active') == 0 ? 'selected' : '' }} >Inactive</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm pull-right m-5" type="submit">
                                            @lang('view_pages.save')
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
    <!-- container -->
    </div>
    <!-- content -->


    <script src="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}">
    </script>

    <script>
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            startDate: 'today'
        });
    </script>
@endsection
