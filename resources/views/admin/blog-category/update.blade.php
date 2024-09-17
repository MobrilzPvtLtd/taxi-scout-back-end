@extends('admin.layouts.app')
@section('title', 'Blog Category Edit')

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

                        <div class="box-header with-blog-category">
                            <a href="{{ url('blog-category') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form method="post" class="form-horizontal" action="{{ url('blog-category/update', $item->id) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="category_name">Category Name<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="category_name"
                                                name="category_name" value="{{ $item->category_name }}"
                                                required="">{{ $errors->first('category_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="status">@lang('view_pages.status')
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{ $item->status == 1 ? 'selected' : '' }} >Active</option>
                                                <option value="0" {{ $item->status == 0 ? 'selected' : '' }} >Inactive</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm pull-right m-5" type="submit">
                                            @lang('view_pages.update')
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
