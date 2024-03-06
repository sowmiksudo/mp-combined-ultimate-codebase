@extends('frontend.layouts.app')

@section('content')
    <section class="mb-4 pt-3">
        <div class="container">
            <h1 class="d-block text-center h2 my-5 fw-700">{{ translate('Pre-Loved Products') }}</h1>
            <div class="row gutters-5 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2">
                @forelse ($products as $key => $product)
                    <div class="col">
                        @include('frontend.partials.product_box_1', ['product' => $product])
                    </div>
                @empty
                    <div class="mx-auto text-center alert alert-primary">No pre-loved product is available right now. Please
                        try again later.</div>
                @endforelse
            </div>
            <div class="aiz-pagination aiz-pagination-center mt-4">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        var ENDPOINT = "{{ route('inhouse.pre_loved') }}";

        var page = 1;



        /*------------------------------------------

        --------------------------------------------

        Call on Scroll

        --------------------------------------------

        --------------------------------------------*/

        $(window).scroll(function() {

            if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 20)) {

                page++;

                infinteLoadMore(page);

            }

        });



        /*------------------------------------------

        --------------------------------------------

        call infinteLoadMore()

        --------------------------------------------

        --------------------------------------------*/

        function infinteLoadMore(page) {

            $.ajax({

                    url: ENDPOINT + "?page=" + page,

                    datatype: "html",

                    type: "get",

                    beforeSend: function() {

                        $('.auto-load').show();

                    }

                })

                .done(function(response) {

                    if (response.html == '') {

                        $('.auto-load').html("We don't have more data to display :(");

                        return;

                    }



                    $('.auto-load').hide();

                    $("#data-wrapper").append(response.html);

                })

                .fail(function(jqXHR, ajaxOptions, thrownError) {

                    console.log('Server error occured');

                });

        }
    </script>
@endsection
