<table class="table table-hover">
    <thead>
        <tr>
            <th> @lang('view_pages.s_no')</th>
            <th> name</th>
            <th> email</th>
            <th> mobile</th>
            <th> country</th>
            {{-- <th> address</th> --}}
            {{-- <th> state</th> --}}
            {{-- <th> pincode</th> --}}
            {{-- <th> subject</th> --}}
            <th> Action</th>
        </tr>
    </thead>

<tbody>
    @php
        $i= $results->firstItem();
    @endphp

    @forelse($results as $key => $result)
        <tr>
            <td>{{ $i++ }} </td>
            <td>{{ $result->name }}</td>
            <td>{{ $result->email }}</td>
            <td>{{ $result->mobile }}</td>
            <td>{{ $result->country }}</td>
            {{-- <td>{{ $result->address }}</td> --}}
            {{-- <td>{{ $result->state }}</td> --}}
            {{-- <td>{{ $result->pincode }}</td> --}}
            {{-- <td>{{ $result->subject }}</td> --}}
            <td><a href="{{url('contact/show',$result->id)}}" class="btn btn-info btn-sm">View</a>
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
