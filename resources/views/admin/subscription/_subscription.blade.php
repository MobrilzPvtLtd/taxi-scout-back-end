<table class="table table-hover">
    <thead>
        <tr>
            <th> @lang('view_pages.s_no')</th>
            <th> @lang('view_pages.package_name')</th>
            <th> @lang('view_pages.number_of_drivers')</th>
            <th> @lang('view_pages.amount')</th>
            <th> @lang('view_pages.validity')</th>
            <th> @lang('view_pages.status')</th>
            @if(auth()->user()->can('edit-subscription'))
                <th> @lang('view_pages.action')</th>
            @endif
        </tr>
    </thead>

<tbody>
    @php  $i= $results->firstItem();
    @endphp

    @forelse($results as $key => $result)
        <tr>
            <td>{{ $i++ }} </td>
            <td>{{ $result->package_name }}</td>
            <td>{{ $result->number_of_drivers }}</td>
            <td>{{ $result->amount }}</td>
            <td>{{ $result->validity }} (Days)</td>
            @if($result->active == 1)
                <td><span class="label label-success">@lang('view_pages.active')</span></td>
            @else
                <td><span class="label label-danger">@lang('view_pages.inactive')</span></td>
            @endif
            <td>
            @if(auth()->user()->can('edit-subscription'))
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('view_pages.action')
                </button>
                    <div class="dropdown-menu">
                    @if(auth()->user()->can('edit-subscription'))
                        <a class="dropdown-item" href="{{url('subscription',$result->id)}}"><i class="fa fa-pencil"></i>@lang('view_pages.edit')</a>
                    @endif
                    @if(auth()->user()->can('delete-subscription'))
                        <a class="dropdown-item sweet-delete" href="{{url('subscription/delete',$result->id)}}"><i class="fa fa-trash-o"></i>@lang('view_pages.delete')</a>
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
