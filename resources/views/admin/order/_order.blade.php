<table class="table table-hover">
    <thead>
        <tr>
            <th> @lang('view_pages.s_no')</th>
            <th> @lang('view_pages.package_name')</th>
            <th> Users</th>
            <th> @lang('view_pages.start_date')</th>
            <th> End Date</th>
            <th> @lang('view_pages.status')</th>
            @if(auth()->user()->can('edit-order'))
                <th> @lang('view_pages.action')</th>
            @endif
        </tr>
    </thead>

<tbody>
    @php
        $i= $results->firstItem();
    @endphp

    @forelse($results as $key => $result)
        <tr>
            <td>{{ $i++ }} </td>
            <td>{{ $result->package_name }}</td>
            <td>{{ $result->name }}</td>
            <td>{{ \Carbon\Carbon::parse($result->start_date)->format('d-M-Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($result->end_date)->format('d-M-Y') }}</td>
            @if($result->active == 1)
                <td><span class="label label-success">@lang('view_pages.active')</span></td>
            @elseif($result->active == 2)
                <td><span class="label label-danger">Expired</span></td>
            @else
                <td><span class="label label-warning">@lang('view_pages.inactive')</span></td>
            @endif
            <td>
            @if(auth()->user()->can('edit-order'))
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('view_pages.action')
                </button>
                    <div class="dropdown-menu">
                        @if(auth()->user()->can('edit-order'))
                            <a class="dropdown-item" href="{{url('order',$result->id)}}"><i class="fa fa-pencil"></i>@lang('view_pages.edit')</a>
                        @endif
                        @if(auth()->user()->can('delete-order'))
                            <a class="dropdown-item sweet-delete" href="{{url('order/delete',$result->id)}}"><i class="fa fa-trash-o"></i>@lang('view_pages.delete')</a>
                        @endif
                    </div>
                </div>
            @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="11">
                <p id="no_data" class="lead no-data text-center">
                    <img src="{{asset('assets/img/dark-data.svg')}}" style="width:150px;margin-top:25px;margin-bottom:25px;" alt="">
                    <h4 class="text-center" style="color:#333;font-size:25px;">@lang('view_pages.no_data_found')</h4>
                </p>
            </td>
        </tr>
    @endforelse

    </tbody>
    </table>
    <ul class="pagination pagination-sm pull-right">
        <li>
            <a href="#">{{$results->links()}}</a>
        </li>
    </ul>
