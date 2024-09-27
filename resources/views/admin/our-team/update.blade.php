@extends('admin.layouts.app')
@section('title', 'Our Team Edit')

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
                        <div class="box-header with-our-team">
                            <a href="{{ url('our-team') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form method="post" class="form-horizontal" action="{{ url('our-team/update', $item->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="title">Title<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="title"
                                                name="title" value="{{ $item->title }}"
                                                required="">{{ $errors->first('title') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="name"
                                                name="name" value="{{ $item->name }}"
                                                required="">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mobile">Phone Number<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" id="mobile"
                                                name="mobile" value="{{ $item->mobile }}"
                                                required="">{{ $errors->first('mobile') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="email"
                                                name="email" value="{{ $item->email }}"
                                                required="">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="image">Image</label>
                                            <input class="form-control" type="file" id="image"
                                                name="image" value="{{ old('image') }}">{{ $errors->first('image') }}</span>
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

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="description">description</label>
                                            <textarea class="form-control" name="description" id="" rows="5">{{ $item->description }}</textarea>
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
