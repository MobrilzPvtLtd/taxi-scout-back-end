@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')

<section class="content">
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row text-right">
                    <div class="col-12 text-right">
                        @if(auth()->user()->can('add-subscription'))
                            <a href="{{url('subscription/create')}}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus-circle mr-2"></i>@lang('view_pages.add_subscription')
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        <div id="js-subscription-partial-target">
            <include-fragment src="subscription/fetch">
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
            $('#js-subscription-partial-target').html(data);
        });
    });

    $('#search').on('click', function(e){
        e.preventDefault();
            var search_keyword = $('#search_keyword').val();
            console.log(search_keyword);
            fetch('subscription/fetch?search='+search_keyword)
            .then(response => response.text())
            .then(html=>{
                document.querySelector('#js-subscription-partial-target').innerHTML = html
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

