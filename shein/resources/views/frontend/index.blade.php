@extends('frontend.layouts.app')

@section('content')
    {{-- Categories , Sliders . Today's deal --}}

    {{-- Banner section 1 --}}
    @if (get_setting('home_banner1_images') != null)
        <div class="mb-4">
            <div class="container">
                <div class="row gutters-10">
                    @php $banner_1_imags = json_decode(get_setting('home_banner1_images')); @endphp
                    @foreach ($banner_1_imags as $key => $value)
                        <div class="col-xl col-md-6">
                            <div class="mb-3 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner1_links'), true)[$key] }}"
                                    class="d-block text-reset">
                                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($banner_1_imags[$key]) }}"
                                        alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- @include('frontend.partials.featured_categories') --}}
    @foreach ($products_arr as $name => $item)
        <div class="loads">
            <div id="section_newest" class="mt-3">
                <section class="mb-4">
                    <div class="container">
                        <div class="px-2 py-4 px-md-4 py-md-3 shadow-sm rounded" style="background-color: #dbdbdb">
                            <div class="d-flex mb-3 align-items-baseline border-bottom">
                                <h3 class="h5 fw-700 mb-0">
                                    <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">
                                        {{ $item->category_name }}
                                    </span>
                                </h3>
                                <a href="{{ route('products.category', $item->products[0]->cat_id) }}" class="ml-auto mr-0 btn btn-primary btn-sm shadow-md w-md-auto">{{ translate('View More') }}</a>
                            </div>
                            <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5"
                                data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true'>
                                @foreach ($item->products as $key => $new_product)
                                    {{-- @if ($new_product->price && $new_product->price->min !== null && $new_product->price->min !== 0) --}}
                                    <div class="carousel-box">
                                        @include('frontend.partials.product_box_1', [
                                            'product' => $new_product,
                                        ])
                                    </div>
                                    {{-- @endif --}}
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    @endforeach




    {{-- Banner Section 2 --}}
    @if (get_setting('home_banner2_images') != null)
        <div class="mb-4">
            <div class="container">
                <div class="row gutters-10">
                    @php $banner_2_imags = json_decode(get_setting('home_banner2_images')); @endphp
                    @foreach ($banner_2_imags as $key => $value)
                        <div class="col-xl col-md-6">
                            <div class="mb-3 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner2_links'), true)[$key] }}"
                                    class="d-block text-reset">
                                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($banner_2_imags[$key]) }}"
                                        alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Category wise Products --}}
    {{-- <div id="section_home_categories">

    </div> --}}
    {{-- Banner Section 2 --}}
    @if (get_setting('home_banner3_images') != null)
        <div class="mb-4">
            <div class="container">
                <div class="row gutters-10">
                    @php $banner_3_imags = json_decode(get_setting('home_banner3_images')); @endphp
                    @foreach ($banner_3_imags as $key => $value)
                        <div class="col-xl col-md-6">
                            <div class="mb-3 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner3_links'), true)[$key] }}"
                                    class="d-block text-reset">
                                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($banner_3_imags[$key]) }}"
                                        alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Top 10 categories and Brands --}}
    @if (1 == 2)
        {{-- @if (get_setting('top10_categories') != null && get_setting('top10_brands') != null) --}}
        <section class="mb-4">
            <div class="container">
                <div class="row gutters-10">
                    @if (get_setting('top10_categories') != null)
                        <div class="col-lg-6">
                            <div class="d-flex mb-3 align-items-baseline border-bottom">
                                <h3 class="h5 fw-700 mb-0">
                                    <span
                                        class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Top 10 Categories') }}</span>
                                </h3>
                                <a href="{{ route('categories.all') }}"
                                    class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('View All Categories') }}</a>
                            </div>
                            <div class="row gutters-5">
                                @php $top10_categories = json_decode(get_setting('top10_categories')); @endphp
                                @foreach ($top10_categories as $key => $value)
                                    @php $category = \App\Models\Category::find($value); @endphp
                                    @if ($category != null)
                                        <div class="col-sm-6">
                                            <a href="{{ route('products.category', $category->slug) }}"
                                                class="bg-white border d-block text-reset rounded p-2 hov-shadow-md mb-2">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-3 text-center">
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                            data-src="{{ uploaded_asset($category->banner) }}"
                                                            alt="{{ $category->getTranslation('name') }}"
                                                            class="img-fluid img lazyload h-60px"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                    </div>
                                                    <div class="col-7">
                                                        <div class="text-truncat-2 pl-3 fs-14 fw-600 text-left">
                                                            {{ $category->getTranslation('name') }}</div>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <i class="la la-angle-right text-primary"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (get_setting('top10_brands') != null)
                        <div class="col-lg-6">
                            <div class="d-flex mb-3 align-items-baseline border-bottom">
                                <h3 class="h5 fw-700 mb-0">
                                    <span
                                        class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Top 10 Brands') }}</span>
                                </h3>
                                <a href="{{ route('brands.all') }}"
                                    class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('View All Brands') }}</a>
                            </div>
                            <div class="row gutters-5">
                                @php $top10_brands = json_decode(get_setting('top10_brands')); @endphp
                                @foreach ($top10_brands as $key => $value)
                                    @php $brand = \App\Models\Brand::find($value); @endphp
                                    @if ($brand != null)
                                        <div class="col-sm-6">
                                            <a href="{{ route('products.brand', $brand->slug) }}"
                                                class="bg-white border d-block text-reset rounded p-2 hov-shadow-md mb-2">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-4 text-center">
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                            data-src="{{ uploaded_asset($brand->logo) }}"
                                                            alt="{{ $brand->getTranslation('name') }}"
                                                            class="img-fluid img lazyload h-60px"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-truncate-2 pl-3 fs-14 fw-600 text-left">
                                                            {{ $brand->getTranslation('name') }}</div>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <i class="la la-angle-right text-primary"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const container = document.querySelector('.container');
            const section = $('#section_newest');

            function getRndInteger(min, max) {
                return Math.floor(Math.random() * (max - min)) + min;
            }

            function createElementFromHTML(htmlString) {
                var div = document.createElement('div');
                div.innerHTML = htmlString.trim();

                // Change this to div.childNodes to support multiple top-level nodes.
                return div.firstChild;
            }

            const URL = '{{ route('home.category') }}'

            function loadImages(numImages = 1) {
                for (let i = 0; i < numImages; i++) {
                    fetch(URL + '/' + getRndInteger(1800, 2300))
                        .then(response => response.text())
                        .then(data => {
                            element = createElementFromHTML(data)
                            $(element).hide().appendTo('#section_newest').fadeIn(1000);
                            AIZ.plugins.slickCarousel();
                        })
                    i++;
                }
            }
            loadImages(1);
            // listen for scroll event and load more images if we reach the bottom of window
            window.addEventListener('scroll', () => {
                if (window.scrollY + window.innerHeight >= (document.documentElement.scrollHeight * 0.60)) {
                    loadImages();
                }
            })
            // $.post('{{ route('home.section.featured') }}', {
            //     _token: '{{ csrf_token() }}'
            // }, function(data) {
            //     $('#section_featured').html(data);
            //     AIZ.plugins.slickCarousel();
            // });
            // $.post('{{ route('home.section.best_selling') }}', {
            //     _token: '{{ csrf_token() }}'
            // }, function(data) {
            //     $('#section_best_selling').html(data);
            //     AIZ.plugins.slickCarousel();
            // });
            // $.post('{{ route('home.section.auction_products') }}', {
            //     _token: '{{ csrf_token() }}'
            // }, function(data) {
            //     $('#auction_products').html(data);
            //     AIZ.plugins.slickCarousel();
            // });
            // $.post('{{ route('home.section.home_categories') }}', {
            //     _token: '{{ csrf_token() }}'
            // }, function(data) {
            //     $('#section_home_categories').html(data);
            //     AIZ.plugins.slickCarousel();
            // });
            // $.post('{{ route('home.section.best_sellers') }}', {
            //     _token: '{{ csrf_token() }}'
            // }, function(data) {
            //     $('#section_best_sellers').html(data);
            //     AIZ.plugins.slickCarousel();
            // });
        });
    </script>
@endsection
