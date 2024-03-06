<section class="mb-4">
    <div class="container">
        <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">
            <div class="d-flex mb-3 align-items-baseline border-bottom">
                <h3 class="h5 fw-700 mb-0">
                    <span
                        class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ $info->category_name }}
                        {!! request()->ajax() ? '<span class="text-sm text-secondary">(From Shein)</span>' : '' !!}</span>
                </h3>
                {{-- {{ dd(request()->headers->get('referer'), request()->getSchemeAndHttpHost().'/', (request()->headers->get('referer') != request()->getSchemeAndHttpHost())) }} --}}
                @if (!request()->ajax())
                    <a href="{{ route('products.category', $info->products[0]->cat_id) }}"
                        class="ml-auto mr-0 btn btn-primary btn-sm shadow-md w-md-auto">{{ translate('View More') }}</a>
                @else
                    <a href="{{ route('products.category', $info->products[0]->cat_id) }}"
                        class="ml-auto mr-0 btn btn-primary btn-sm shadow-md w-md-auto">{{ translate('Browse Shein') }}</a>
                @endif
            </div>
            <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5" data-lg-items="4"
                data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true'>
                @php
                    $products = collect($info->products)->shuffle();
                @endphp
                @foreach ($products as $key => $product)
                    <div class="carousel-box">
                        @include('frontend.partials.product_box_1', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
