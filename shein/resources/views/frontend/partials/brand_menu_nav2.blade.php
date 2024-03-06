<div class="aiz-category-menu bg-white rounded shadow-sm" id="brands-sidebar">
    <ul class="list-unstyled no-scrollbar py-2 mb-0 text-left">
        @php
            $top10_brands = \App\Models\Brand::all()
                ->shuffle()
                ->take(10);
        @endphp
        @foreach ($top10_brands as $key => $brand)
            @if ($brand != null)
                <li class="category-nav-element" data-id="{{ $brand->id }}">
                    <a href="{{ route('products.brand', $brand->slug) }}"
                        class="text-reset text-dark rounded fs-12 fw-600 text-uppercase py-2 px-3 d-block">
                        <img class="cat-image lazyload mr-2 opacity-60"
                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                            data-src="{{ uploaded_asset($brand->logo) }}" width="25"
                            alt="{{ $brand->getTranslation('name') }}"
                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        <span class="">{{ $brand->getTranslation('name') }}</span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
