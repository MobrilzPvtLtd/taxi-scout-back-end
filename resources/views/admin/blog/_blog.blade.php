<table class="table table-hover">
    <thead>
        <tr>
            <th> @lang('view_pages.s_no')</th>
            <th> Image</th>
            <th> Title</th>
            <th> Blog Category</th>
            <th> Description</th>
            <th> @lang('view_pages.status')</th>
            <th> @lang('view_pages.action')</th>
        </tr>
    </thead>

<tbody>
    @php
        $i= $results->firstItem();
    @endphp

    @forelse($results as $key => $result)
        <tr>
            <td>{{ $i++ }} </td>
            <td><img src="{{ asset('blog/'.$result->image) }}" alt="" width="70px"></td>
            <td>{{ $result->title }}</td>
            <td>{{ $result->category_name }}</td>
            <td>{{ $result->description }}</td>
            @if($result->status == 1)
                <td><span class="label label-success">Active</span></td>
            @else
                <td><span class="label label-warning">Inactive</span></td>
            @endif
            <td>
            @if(auth()->user()->can('edit-blogs'))
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('view_pages.action')
                </button>
                    <div class="dropdown-menu">
                        @if(auth()->user()->can('edit-blogs'))
                            <a class="dropdown-item" href="{{url('blogs',$result->id)}}"><i class="fa fa-pencil"></i>@lang('view_pages.edit')</a>
                        @endif
                        @if(auth()->user()->can('delete-blogs'))
                            <a class="dropdown-item sweet-delete" href="{{url('blogs/delete',$result->id)}}"><i class="fa fa-trash-o"></i>@lang('view_pages.delete')</a>
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
