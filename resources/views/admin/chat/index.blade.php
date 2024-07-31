@extends('admin.layouts.app')

@section('title', 'Users')

@section('extra-css')
<style>
    p.notifyChat {
        color: #fff;
        background-color: #e62525;
        width: 1.5vw;
        height: 3vh;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 41px;
        font-size: 12px;
        position: absolute;
        top: -50%;
        left: 60%;
    }
    tr td:nth-child(2) a {
        position: relative;
    }
</style>
@endsection

@section('content')

<section class="content">
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row text-right">
                    <div class="col-8 col-md-3">
                        <div class="form-group">
                            <input type="text" name="search" id="search_keyword" class="form-control"
                                placeholder="@lang('view_pages.enter_keyword')">
                        </div>
                    </div>
                    <div class="col-4 col-md-2 text-left">
                        <button id="search" class="btn btn-success btn-outline btn-sm py-2" type="submit">
                            @lang('view_pages.search')
                        </button>
                    </div>
                </div>
            </div>

        <div id="js-chat-partial-target">
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
                @endphp

                @forelse($results as $key => $result)
                    @php
                        $total_chat = App\Models\ChatMessage::where('chat_id', $result->id)->where('sender_id', $result->user_id)->where('seen_count', 0)->count();
                        // $total_chat = $result
                        // // ->where('from_type', 4)
                        // ->where('user_id', $result->user_id)
                        // // ->where('seen_count', $result->seen)
                        // ->count();
                    @endphp
                    <tr>
                        <td>{{ $i++ }} </td>
                        <td>
                            <a href="{{ route('chatGetById', $result->id) }}" class="is_view" data-target="{{ $result->id }}">
                                @if($total_chat)
                                    <p class="notifyChat">
                                        {{$total_chat}}
                                    </p>
                                @endif
                                <img src="{{ asset('storage/uploads/user/profile-picture/') . '/' . $result->profile_picture }}" alt="" width="50px">
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('chatGetById', $result->id) }}" class="is_view" data-target="{{ $result->id }}">
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
            {{-- <include-fragment src="chat/fetch"> --}}
                {{-- <span style="text-align: center;font-weight: bold;">@lang('view_pages.loading')</span> --}}
            {{-- </include-fragment> --}}
        </div>

        </div>
    </div>
</div>

<script src="{{asset('assets/js/fetchdata.min.js')}}"></script>
<script>
    $(function() {
        $('body').on('click', '.pagination a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.get(url, $('#search').serialize(), function(data){
                $('#js-chat-partial-target').html(data);
            });
        });

        $('#search').on('click', function(e){
            e.preventDefault();
                var search_keyword = $('#search_keyword').val();
                console.log(search_keyword);
                fetch('chat/fetch?search='+search_keyword)
                .then(response => response.text())
                .then(html=>{
                    document.querySelector('#js-chat-partial-target').innerHTML = html
                });
        });
    });

    $('.sweet-delete').click(function(e){
    // $(document).on('click','.sweet-delete',function(e){
        button = $(this);
        e.preventDefault();

        swal({
            title: "Are you sure to delete ?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete",
            cancelButtonText: "No! Keep it",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                button.unbind();
                button[0].click();
            }
        });
    });
</script>
@endsection

