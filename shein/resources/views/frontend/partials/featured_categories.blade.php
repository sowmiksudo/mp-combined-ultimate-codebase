@if (get_categories() > 0)
<div class="container">
    <div class="mx-2 p-3 mb-4 text-center rounded" style="background-color: #dbdbdb">
        <a href="{{ route('categories.all') }}"><span class="">Browser all categories by Amazon ></span></a>
        <ul class="list-unstyled mb-0 row gutters-5">
            @foreach (collect(get_categories())->shuffle()->take(4) as $key => $category)
                <li class="minw-0 col-4 col-md mt-3">
                    <a href="{{ route('products.category', $category->node_id) }}" class="d-block rounded bg-white p-2 text-reset shadow-sm">
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
