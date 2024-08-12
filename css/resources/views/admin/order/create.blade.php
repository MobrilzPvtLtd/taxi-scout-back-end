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
                            <a href="{{ url('order') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form method="post" class="form-horizontal" action="{{ url('order/store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="package_id">@lang('view_pages.package_name') <span class="text-danger">*</span></label>
                                            <select name="package_id" id="package_id" class="form-control">
                                                @foreach (App\Models\Admin\Subscription::get() as $package)
                                                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }} >{{ $package->package_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('package_id') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="user_id">Users <span class="text-danger">*</span></label>
                                            <select name="user_id" id="user_id" class="form-control">
                                                @foreach (App\Models\Admin\AdminDetail::whereHas('user')->get() as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->user->id ? 'selected' : '' }} >{{ $user->user->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="start_date">@lang('view_pages.start_date') </label>
                                            <input class="form-control" type="date" id="start_date"
                                                name="start_date" value="{{ old('start_date') }}">
                                            <span class="text-danger">{{ $errors->first('start_date') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="end_date">End Date<span class="text-danger">*</span></label>
                                            <input class="form-control" type="date" id="end_date"
                                                name="end_date" value="{{ old('end_date') }}"
                                                required="">{{ $errors->first('end_date') }}</span>
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
