@extends('admin.layouts.app')

@section('title', 'Users')

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
            <include-fragment src="chat/fetch">
                <span style="text-align: center;font-weight: bold;">@lang('view_pages.loading')</span>
            </include-fragment>
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

