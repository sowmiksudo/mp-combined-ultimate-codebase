@php
    $random = bin2hex(random_bytes(5));
@endphp
<div class="aiz-card-box border border-light bg-white rounded hov-shadow-md mt-1 mb-2 has-transition">
    <div class="position-relative">
        <a href="{{ route('in-product', ['pid'=>$product->goods_id, 'sid'=>\App\Models\Supplier::$SHEIN]) }}" class="d-block">
            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                data-src="{{ $product->goods_thumb ?? ($product->goods_img ?? '') }}" alt="{{ $product->goods_name }}"
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
        </a>
    </div>
    <div class="p-md-3 p-2 text-left">
        <div class="rating" id="rating-{{ $random }}">
            {{ renderStarRating($product->comment_rank_average) }}
        </div>
        <div class="fs-15">
            <span class="fw-700 text-primary">৳{{ get_price(["info"=>(object)array("info"=>$product), "sid" => \App\Models\Supplier::$SHEIN]) }}</span>
        </div>
        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
            <a href="{{ route('product', $product->goods_id) }}"
                class="d-block text-reset">{{ $product->goods_name }}</a>
        </h3>
        @if (addon_is_activated('club_point'))
        <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
            {{ translate('Club Point') }}:
            <span class="fw-700 float-right">{{ get_price(["info"=>(object)array("info"=>$product), "sid" => \App\Models\Supplier::$SHEIN]) * club_points_rate() }}</span>
        </div>
    @endif
    </div>

</div>
