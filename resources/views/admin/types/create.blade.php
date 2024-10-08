@extends('admin.layouts.app')


@section('title', 'Main page')

<!-- Bootstrap fileupload css -->
@section('content')
    <!-- Start Page content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="box">

                        <div class="box-header with-border">
                            <a href="{{ url('types') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form id="type-form" role="form" method="post" class="form-horizontal"
                                action="{{ url('types/store') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row">
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                        <label for="service_location_id">@lang('view_pages.select_area')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="service_location_id" id="service_location_id" class="form-control" required>
                                            <option value="" >@lang('view_pages.select_area')</option>
                                            @foreach ($services as $key => $service)
                                            <option value="{{$service->id}}" {{ old('service_location_id') == $service->id ? 'selected' : '' }}>{{$service->name}}</option>
                                            @endforeach
                                        </select>
                                        </div>
                                    </div> --}}

                                    @if(auth()->user()->id == 1)
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Taxi Company<span class="text-danger">*</span></label>
                                                <select name="owner_id" id="owner_id" class="form-control" required>
                                                    <option value="" selected disabled>Select Taxi Company</option>
                                                    @foreach ($admin as $company)
                                                        <option value="{{ $company->owner_unique_id }}">{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('transport_type') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">@lang('view_pages.transport_type') <span
                                                    class="text-danger">*</span></label>
                                            <select name="transport_type" id="transport_type" class="form-control"
                                                required>
                                                <option value="" selected disabled>@lang('view_pages.select_transport_type')</option>
                                                <option value="taxi" {{ 'taxi' }}>Taxi</option>
                                                {{-- <option value="delivery" {{ 'delivery' }}>@lang('view_pages.delivery')</option> --}}
                                                {{-- <option value="both" {{ 'both' }}>@lang('view_pages.both')</option> --}}
                                            </select>
                                            <span class="text-danger">{{ $errors->first('transport_type') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group m-b-25">
                                            <label for="name">Car’s Name <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="name" name="name"
                                                value="{{ old('name') }}" required="" placeholder="@lang('view_pages.enter_name')">
                                            <span class="text-danger">{{ $errors->first('name') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-6" name="taxi" id="taxi">
                                        <div class="form-group m-b-25">
                                            <label for="name">@lang('view_pages.capacity') <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="number" id="capacity" name="capacity" value="{{ old('capacity') }}"
                                                placeholder="@lang('view_pages.enter_capacity')" min="1">
                                            <span class="text-danger">{{ $errors->first('capacity') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="model_name">Car Model<span class="text-danger">*</span></label>
                                            <select name="model_name" id="model_name" class="form-control select2" required>
                                                @foreach ($carModels as $carModel)
                                                    <option value="{{ $carModel->name }}">{{ $carModel->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('country') }}</span>
                                        </div>
                                    </div>
                                    {{-- <div class="col-6">
                                        <div class="form-group m-b-25">
                                            <label for="price">Per Km Price<span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="price" name="price"
                                                value="{{ old('price') }}" required=""
                                                placeholder="Per Km Price" min="1">
                                            <span class="text-danger">{{ $errors->first('price') }}</span>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-6" name="delivery" id="delivery">
                                        <div class="form-group m-b-25">
                                            <label for="maximum_weight_can_carry">@lang('view_pages.maximum_weight_can_carry') <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="maximum_weight_can_carry"
                                                name="maximum_weight_can_carry" value="{{ old('capacity') }}"
                                                required="" placeholder="@lang('view_pages.enter_maximum_weight_can_carry')" min="1">
                                            <span class="text-danger">{{ $errors->first('capacity') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6" name="delivery_1" id="delivery_1">
                                        <div class="form-group m-b-25">
                                            <label for="name">@lang('view_pages.size') <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="size" name="size"
                                                value="{{ old('size') }}" required="" placeholder="@lang('view_pages.enter_size')"
                                                min="1">
                                            <span class="text-danger">{{ $errors->first('size') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group m-b-25">
                                            <label for="short_description">@lang('view_pages.short_description') <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="name"
                                                name="short_description" value="{{ old('short_description') }}"
                                                required="" placeholder="@lang('view_pages.enter_short_description')">
                                            <span class="text-danger">{{ $errors->first('short_description') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group m-b-25">
                                            <label for="description">@lang('view_pages.description') <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="description" id="description" class="form-control" placeholder="@lang('view_pages.enter_description')"></textarea>

                                            <span class="text-danger">{{ $errors->first('description') }}</span>

                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-6">
                                        <div class="form-group m-b-25">
                                            <label for="supported_vehicles">@lang('view_pages.supported_vehicles') <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="supported_vehicles" id="supported_vehicles" class="form-control"
                                                placeholder="Example: Toyato,Audi,Acura"></textarea>

                                            <span class="text-danger">{{ $errors->first('supported_vehicles') }}</span>

                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                            <label for="">@lang('view_pages.icon_types_for') <span
                                                    class="text-danger">*</span></label>
                                            <select name="icon_types_for" id="icon_types_for" class="form-control"
                                                required>
                                                <option value="" selected disabled>@lang('view_pages.select')</option>
                                                <option value="taxi"
                                                    {{ old('icon_types_for') == 'taxi' ? 'selected' : '' }}>
                                                    @lang('view_pages.taxi')</option>
                                                <option value="auto"
                                                    {{ old('icon_types_for') == 'auro' ? 'selected' : '' }}>
                                                    @lang('view_pages.auto')</option>
                                                <option value="truck"
                                                    {{ old('icon_types_for') == 'truck' ? 'selected' : '' }}>
                                                    @lang('view_pages.truck')</option>
                                                <option value="motor_bike"
                                                    {{ old('icon_types_for') == 'motor_bike' ? 'selected' : '' }}>
                                                    @lang('view_pages.motor_bike')</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('icon_types_for') }}</span>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                            <label for="">@lang('view_pages.trip_dispatch_type') <span
                                                    class="text-danger">*</span></label>
                                            <select name="trip_dispatch_type" id="trip_dispatch_type"
                                                class="form-control" required>
                                                <option value="" selected disabled>@lang('view_pages.select')</option>
                                                <option value="bidding"
                                                    {{ old('trip_dispatch_type') == 'bidding' ? 'selected' : '' }}>
                                                    @lang('view_pages.bidding')</option>
                                                <option value="normal"
                                                    {{ old('trip_dispatch_type') == 'normal' ? 'selected' : '' }}>
                                                    @lang('view_pages.normal')</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('trip_dispatch_type') }}</span>
                                        </div>
                                    </div> --}}

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Smoking <span class="text-danger">*</span></label>
                                            <select name="smoking" id="smoking"  class="form-control" required>
                                                <option value="" selected disabled>Select</option>
                                                <option value="1" {{ old('smoking') == '1' ? 'selected' : '' }}>Yes</option>
                                                <option value="0" {{ old('smoking') == '0' ? 'selected' : '' }}>No</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('smoking') }}</span>
                                        </div>
                                    </div>

                                     <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Pets <span class="text-danger">*</span></label>
                                            <select name="pets" id="pets" class="form-control" required>
                                                <option value="" selected disabled>Select</option>
                                                <option value="1" {{ old('pets') == '1' ? 'selected' : '' }}>Yes</option>
                                                <option
                                                    value="0" {{ old('pets') == '0' ? 'selected' : '' }}>No</option>
                                                 </select>
                                            <span class="text-danger">{{ $errors->first('pets') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Drinking <span class="text-danger">*</span></label>
                                            <select name="drinking" id="drinking" class="form-control" required>
                                                <option value="" selected disabled>Select</option>
                                                <option
                                                    value="1" {{ old('drinking') == '1' ? 'selected' : '' }}>Yes</option>
                                                <option
                                                    value="0" {{ old('drinking') == '0' ? 'selected' : '' }}>No</option>
                                                 </select>
                                            <span class="text-danger">{{ $errors->first('drinking') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Handicaped <span class="text-danger">*</span></label>
                                            <select name="handicaped" id="handicaped" class="form-control" required>
                                                <option value="" selected disabled>Select</option>
                                                <option value="1" {{ old('handicaped') == '1' ? 'selected' : '' }}>Yes</option>
                                                <option
                                                    value="0" {{ old('handicaped') == '0' ? 'selected' : '' }}>No</option>
                                                 </select>
                                            <span class="text-danger">{{ $errors->first('handicaped') }}</span>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-6">
                                        <label for="icon">@lang('view_pages.icon')</label><br>
                                        <img id="blah" src="#" alt=""><br>
                                        <input type="file" id="icon" onchange="readURL(this)" name="icon"
                                            style="display:none">
                                        <button class="btn btn-primary btn-sm" type="button"
                                            onclick="$('#icon').click()" id="upload">@lang('view_pages.browse')</button>
                                        <button class="btn btn-danger btn-sm" type="button" id="remove_img"
                                            style="display: none;">@lang('view_pages.remove')</button><br>
                                        <span class="text-danger">{{ $errors->first('icon') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm m-5 pull-right" type="submit">
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

    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>

    {!! JsValidator::formRequest('App\Http\Requests\Admin\VehicleTypes\CreateVehicleTypeRequest', '#type-form') !!}

    <!-- Bootstrap fileupload js -->
    <script>
        $(document).ready(function() {
            $('#transport_type').on('change', function(e) {
                var selected = $(e.target).val();
                console.log(selected);

                if (selected == 'taxi') {
                    $("#taxi").show();
                    $("#delivery").hide();
                    $("#delivery_1").hide();

                } else if (selected == 'delivery') {
                    $("#taxi").hide();
                    $("#delivery").show();
                    $("#delivery_1").show();
                } else if (selected == 'both') {
                    $("#taxi").show();
                    $("#delivery").show();
                    $("#delivery_1").show();
                } else {
                    $("#taxi").hide();
                    $("#delivery").show();
                    $("#delivery_1").show();
                }

            });
        });
    </script>

@endsection
