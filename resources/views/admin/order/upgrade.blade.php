@extends('admin.layouts.app')
@section('title', 'Order Upgrate')

@section('content')
    {{-- {{session()->get('errors')}} --}}

    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{!! asset('assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') !!}">

    <!-- Start Page content -->
    <div class="content">
        <div class="container-fluid" style="margin-left: 15%;margin-top: 5%;">
            <div class="row">
                <div class="col-sm-8">
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
                            <form method="post" class="form-horizontal" action="{{ url('order/package-upgrade') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $item->id }}">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="package_name">@lang('view_pages.package_name') <span class="text-danger">*</span></label>
                                                <select name="package_id" id="package_id" class="form-control" required>
                                                    <option value="">-- Select a Package --</option>
                                                    @foreach (App\Models\Admin\Subscription::get() as $package)
                                                        @if($package->package_name != "Free")
                                                            <option value="{{ $package->id }}" {{ $package->id == $item->package_id ? 'selected' : '' }}>{{ $package->package_name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('package_name') }}</span>
                                            </div>
                                        </div>
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>@lang('view_pages.description')</th>
                                                    <th>@lang('view_pages.price')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="hidden" value="{{ $item->subscription->package_name }}" name="description" id="description" class="form-control">
                                                        <span id="descriptionText">{{ $item->subscription->package_name }}</span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" value="{{ $item->subscription->amount }}" name="package_amount" id="package_amount" class="form-control">
                                                        <span id="packageAmountText">$ {{ $item->subscription->amount }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm pull-right m-5" type="submit">
                                            Continue
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
    </div>

    <script>
        $('#package_id').on("change", function() {
            var package_id = $('[name="package_id"]').val();

            $.ajax({
                url: "{{ url('/order/package-show') }}",
                method: "get",
                data: {
                    package_id: package_id
                },
                success: function(data){
                    console.log(data);
                    if (data.amount) {
                        $('#packageAmountText').text('$ ' + data.amount);
                        $('#package_amount').val(data.amount);
                        $('#descriptionText').text(data.package_name);
                        $('#description').val(data.package_name);
                    } else {
                        console.error('Invalid amount:', data.amount);
                    }
                }
            })
        });
    </script>
@endsection
