@extends('admin.layouts.app')
@section('title', 'Main page')


@section('content')

<section class="content">
    <div class="page-content page-container" id="page-content">
        <div class="padding">
            <div class="col-md-12">
                <div class="card card-bordered">
                    <div class="card-header">
                        <h4 class="card-title"><strong>Chat</strong></h4>
                        <a class="btn btn-xs btn-secondary" href="#" data-abc="true">Let's Chat
                            App</a>
                    </div>


                    <div class="ps-container ps-theme-default ps-active-y" id="chat-content"
                        style="overflow-y: scroll !important; height:400px !important;">

                        @php
                            $previousDate = null;
                            $todayDisplayed = false;
                        @endphp

                        @foreach($messages as $message)
                            @php
                                $messageDate = \Carbon\Carbon::parse($message->created_at)->toDateString();
                                $messageTime = \Carbon\Carbon::parse($message->created_at)->format('h:i A');

                                if (!$todayDisplayed && \Carbon\Carbon::parse($message->created_at)->isToday()) {
                                    echo '<div class="media media-meta-day">Today</div>';
                                    $todayDisplayed = true;
                                } elseif (!$previousDate || $messageDate !== $previousDate) {
                                    echo '<div class="media media-meta-day">' . \Carbon\Carbon::parse($message->created_at)->format('F j, Y') . '</div>';
                                }

                                $previousDate = $messageDate;
                            @endphp
                            @if($message->from_type == 4)
                                {{-- Driver message --}}
                                <div class="media media-chat">
                                    <img class="avatar" src="https://img.icons8.com/color/36/000000/administrator-male.png" alt="Driver">
                                    <div class="media-body">
                                        <p>{{ $message->message }}</p>
                                        <p class="meta"><time datetime="{{ $message->created_at }}">{{ $message->created_at->format('h:i') }}</time></p>
                                    </div>
                                </div>
                            @elseif($message->from_type == 3)
                                {{-- Company message --}}
                                <div class="media media-chat media-chat-reverse">
                                    <div class="media-body">
                                        <p>{{ $message->message }}</p>
                                        <p class="meta"><time datetime="{{ $message->created_at }}">{{ $message->created_at->format('h:i') }}</time></p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="publisher bt-1 border-light">
                        <img class="avatar avatar-xs"
                            src="https://img.icons8.com/color/36/000000/administrator-male.png"
                            alt="...">
                        <input class="publisher-input" type="text" id="message" name="message" placeholder="Write something">
                        <span class="publisher-btn file-group">
                            <i class="fa fa-paperclip file-browser"></i>
                            <input type="file">
                        </span>
                        <a class="publisher-btn" href="#" data-abc="true">
                            <i class="fa fa-smile"></i>
                        </a>
                        <a class="publisher-btn text-info" id="chat" href="#" data-abc="true">
                            <i class="fa fa-paper-plane"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $("#chat").click(function() {
        $(document).ready(function() {
        var messageValue = $("#message").val();
        console.log(messageValue);
        $.ajax({
            url: '/chat/send',
            type: 'post',
            data: {
                _token: '{{ csrf_token() }}',
                message: messageValue,
            },
            success: function(response) {
                console.log(response);
                // $('#cartItems').html('');
            },
            error: function(xhr, status, error) {
                console.log('An error occurred: ' + error);
            }
        });
        });
    });
</script>
@endsection
