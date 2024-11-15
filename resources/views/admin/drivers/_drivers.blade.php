<div class="box-body">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th> @lang('view_pages.s_no')
                        <span style="float: right;">

                        </span>
                    </th>
                    <th> @lang('view_pages.name')
                        <span style="float: right;">
                        </span>
                    </th>

                    @if (auth()->user()->id == 1)
                        <th> Taxi Company
                            <span style="float: right;">
                            </span>
                        </th>
                    @endif

                    <th> @lang('view_pages.area')
                        <span style="float: right;">
                        </span>
                    </th>
                    <th> @lang('view_pages.email')
                        <span style="float: right;">
                        </span>
                    </th>
                    <th> @lang('view_pages.mobile')
                        <span style="float: right;">
                        </span>
                    </th>
                    <th> @lang('view_pages.transport_type')</th>
                    <span style="float: right;">
                    </span>
                    </th>
                    <!-- <th> @lang('view_pages.vehicle_type')</th>
                    <span style="float: right;">
                    </span>
                    </th> -->
                    {{-- <th>@lang('view_pages.document_view')</th> --}}
                    <!-- <th> @lang('view_pages.status') -->
                    <span style="float: right;">
                    </span>
                    </th>
                    <th> @lang('view_pages.approve_status')<span style="float: right;"></span></th>
                    {{-- <th> @lang('view_pages.declined_reason')<span style="float: right;"></span></th> --}}
                    {{-- <th> @lang('view_pages.rating')
                        <span style="float: right;">
                        </span>
                    </th> --}}
                    <!-- <th> @lang('view_pages.online_status')<span style="float: right;"></span></th> -->
                    <th> @lang('view_pages.action')
                        <span style="float: right;">
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @if (count($results) < 1)
                    <tr>
                        <td colspan="11">
                            <p id="no_data" class="lead no-data text-center">
                                <img src="{{ asset('assets/img/dark-data.svg') }}"
                                    style="width:150px;margin-top:25px;margin-bottom:25px;" alt="">
                            <h4 class="text-center" style="color:#333;font-size:25px;">@lang('view_pages.no_data_found')</h4>
                            </p>
                    </tr>
                @else
                    @php  $i= $results->firstItem(); @endphp

                    @foreach ($results as $key => $result)
                        @php
                            $owner = App\Models\Admin\Owner::whereHas('user', function ($query) use   ($result) {
                                $query->where('owner_unique_id', $result->owner_id);
                            })->first();
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }} </td>
                            <td>{{ $result->name }}</td>
                            @if(auth()->user()->id == 1)
                                <td> {{$owner->name}}</td>
                            @endif
                            @if ($result->serviceLocation)
                                <td>{{ $result->serviceLocation->name }}</td>
                            @else
                                <td>--</td>
                            @endif
                            @if (env('APP_FOR') == 'demo')
                                <td>**********</td>
                            @else
                                <td>{{ $result->email }}</td>
                            @endif
                            @if (env('APP_FOR') == 'demo')
                                <td>**********</td>
                            @else
                                <td>{{ $result->mobile }}</td>
                            @endif
                            <td>{{ $result->transport_type }}</td>
                            <!-- <td>{{ $result->vehicleType }}</td> -->
                            {{-- <td>
                                @if (auth()->user()->can('driver-document'))
                                    <a href="{{ url('drivers/document/view', $result->id) }}"
                                        class="btn btn-social-icon btn-bitbucket">
                                        <i class="fa fa-file-text"></i>
                                @endif
                                </a>
                            </td> --}}
                            <!-- @if ($result->active)
                            <td><button class="btn btn-success btn-sm">{{ trans('view_pages.active') }}</button></td>
                            @else
                            <td><button class="btn btn-danger btn-sm">{{ trans('view_pages.iniive') }}</button></td>
                            @endif -->

                            <td>
                                @if($result->approve == 1)
                                    <button type="button" class="dropdown-item btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: green;color:#fff">@lang('view_pages.approved')
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item driver-approval text-approval" href="#" data-url="{{ url('company/drivers/approve', $result->id) }}/?status=0">
                                            Disapprove
                                        </a>
                                    </div>
                                @elseif($result->approve == 0)
                                    <button type="button" class="dropdown-item btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #fc4b6c;color:#fff">@lang('view_pages.disapproved')
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item driver-approval" href="#" data-url="{{ url('company/drivers/approve', $result->id) }}/?status=1">
                                            Approve
                                        </a>
                                    </div>
                                @else
                                    <button type="button" class="dropdown-item btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #f4c529;color:#fff">Pending
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item driver-approval" href="#" data-url="{{ url('company/drivers/approve', $result->id) }}/?status=1">
                                            Approve
                                        </a>
                                        <a class="dropdown-item driver-approval text-approval" href="#" data-url="{{ url('company/drivers/approve', $result->id) }}/?status=0">
                                            Disapprove
                                        </a>
                                    </div>
                                @endif
                            </td>
                            {{-- @if ($result->reason)
                                <td>{{ $result->reason }}</td>
                            @else
                                <td>--</td>
                            @endif --}}
                            {{-- <td>
                                @php $rating = $result->rating($result->user_id); @endphp

                                @foreach (range(1, 5) as $i)
                                    <span class="fa-stack" style="width:1em">

                                        @if ($rating > 0)
                                            @if ($rating > 0.5)
                                                <i class="fa fa-star checked"></i>
                                            @else
                                                <i class="fa fa-star-half-o"></i>
                                            @endif
                                        @else
                                            <i class="fa fa-star-o "></i>
                                        @endif
                                        @php $rating--; @endphp
                                    </span>
                                @endforeach
                            </td> --}}
                            <td>
                                <button type="button" class="btn btn-info btn-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('view_pages.action')
                                </button>
                                @if ($result->approve == 1)
                                    <div class="dropdown-menu w-48 ">
                                        @if (auth()->user()->can('edit-drivers'))
                                            <a class="dropdown-item" href="{{ url('drivers', $result->id) }}">
                                                <i class="fa fa-pencil"></i>@lang('view_pages.edit')
                                            </a>
                                        @endif
                                        <!-- @if ($result->active)
                                        <a class="dropdown-item" href="{{ url('drivers/toggle_status', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.inactive')</a>
                                        @else
                                        <a class="dropdown-item" href="{{ url('drivers/toggle_status', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.active')</a>
                                        @endif -->
                                        {{-- @if (auth()->user()->can('toggle-drivers'))
                                            <a class="dropdown-item decline" data-reason="{{ $result->reason }}"
                                                data-id="{{ $result->id }}"
                                                href="{{ url('drivers/toggle_approve', ['driver' => $result->id, 'approval_status' => 0]) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.disapproved')</a>

                                            <a class="dropdown-item"
                                                href="{{ url('drivers/toggle_approve', ['driver' => $result->id, 'approval_status' => 1]) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.approved')</a>
                                        @endif --}}
                                        <!-- @if ($result->available)
                                        <a class="dropdown-item" href="{{ url('drivers/toggle_available', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.offline')</a>
                                        @else
                                        <a class="dropdown-item" href="{{ url('drivers/toggle_available', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.online')</a>
                                        @endif -->
                                        @if (auth()->user()->can('delete-drivers'))
                                            <a class="dropdown-item sweet-delete" href="#"
                                                data-url="{{ url('drivers/delete', $result->id) }}">
                                                <i class="fa fa-trash-o"></i>@lang('view_pages.delete')</a>
                                        @endif
                                        @if (auth()->user()->can('view-request-list'))
                                            <a class="dropdown-item"
                                                href="{{ url('drivers/request-list', $result->id) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.request_list')</a>
                                        @endif
                                        @if (auth()->user()->can('driver-payment-history'))
                                            <a class="dropdown-item"
                                                href="{{ url('drivers/payment-history', $result->id) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.driver_payment_history')</a>
                                        @endif
                                        @if (auth()->user()->can('view-driver-profile'))
                                            <a class="dropdown-item"
                                                href="{{ url('driver_profile_dashboard_view', $result->id) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.view_profile')</a>
                                        @endif
                                    </div>
                                @else
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->can('edit-drivers'))
                                            <a class="dropdown-item" href="{{ url('drivers', $result->id) }}">
                                                <i class="fa fa-pencil"></i>@lang('view_pages.edit')
                                            </a>
                                        @endif

                                        <!-- @if ($result->active)
                                            <a class="dropdown-item" href="{{ url('drivers/toggle_status', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.inactive')</a>
                                            @else
                                            <a class="dropdown-item" href="{{ url('drivers/toggle_status', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.active')</a>
                                        @endif -->
                                        {{-- @if (auth()->user()->can('toggle-drivers'))
                                            <a class="dropdown-item decline" data-reason="{{ $result->reason }}"
                                                data-id="{{ $result->id }}"
                                                href="{{ url('drivers/toggle_approve', ['driver' => $result->id, 'approval_status' => 0]) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.disapproved')</a>

                                            <a class="dropdown-item"
                                                href="{{ url('drivers/toggle_approve', ['driver' => $result->id, 'approval_status' => 1]) }}">
                                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.approved')</a>
                                        @endif --}}
                                        <!-- @if ($result->available)
                                            <a class="dropdown-item" href="{{ url('drivers/toggle_available', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.offline')</a>
                                            @else
                                            <a class="dropdown-item" href="{{ url('drivers/toggle_available', $result->id) }}"><i class="fa fa-dot-circle-o"></i>@lang('view_pages.online')</a>
                                        @endif -->
                                        @if (auth()->user()->can('delete-drivers'))
                                            <a class="dropdown-item sweet-delete" href="#"
                                                data-url="{{ url('drivers/delete', $result->id) }}">
                                                <i class="fa fa-trash-o"></i>@lang('view_pages.delete')</a>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            {{-- </a> --}}
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="text-right">
            <span style="float:right">
                {{ $results->links() }}
            </span>
        </div>
    </div>
</div>
