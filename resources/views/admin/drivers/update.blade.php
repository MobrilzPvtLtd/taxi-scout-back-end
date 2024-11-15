@extends('admin.layouts.app')
@section('title', 'Main page')


@section('content')

    <!-- Start Page content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="box">

                        <div class="box-header with-border">
                            <a href="{{ url('drivers') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">

                            <form method="post" class="form-horizontal" action="{{ url('drivers/update', $item->id) }}"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="admin_id">@lang('view_pages.select_area')
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="service_location_id" id="service_location_id" class="form-control"
                                                onchange="getypesAndCompanys()" required>
                                                <option value="" selected disabled>@lang('view_pages.select_area')</option>
                                                @foreach ($services as $key => $service)
                                                    <option value="{{ $service->id }}"
                                                        {{ old('service_location_id', $item->service_location_id) == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">@lang('view_pages.name') <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="name" name="name"
                                                value="{{ old('name', $item->name) }}" required=""
                                                placeholder="@lang('view_pages.enter_name')">
                                            <span class="text-danger">{{ $errors->first('name') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-6">
                                    @if(env('APP_FOR')=='demo')
                                        <div class="form-group">
                                            <label for="name">@lang('view_pages.mobile') <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="mobile" name="mobile"
                                                value="{{ old('mobile', "********") }}" required=""
                                                placeholder="@lang('view_pages.enter_mobile')">
                                            <span class="text-danger">{{ $errors->first('mobile') }}</span>

                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="name">@lang('view_pages.mobile') <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="mobile" name="mobile"
                                                value="{{ old('mobile', $item->mobile) }}" required=""
                                                placeholder="@lang('view_pages.enter_mobile')">
                                            <span class="text-danger">{{ $errors->first('mobile') }}</span>

                                        </div>
                                    @endif
                                    </div>

                                    <div class="col-sm-6">
                                        @if(env('APP_FOR')=='demo')
                                            <div class="form-group">
                                                <label for="email">@lang('view_pages.email') <span class="text-danger">*</span></label>
                                                <input class="form-control" type="email" id="email" name="email"
                                                    value="{{ old('email', "******************") }}" required=""
                                                    placeholder="@lang('view_pages.enter_email')">
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <label for="email">@lang('view_pages.email') <span class="text-danger">*</span></label>
                                                <input class="form-control" type="email" id="email" name="email"
                                                    value="{{ old('email', $item->email) }}" required=""
                                                    placeholder="@lang('view_pages.enter_email')">
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row">
                                    {{-- <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">@lang('view_pages.transport_type') <span class="text-danger">*</span></label>
                                            <select name="transport_type" id="transport_type" class="form-control" required>
                                                <option value="" selected disabled>@lang('view_pages.select')</option>
                                                <option value="taxi" {{ old('transport_type', $item->transport_type) == 'taxi' ? 'selected' : '' }}>Taxi
                                                </option>
                                                <option value="delivery" {{ old('transport_type',$item->transport_type) == 'delivery' ? 'selected' : '' }}>@lang('view_pages.delivery')
                                                </option>
                                                <option value="both" {{ old('transport_type',$item->transport_type) == 'both' ? 'selected' : '' }}>@lang('view_pages.both')
                                                </option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('transport_type') }}</span>
                                        </div>
                                    </div> --}}
                                    @if(auth()->user()->id == 1)
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Taxi Company<span class="text-danger">*</span></label>
                                                <select name="owner_id" id="owner_id" class="form-control" required>
                                                    <option value="" selected disabled>Select Taxi Company</option>
                                                    @foreach ($admin as $company)
                                                        <option value="{{ $company->owner_unique_id }}" {{ $company->owner_unique_id == $item->owner_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('owner_id') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- @php
                                        dd($item->vehicle_type);
                                    @endphp --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="type">Assign Taxi
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="vehicle_type" id="type" class="form-control select2" required>
                                                @foreach($types as $key=>$type)
                                                        <option value="{{ $type->id }}"

                                                            {{ old('vehicle_type', $item->driverVehicleTypeDetail()->Where('vehicle_type', $type->id)->pluck('vehicle_type')->first()) ? 'selected' : '' }}
                                                            >
                                                        {{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                               </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="car_make">@lang('view_pages.car_make')<span
                                                    class="text-danger">*</span></label>
                                            <select name="car_make" id="car_make" class="form-control select2" required>
                                                <option value="" selected disabled>@lang('view_pages.select')</option>
                                                @foreach ($carmake as $key => $make)
                                                    <option value="{{ $make->id }}"
                                                        {{ old('car_make', $item->car_make) == $make->id ? 'selected' : '' }}>
                                                        {{ $make->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                            <label for="car_model">@lang('view_pages.car_model')<span
                                                    class="text-danger">*</span></label>
                                            <select name="car_model" id="car_model" class="form-control select2" required>
                                                <option value="" selected disabled>@lang('view_pages.select')</option>
                                                @foreach ($carmodel as $key => $model)
                                                    <option value="{{ $model->id }}"
                                                        {{ old('car_model', $item->car_model) == $model->id ? 'selected' : '' }}>
                                                        {{ $model->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="driving_license">License Number<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="driving_license" name="driving_license" value="{{old('driving_license',$item->driving_license)}}" required="" placeholder="@lang('view_pages.enter') License Number">
                                            <span class="text-danger">{{ $errors->first('driving_license') }}</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="car_color">@lang('view_pages.car_color') <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="car_color" name="car_color"
                                                value="{{ old('car_color', $item->car_color) }}" required=""
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.car_color')">
                                            <span class="text-danger">{{ $errors->first('car_color') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="car_number">@lang('view_pages.car_number') <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="car_number" name="car_number"
                                                value="{{ old('car_number', $item->car_number) }}" required=""
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.car_number')">
                                            <span class="text-danger">{{ $errors->first('car_number') }}</span>
                                        </div>
                                    </div>
                                </div> --}}
                                    <div class="form-group">
                                    <div class="col-6">
                                        <label for="profile_picture">@lang('view_pages.profile')</label><br>
                                        <img class="user-image" id="blah" src="{{asset( $item->user->profile_picture) }}" alt=" "><br>
                                        <input type="file" id="icon" onchange="readURL(this)" name="profile_picture"
                                            style="display:none">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="$('#icon').click()"
                                            id="upload">@lang('view_pages.browse')</button>
                                        <button class="btn btn-danger btn-sm" type="button" id="remove_img"
                                            style="display: none;">@lang('view_pages.remove')</button><br>
                                        <span class="text-danger">{{ $errors->first('icon') }}</span>
                                    </div>
                                </div>

                                </div>
                                <div class="form-group">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm pull-right" type="submit">
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
    <!-- jQuery 3 -->
    <script src="{{ asset('assets/vendor_components/jquery/dist/jquery.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        // Retrieve the initial selected owner_id value
        var initialOwnerId = $('#owner_id').val();

        // Perform an initial request to get the corresponding types based on the owner_id value
        getTypesByOwnerId(initialOwnerId);

        // On change event of owner_id select
        $(document).on('change', '#owner_id', function() {
            var owner_id = $(this).val();

            // Call the function to get the types based on the selected owner_id
            getTypesByOwnerId(owner_id);
        });

        // Function to get types based on the transport_type
        function getTypesByOwnerId(owner_id) {
            $.ajax({
                url: "{{ route('getType') }}",
                type: 'GET',
                data: {
                    // 'transport_type': transportType,
                    'owner_id': owner_id,
                },
                success: function(result) {
                    var selectedTypes = [];

                    // Get the selected type values from the type select element
                    $('#type').find('option:selected').each(function() {
                        selectedTypes.push($(this).val());
                    });

                    $('#type').empty();

                    result.forEach(element => {
                        var option = $('<option>').val(element.id).text(element.name);

                        // Check if the type value is in the selectedTypes array
                        if (selectedTypes.includes(element.id.toString())) {
                            option.attr('selected', 'selected');
                        }

                        $('#type').append(option);
                    });

                    $('#type').select2();
                }
            });
        }
    });


    $('.select2').select2({
        placeholder : "Select ...",
    });
        $('#is_company_driver').change(function() {
            var value = $(this).val();
            if (value == 1) {
                $('#companyShow').show();
            } else {
                $('#companyShow').hide();
            }
        });

        function getypesAndCompanys() {

            var admin_id = document.getElementById('admin_id').value;
            var ajaxPath = "<?php echo url('types/by/admin'); ?>";
            var ajaxCompanyPath = "<?php echo url('company/by/admin'); ?>";

            $.ajax({
                url: ajaxPath,
                type: 'GET',
                data: {
                    'admin_id': admin_id,
                },
                success: function(result) {
                    $('#type').empty();

                    $("#type").append('<option value="">Select Type</option>');

                    for (var i = 0; i < result.data.length; i++) {
                        console.log(result.data[i]);
                        $("#type").append('<option  class="left" value="' + result.data[i].id +
                            '" data-icon="' + result.data[i].icon + '"  >' + result.data[i].name +
                            '</option>');
                    }

                    $('#type').select();
                },
                error: function() {

                }
            });

            $.ajax({
                url: ajaxCompanyPath,
                type: 'GET',
                data: {
                    'admin_id': admin_id,
                },
                success: function(result) {
                    $('#company').empty();

                    $("#company").append('<option value="">Select Company</option>');
                    $("#company").append('<option value="0">Individual</option>');

                    for (var i = 0; i < result.data.length; i++) {
                        console.log(result.data[i]);
                        $("#company").append('<option  class="left" value="' + result.data[i].id + '" >' +
                            result.data[i].name + '</option>');
                    }

                    $('#company').select();
                },
                error: function() {

                }
            });
        }
        $(document).on('change', '#owner_id', function() {
            let value = $(this).val();

            $.ajax({
                url: "{{ route('getType') }}",
                type: 'GET',
                data: {
                    'owner_id': value,
                },
                success: function(result) {
                    $('#type').empty();
                    // $("#type").append('<option value="" selected disabled>Select</option>');
                    result.forEach(element => {
                        $("#type").append('<option value=' + element.id + '>' + element
                            .name + '</option>')
                    });
                    $('#type').select();
                }
            });
        });
        $(document).on('change', '#type', function() {
            let value = $(this).val();

            $.ajax({
                url: "{{ route('getCarMake') }}",
                type: 'GET',
                data: {
                    'type': value,
                },
                success: function(result) {
                    $('#car_make').empty();
                    $("#car_make").append('<option value="" selected disabled>Select</option>');
                    result.forEach(element => {
                        $("#car_make").append('<option value=' + element.id + '>' + element
                            .name + '</option>')
                    });
                    $('#car_make').select();
                }
            });
        });

        $(document).on('change', '#car_make', function() {
            let value = $(this).val();

            $.ajax({
                url: "{{ route('getCarModel') }}",
                type: 'GET',
                data: {
                    'car_make': value,
                },
                success: function(result) {
                    $('#car_model').empty();
                    $("#car_model").append('<option value="" selected disabled>Select</option>');
                    result.forEach(element => {
                        $("#car_model").append('<option value=' + element.id + '>' + element
                            .name + '</option>')
                    });
                    $('#car_model').select();
                }
            });
        });

    </script>

@endsection
