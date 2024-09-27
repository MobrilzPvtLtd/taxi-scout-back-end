@extends('admin.layouts.app')

@section('title', 'Galleries')

@section('content')

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box table-responsive">
                    <div class="box-header with-border">
                        <div class="row text-right">
                            @if (auth()->user()->can('add-galleries'))
                            <div class="col-12 text-right">
                                <a href="{{ url('galleries/create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus-circle mr-2"></i>Add galleries</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div id="js-galleries-partial-target">
                        <include-fragment src="galleries/fetch">
                            <span style="text-align: center;font-weight: bold;">Loading</span>
                        </include-fragment>
                    </div>

                </div>
            </div>
        </div>

        <script src="{{ asset('assets/js/fetchdata.min.js') }}"></script>
        <script>
            $(function() {
                $('body').on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    $.get(url, $('#search').serialize(), function(data) {
                        $('#js-galleries-partial-target').html(data);
                    });
                });

                $('#search').on('click', function(e) {
                    e.preventDefault();
                    var search_keyword = $('#search_keyword').val();
                    console.log(search_keyword);
                    fetch('galleries/fetch?search=' + search_keyword)
                        .then(response => response.text())
                        .then(html => {
                            document.querySelector('#js-galleries-partial-target').innerHTML = html
                        });
                });

            });

        </script>
    @endsection
