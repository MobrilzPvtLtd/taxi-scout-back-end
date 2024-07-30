@extends('admin.layouts.app')
@section('title', 'Main page')


@section('content')
<section class="content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Chat </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div>
                        @if (count($user_messages) > 0)
                            {{-- <div style="max-height:400px;height:400px;overflow-y: scroll;"> --}}
                                <div chat-content id="ko">
                                </div>
                            {{-- </div> --}}
                        @else
                            <p>No conversation so far. Start a conversation</p>
                        @endif
                    </div>
                    <form id="myForm" class="form-group" style="margin-top: 20px">
                        <input type="hidden" name="chat_id" value="{{ request()->id }}">
                        <div class="publisher bt-1 border-light">
                            <img class="avatar avatar-xs" src="https://img.icons8.com/color/36/000000/administrator-male.png" alt="...">
                            <input class="publisher-input" type="text" name="message" autocomplete="off" chat-box class="form-control" placeholder="Write something">
                            <span class="publisher-btn file-group">
                                <i class="fa fa-paperclip file-browser"></i>
                                <input type="file">
                            </span>
                            <div class="input-group-prepend">
                                <button class="publisher-btn text-info" id="submitMessage"><i class="fa fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- @php
        dd(request()->id);
    @endphp --}}
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $('#submitMessage').click(function(e) {
            e.preventDefault();
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            })
            $.ajax({
                url: "{{ url('/chat/send') }}",
                method: 'post',
                data: {
                    message: $('[name="message"]').val(),
                    chat_id: $('[name="chat_id"]').val()
                },
                success: function(result) {
                    $('[name="message"]').val('');
                }
            })
        })

        setInterval(function() {
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        var auth = "{{Auth::user()->id}}";
        var chat_id = $('[name="chat_id"]').val();
        $.ajax({
            url: "{{ url('/chat/getConversations') }}",
            method: "get",
            data: {
                chat_id: chat_id
            },
            success: function(data){
                // console.log(data);
                var previousDate = null;
                var todayDisplayed = false;
                $('[chat-content]').html('');

                $.each(data, function(i, v) {
                    var createdAt = new Date(v.created_at);
                    var formattedDate = createdAt.toLocaleString('en-US', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });
                    var messageHtml = '';

                    var messageDate = new Date(v.created_at).toLocaleDateString();

                    if (!todayDisplayed && isToday(createdAt)) {
                        messageHtml += '<div class="media media-meta-day">Today</div>';
                        todayDisplayed = true;
                    } else if (!previousDate || messageDate !== previousDate) {
                        var displayDate = new Date(v.created_at).toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric'
                        });
                        messageHtml += '<div class="media media-meta-day">' + displayDate + '</div>';
                    }

                    previousDate = messageDate;

                    if (v.receiver_id == auth) {
                        messageHtml += `
                            <div class="media media-chat">
                                <img class="avatar" src="https://img.icons8.com/color/36/000000/administrator-male.png" alt="Driver">
                                <div class="media-body">
                                    <p>${v.message}</p>
                                    <p class="meta"><time datetime="${v.created_at}">${formattedDate}</time></p>
                                </div>
                            </div>
                        `;
                    } else {
                        messageHtml += `
                            <div class="media media-chat media-chat-reverse medias" id="">
                                <div class="media-body" id="messageShow">
                                    <p>${v.message}</p>
                                    <p class="meta"><time datetime="${v.created_at}">${formattedDate}</time></p>
                                </div>
                            </div>
                        `;
                    }

                    $('[chat-content]').append(messageHtml);
                });

                function isToday(someDate) {
                    const today = new Date();
                    return someDate.getDate() == today.getDate() &&
                        someDate.getMonth() == today.getMonth() &&
                        someDate.getFullYear() == today.getFullYear();
                }

                markMessagesAsSeen();
            }
        })
        }, 1000);


        function markMessagesAsSeen() {
            var chat_id = $('[name="chat_id"]').val();
            $.ajax({
                url: '/chat/seen',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    chat_id: chat_id,
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.log('An error occurred while marking messages as seen:', error);
                }
            });
        }
    })

</script>
@endsection
