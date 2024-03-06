<div class="aiz-card-box border border-light bg-white rounded hov-shadow-md mt-1 mb-2 has-transition">
    <div class="position-relative">
        <a href="{{ route('product', ['asin' => $product->asin]) }}" class="d-block">
            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ $product->image ?? '' }}"
                alt="{{ $product->title }}"
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
        </a>
        {{-- <div class="absolute-top-right aiz-p-hov-icon">
            <a href="javascript:void(0)" onclick="addToWishList('{{ $product->asin }}', 'amazon')" data-toggle="tooltip" data-title="{{ translate('Add to wishlist') }}" data-placement="left">
                <i class="la la-heart-o"></i>
            </a>
            <a href="javascript:void(0)" onclick="addToCompare('{{ $product->asin }}', 'amazon')" data-toggle="tooltip" data-title="{{ translate('Add to compare') }}" data-placement="left">
                <i class="las la-sync"></i>
            </a>
            <a href="javascript:void(0)" onclick="showAddToCartModal('{{ $product->asin }}', 'amazon')" data-toggle="tooltip" data-title="{{ translate('Add to cart') }}" data-placement="left">
                <i class="las la-shopping-cart"></i>
            </a>
        </div> --}}
    </div>
    <div class="p-md-3 p-2 text-left">
        <div class="fs-15">
            <span class="fw-700 text-primary">৳{{ amazon_get_price($product->price->min) }}.00</span>
        </div>
        <div class="rating rating-sm mt-1">
            @if ($product->reviews !== null)
                {{ renderStarRating($product->reviews->avg_rating) }}
            @else
                {{ renderStarRating(0.00) }}
            @endif
        </div>
        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
            <a href="{{ route('product', ['asin' => $product->asin]) }}"
                class="d-block text-reset">{{ $product->title }}</a>
        </h3>
    </div>
</div>
