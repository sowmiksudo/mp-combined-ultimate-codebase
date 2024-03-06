<div class="aiz-category-menu bg-white rounded shadow-sm" id="category-sidebar">
    <ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left">
        @foreach (get_categories() as $category)
            <li class="category-nav-element" data-id="{{ $category->id }}">
                <a href="{{ route('products.category', $category->id) }}" class="text-reset text-dark rounded fs-12 fw-600 text-uppercase py-2 px-3 d-block">
                    <img
                        class="cat-image lazyload mr-2 opacity-60"
                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src=""
                        width="16"
                        alt="{{ $category->name }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                    >
                    <span class="">{{ $category->name }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
