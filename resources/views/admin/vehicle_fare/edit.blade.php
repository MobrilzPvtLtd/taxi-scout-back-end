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
                            <a href="{{ url('vehicle_fare') }}">
                                <button class="btn btn-danger btn-sm pull-right" type="submit">
                                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                    @lang('view_pages.back')
                                </button>
                            </a>
                        </div>

                        <div class="col-sm-12">
                            <form method="post" action="{{ url('vehicle_fare/update', $zone_price->id) }}">
                                @csrf
                                <input type="hidden" name="transport_type" value="{{ $zone_price->zoneType->transport_type }}">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="admin_id">@lang('view_pages.select_zone')
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="zone" id="zone" class="form-control" required>
                                                <option value="{{ $zone_price->zoneType->zone->id }}">
                                                    {{ $zone_price->zoneType->zone->name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">@lang('view_pages.transport_type') <span
                                                    class="text-danger">*</span></label>
                                            <select name="transport_type" id="transport_type" class="form-control" required>
                                                <option value="taxi"
                                                    {{ old('transport_type', $zone_price->zoneType->transport_type) == 'taxi' ? 'selected' : '' }}>
                                                    Taxi</option>
                                                <option value="delivery"
                                                    {{ old('transport_type', $zone_price->zoneType->transport_type) == 'delivery' ? 'selected' : '' }}>
                                                    @lang('view_pages.delivery')</option>
                                                <option value="both"
                                                    {{ old('transport_type', $zone_price->zoneType->transport_type) == 'both' ? 'selected' : '' }}>
                                                    @lang('view_pages.both')</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('transport_type') }}</span>
                                        </div>
                                    </div> --}}
                                    @if(auth()->user()->id == 1)
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Taxi Company<span class="text-danger">*</span></label>
                                                <select name="owner_id" id="owner_id" class="form-control" required>
                                                    <option value="{{ $zone_price->owner_id }}">{{ $admin->name }}</option>
                                                </select>
                                                <span class="text-danger">{{ $errors->first('transport_type') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="type">@lang('view_pages.select_type')
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="type" id="type" class="form-control" required>
                                                <option value="{{ $zone_price->zoneType->vehicleType->id }}">
                                                    {{ $zone_price->zoneType->vehicleType->name }}</option>
                                                <!-- Add more options if needed -->
                                            </select>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('type') }}</span>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="payment_type">@lang('view_pages.payment_type')
                                                <span class="text-danger">*</span>
                                            </label>
                                            @php
                                                $credit_card = $cash = $wallet = $debit_card = $contactless = '';
                                            @endphp
                                            @if (old('payment_type'))
                                                @foreach (old('payment_type') as $item)
                                                    @if ($item == 'credit_card')
                                                        @php
                                                            $credit_card = 'selected';
                                                        @endphp
                                                    @elseif($item == 'cash')
                                                        @php
                                                            $cash = 'selected';
                                                        @endphp
                                                    @elseif($item == 'wallet')
                                                        @php
                                                            $wallet = 'selected';
                                                        @endphp
                                                    @elseif($item == 'debit_card')
                                                        @php
                                                            $debit_card = 'selected';
                                                        @endphp
                                                    @elseif($item == 'contactless')
                                                        @php
                                                            $contactless = 'selected';
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            @else
                                                @php
                                                    $paymentType = explode(',', $zone_price->zoneType->payment_type);
                                                @endphp
                                                @foreach ($paymentType as $val)
                                                    @if ($val == 'credit_card')
                                                        @php
                                                            $credit_card = 'selected';
                                                        @endphp
                                                    @elseif($val == 'cash')
                                                        @php
                                                            $cash = 'selected';
                                                        @endphp
                                                    @elseif($val == 'wallet')
                                                        @php
                                                            $wallet = 'selected';
                                                        @endphp
                                                    @elseif($val == 'debit_card')
                                                        @php
                                                            $debit_card = 'selected';
                                                        @endphp
                                                    @elseif($val == 'contactless')
                                                        @php
                                                            $contactless = 'selected';
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            @endif
                                            <select name="payment_type[]" id="payment_type" class="form-control select2"
                                                multiple="multiple" data-placeholder="@lang('view_pages.select') @lang('view_pages.payment_type')"
                                                required>
                                                <option value="cash" {{ $cash }}>Cash</option>
                                                <option value="wallet" {{ $wallet }}>Mobile Wallets</option>
                                                <option value="credit_card" {{ $credit_card }}>Credit Cards</option>
                                                <option value="debit_card" {{ $debit_card }}>Debit Cards</option>
                                                <option value="contactless" {{ $contactless }}>Contactless Payments (NFC)</option>
                                            </select>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('payment_type') }}</span>
                                    </div>
                                </div>
                                @if ($zone_price->price_type == 1)
                                    <div class="row">
                                        <div class="box box-solid box-info">
                                            <div class="box-header with-border">
                                                <h4 class="box-title">@lang('view_pages.price')</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ml-2 mr-2">
                                        <div class="col-12 col-lg-6 mt-4">
                                            <label for="ride_now_base_price" class="form-label">@lang('view_pages.base_price')
                                                (@lang('view_pages.kilometer'))</label>
                                            <input id="ride_now_base_price" name="ride_now_base_price"
                                                value="{{ old('ride_now_base_price', $zone_price->base_price) }}"
                                                type="text" class="form-control w-full"
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.base_price')" required>
                                            <span class="text-danger">{{ $errors->first('ride_now_base_price') }}</span>
                                        </div>

                                        {{-- <div class="col-12 col-lg-6 mt-4">
                                <label for="price_per_distance" class="form-label">@lang('view_pages.price_per_distance')  (@lang('view_pages.kilometer'))</label>
                                <input id="ride_now_price_per_distance" name="ride_now_price_per_distance" value="{{ old('ride_now_price_per_distance', $zone_price->price_per_distance) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_distance')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_price_per_distance') }}</span>
                            </div> --}}

                                        <div class="col-12 col-lg-6 mt-4">
                                            <label for="base_distance" class="form-label">@lang('view_pages.base_distance')</label>
                                            <input id="ride_now_base_distance" name="ride_now_base_distance"
                                                value="{{ old('ride_now_base_distance', $zone_price->base_distance) }}"
                                                type="number" min="0" class="form-control w-full"
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.base_distance')" required>
                                            <span class="text-danger">{{ $errors->first('ride_now_base_distance') }}</span>
                                        </div>

                                        {{-- <div class="col-12 col-lg-6 mt-4">
                                <label for="price_per_time" class="form-label">@lang('view_pages.price_per_time')</label>
                                <input id="ride_now_price_per_time" name="ride_now_price_per_time" value="{{ old('ride_now_price_per_time', $zone_price->price_per_time) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_time')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_price_per_time') }}</span>
                            </div> --}}

                                        <div class="col-12 col-lg-6 mt-4">
                                            <label for="cancellation_fee" class="form-label">@lang('view_pages.cancellation_fee')</label>
                                            <input id="ride_now_cancellation_fee" name="ride_now_cancellation_fee"
                                                value="{{ old('ride_now_cancellation_fee', $zone_price->cancellation_fee) }}"
                                                type="text" class="form-control w-full"
                                                placeholder="@lang('view_pages.enter') @lang('view_pages.cancellation_fee')" required>
                                            <span
                                                class="text-danger">{{ $errors->first('ride_now_cancellation_fee') }}</span>
                                        </div>

                                        {{-- <div class="col-12 col-lg-6 mt-4">
                                <label for="waiting_charge" class="form-label">@lang('view_pages.waiting_charge')</label>
                                <input id="ride_now_waiting_charge" name="ride_now_waiting_charge" value="{{ old('ride_now_waiting_charge', $zone_price->waiting_charge) }}" type="number" min="0" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.waiting_charge')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_waiting_charge') }}</span>
                            </div> --}}

                                        {{-- <div class="col-12 col-lg-6 mt-4">
                                <label for="free_waiting_time_in_mins_before_trip_start" class="form-label">@lang('view_pages.free_waiting_time_in_mins_before_trip_start')</label>
                                <input id="ride_now_free_waiting_time_in_mins_before_trip_start" name="ride_now_free_waiting_time_in_mins_before_trip_start" value="{{ old('ride_now_free_waiting_time_in_mins_before_trip_start', $zone_price->free_waiting_time_in_mins_before_trip_start) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.free_waiting_time_in_mins_before_trip_start')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_free_waiting_time_in_mins_before_trip_start') }}</span>
                            </div> --}}

                                        {{-- <div class="col-12 col-lg-6 mt-4">
                                <label for="free_waiting_time_in_mins_after_trip_start" class="form-label">@lang('view_pages.free_waiting_time_in_mins_after_trip_start')</label>
                                <input id="ride_now_free_waiting_time_in_mins_after_trip_start" name="ride_now_free_waiting_time_in_mins_after_trip_start" value="{{ old('ride_now_free_waiting_time_in_mins_after_trip_start', $zone_price->free_waiting_time_in_mins_after_trip_start) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.free_waiting_time_in_mins_after_trip_start')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_free_waiting_time_in_mins_after_trip_start') }}</span>
                            </div> --}}
                                    </div>
                        </div>
                    @else
                        <!-- <div class="col-sm-12"> -->
                        <div class="row">
                            <div class="form-group">
                                <h2 class="fw-medium fs-base me-auto">
                                    Ride Later
                                </h2>
                            </div>
                            <div class="row ml-2 mr-2">
                                <div class="col-12 col-lg-6 mt-4">
                                    <label for="ride_later_base_price" class="form-label">@lang('view_pages.base_price')
                                        (@lang('view_pages.kilometer'))</label>
                                    <input id="ride_later_base_price" name="ride_later_base_price"
                                        value="{{ old('ride_later_base_price', $zone_price->base_price) }}"
                                        type="text" class="form-control w-full"
                                        placeholder="@lang('view_pages.enter') @lang('view_pages.base_price')" required>
                                    <span class="text-danger">{{ $errors->first('ride_later_base_price') }}</span>
                                </div>

                                <div class="col-12 col-lg-6 mt-4">
                                    <label for="price_per_distance" class="form-label">@lang('view_pages.price_per_distance')
                                        (@lang('view_pages.kilometer'))</label>
                                    <input id="ride_later_price_per_distance" name="ride_later_price_per_distance"
                                        value="{{ old('ride_later_price_per_distance', $zone_price->price_per_distance) }}"
                                        type="text" class="form-control w-full"
                                        placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_distance')" required>
                                    <span class="text-danger">{{ $errors->first('ride_later_price_per_distance') }}</span>
                                </div>

                                <div class="col-12 col-lg-6 mt-4">
                                    <label for="base_distance" class="form-label">@lang('view_pages.base_distance')</label>
                                    <input id="ride_later_base_distance" name="ride_later_base_distance"
                                        value="{{ old('ride_later_base_distance', $zone_price->base_distance) }}"
                                        type="number" min="0" class="form-control w-full"
                                        placeholder="@lang('view_pages.enter') @lang('view_pages.base_distance')" required>
                                    <span class="text-danger">{{ $errors->first('ride_later_base_distance') }}</span>
                                </div>

                                <div class="col-12 col-lg-6 mt-4">
                                    <label for="price_per_time" class="form-label">@lang('view_pages.price_per_time')</label>
                                    <input id="ride_later_price_per_time" name="ride_later_price_per_time"
                                        value="{{ old('ride_later_price_per_time', $zone_price->price_per_time) }}"
                                        type="text" class="form-control w-full"
                                        placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_time')" required>
                                    <span class="text-danger">{{ $errors->first('ride_later_price_per_time') }}</span>
                                </div>

                                <div class="col-sm-6">
                                    <label for="cancellation_fee" class="form-label">@lang('view_pages.cancellation_fee')</label>
                                    <input id="ride_later_cancellation_fee" name="ride_later_cancellation_fee"
                                        value="{{ old('ride_later_cancellation_fee', $zone_price->cancellation_fee) }}"
                                        type="text" class="form-control w-full"
                                        placeholder="@lang('view_pages.enter') @lang('view_pages.cancellation_fee')" required>
                                    <span class="text-danger">{{ $errors->first('ride_later_cancellation_fee') }}</span>
                                </div>
                            </div>
                            @endif

                            <div class="col-12">
                                <button type="submit"
                                    class="btn btn-primary btn-sm pull-right m-5">{{ __('view_pages.save') }}</button>
                            </div>
                            </form>
                        </div>
                        <!-- END: Form Layout -->
                    </div>
                </div>
                <!-- jQuery 3 -->
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script>
                    $('.select2').select2({
                        placeholder: "Select ...",
                    });

                    $(document).on('change', '#transport_type', function() {
                        let zone = document.getElementById("zone").value;
                        let transport_type = $(this).val();

                        $.ajax({
                            url: "{{ url('vehicle_fare/fetch/vehicles') }}",
                            type: 'GET',
                            data: {
                                '_zone': zone,
                                'transport_type': transport_type,
                            },
                            success: function(result) {

                                var vehicles = result.data;
                                var option = ''
                                option += '<option value="" disabled selected>Select a vehicle</option>';
                                vehicles.forEach(vehicle => {
                                    option += `<option value="${vehicle.id}">${vehicle.name}</option>`;
                                });

                                $('#type').html(option)
                            }
                        });
                    });
                    $(document).on('change', '#zone', function() {
                        var selected = $(this).val();
                        $("#transport_type").empty();

                        $.ajax({
                            url: "{{ route('getTransportTypes') }}",
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                // $("#transport_type").attr('disabled', false);
                                $.each(response, function(key, value) {
                                    $("#transport_type").append('<option value=' + value + '>' + value +
                                        '</option>');
                                });
                            }
                        });
                    });
                    /*hide and show*/
                    $(document).on('change', '#type', function() {
                        let selectedType = $(this).val();

                        // Check if a type is selected
                        if (selectedType) {
                            $.ajax({
                                url: "{{ url('vehicle_fare/fetch/trip_type') }}",
                                type: 'GET',
                                data: {
                                    'selectedType': selectedType
                                },
                                success: function(result) {
                                    console.log(result.data.trip_dispatch_type);

                                    if (result.data.trip_dispatch_type === 'bidding') {
                                        // Hide the specified div elements for "ride_now"
                                        $('#ride_now_waiting_charge').closest('.col-12').hide();
                                        $('#ride_now_free_waiting_time_in_mins_before_trip_start').closest(
                                            '.col-12').hide();
                                        $('#ride_now_free_waiting_time_in_mins_after_trip_start').closest('.col-12')
                                            .hide();

                                        // Hide the specified div elements for "ride_later"
                                        $('#ride_later_waiting_charge').closest('.col-12').hide();
                                        $('#ride_later_free_waiting_time_in_mins_before_trip_start').closest(
                                            '.col-12').hide();
                                        $('#ride_later_free_waiting_time_in_mins_after_trip_start').closest(
                                            '.col-12').hide();
                                    } else {
                                        // Show the div elements if the type is not "bidding"
                                        $('.col-12').show();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Handle error here
                                }
                            });
                        } else {
                            // Hide all divs when no option is selected
                            $('.col-12').hide();
                        }
                    });

                    /*hide and show*/

                    $(document).ready(function() {
                        let selectedType = $('#type').val(); // Get the initially selected type
                        // alert(selectedType);
                        // Check if a type is selected
                        if (selectedType) {
                            $.ajax({
                                url: "{{ url('vehicle_fare/fetch/trip_type') }}",
                                type: 'GET',
                                data: {
                                    'selectedType': selectedType
                                },
                                success: function(result) {
                                    // console.log(result.data.trip_dispatch_type);

                                    if (result.data.trip_dispatch_type === 'bidding') {
                                        // Hide the specified div elements for "ride_now"
                                        $('#ride_now_waiting_charge').closest('.col-12').hide();
                                        $('#ride_now_free_waiting_time_in_mins_before_trip_start').closest(
                                            '.col-12').hide();
                                        $('#ride_now_free_waiting_time_in_mins_after_trip_start').closest('.col-12')
                                            .hide();

                                        // Hide the specified div elements for "ride_later"
                                        $('#ride_later_waiting_charge').closest('.col-12').hide();
                                        $('#ride_later_free_waiting_time_in_mins_before_trip_start').closest(
                                            '.col-12').hide();
                                        $('#ride_later_free_waiting_time_in_mins_after_trip_start').closest(
                                            '.col-12').hide();
                                    } else {
                                        // Show the div elements if the type is not "bidding"
                                        $('.col-12').show();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Handle error here
                                }
                            });
                        } else {
                            // Hide all divs when no option is selected
                            $('.col-12').hide();
                        }
                    });
                </script>

            @endsection
