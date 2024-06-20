<table class="table table-hover">
    <thead>
        <tr>
            <th> @lang('view_pages.s_no')</th>
            <th> Image</th>
            <th> User</th>
            <th> Date</th>
        </tr>
    </thead>

<tbody>
    @php
        $i= $results->firstItem();
        $total_chat = App\Models\Request\Chat::where('from_type', 4)->where('seen', 0)->count();
    @endphp

    @forelse($results as $key => $result)
        <tr>
            <td>{{ $i++ }} </td>
            <td>
                <a href="{{ route('chatGetById', $result->user_id) }}" id="is_view">
                    @if($result->seen == 0)
                        <p class="notifyChat">
                            {{$total_chat}}
                        </p>
                    @endif
                    <img src="{{ asset('storage/uploads/user/profile-picture/') . '/' . $result->profile_picture }}" alt="" width="50px">
                </a>
            </td>
            <td>
                <a href="{{ route('chatGetById', $result->user_id) }}">
                    {{ $result->name }}
                </a>
            </td>
            <td>
                @php
                    $createdAt = \Carbon\Carbon::parse($result->created_at);
                    $now = \Carbon\Carbon::now();

                    if ($createdAt->isToday()) {
                        echo 'Today at ' . $createdAt->format('h:i A');
                    } else {
                        echo $createdAt->format('d-M-Y');
                    }
                @endphp
                {{-- {{ \Carbon\Carbon::parse($result->created_at)->format('d-M-Y') }} --}}
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
