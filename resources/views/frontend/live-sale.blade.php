@extends('frontend.layouts.app')

@section('content')
    <section class="pt-4 mb-4">
        <div class="container text-center p-3 bg-white" style="background-color: #dbdbdb">
            <h4 class="m-3">{{ $live->title }}</h4>
            <div>{!! $live->embed_code !!}</div>
            <br>
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-6 alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @auth
                <h6>Hey {{ auth()->user()->name }}, grab the order before stock is out!</h6>
            @endauth
            @guest
                <h5>Place order before stock is out!</h5>
            @endguest
            <div class="row mt-3 justify-content-center">
                @auth
                    <form action="{{ route('live.order') }}" method="post" class="col-lg-4 col-sm-12">
                        @csrf
                        <div class="mb-2">
                            <input type="hidden" name="name" class="form-control" id="name"
                                value="{{ auth()->user()->email }}" placeholder="Your name">
                        </div>
                        @if (auth()->user()->phone === null)
                            <div class="mb-2">
                                <input type="number" name="phone" class="form-control" id="phone"
                                    placeholder="Phone number">
                                <div class="form-text text-secondary">Please provide your phone number so that we can call you
                                    later
                                    and confirm your order.</div>
                            </div>
                        @else
                            <div class="mb-2">
                                <input type="hidden" value="{{ auth()->user()->phone }}" name="phone" class="form-control"
                                    id="phone" placeholder="Phone number">
                                <div class="form-text text-secondary">Please provide your phone number so that we can call you
                                    later
                                    and confirm your order.</div>
                            </div>
                        @endif
                        <div class="mb-2">
                            <textarea type="text" name="address" class="form-control" id="address" placeholder="Your address"></textarea>
                        </div>
                        <input type="hidden" name="live_sale_id" value="{{ $live->id }}">
                        <button type="submit" class="btn btn-primary btn-lg">Place order</button>
                    </form>
                @endauth
                @guest
                    <form action="{{ route('live.order') }}" method="post" class="col-lg-4 col-sm-12">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Your name">
                        </div>
                        <div class="mb-2">
                            <input type="number" name="phone" class="form-control" id="phone" placeholder="Phone number">
                            <div class="form-text text-secondary">Please provide your phone number so that we can call you later
                                and confirm your order.</div>
                        </div>
                        <div class="mb-2">
                            <textarea type="text" name="address" class="form-control" id="address" placeholder="Your address"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Place order</button>
                        <input type="hidden" name="live_sale_id" value="{{ $live->id }}">
                        <p class="mt-1 text-secondary">Or simply login to your account <a href="{{ route('login') }}">here</a>
                        </p>
                    </form>
                @endguest
            </div>
            {{-- <div class="product_show mt-3">
            <div class="row">
                <div class="col-xl-5 col-lg-6 mb-4">
                    <div class="sticky-top z-3 row gutters-10">
                        @php
                        $photos = explode(',', $detailedProduct->photos);
                        @endphp
                        <div class="col order-1 order-md-2">
                            <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb'
                                data-fade='true' data-auto-height='true'>
                                @foreach ($photos as $key => $photo)
                                <div class="carousel-box img-zoom rounded">
                                    <img class="img-fluid lazyload"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($photo) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                @endforeach
                                @foreach ($detailedProduct->stocks as $key => $stock)
                                @if ($stock->image != null)
                                <div class="carousel-box img-zoom rounded">
                                    <img class="img-fluid lazyload"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 col-md-auto w-md-80px order-2 order-md-1 mt-3 mt-md-0">
                            <div class="aiz-carousel product-gallery-thumb" data-items='5'
                                data-nav-for='.product-gallery' data-vertical='true' data-vertical-sm='false'
                                data-focus-select='true' data-arrows='true'>
                                @foreach ($photos as $key => $photo)
                                <div class="carousel-box c-pointer border p-1 rounded">
                                    <img class="lazyload mw-100 size-50px mx-auto"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($photo) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                @endforeach
                                @foreach ($detailedProduct->stocks as $key => $stock)
                                @if ($stock->image != null)
                                <div class="carousel-box c-pointer border p-1 rounded"
                                    data-variation="{{ $stock->variant }}">
                                    <img class="lazyload mw-100 size-50px mx-auto"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-lg-6">
                    <div class="text-left">
                        <h1 class="mb-2 fs-20 fw-600">
                            {{ $detailedProduct->getTranslation('name') }}
                        </h1>

                        <div class="row align-items-center">
                            <div class="col-12">
                                @php
                                $total = 0;
                                $total += $detailedProduct->reviews->count();
                                @endphp
                                <span class="rating">
                                    {{ renderStarRating($detailedProduct->rating) }}
                                </span>
                                <span class="ml-1 opacity-50">({{ $total }}
                                    {{ translate('reviews') }})</span>
                            </div>
                            @if ($detailedProduct->est_shipping_days)
                            <div class="col-auto ml">
                                <small class="mr-2 opacity-50">{{ translate('Estimate Shipping Time') }}:
                                </small>{{ $detailedProduct->est_shipping_days }} {{ translate('Days') }}
                            </div>
                            @endif
                        </div>

                        <hr>

                        <div class="row align-items-center">
                            <div class="col-auto">
                                <small class="mr-2 opacity-50">{{ translate('Sold by') }}: </small><br>
                                @if ($detailedProduct->added_by == 'seller' && get_setting('vendor_system_activation') == 1)
                                <a href="{{ route('shop.visit', $detailedProduct->user->shop->slug) }}"
                                    class="text-reset">{{ $detailedProduct->user->shop->name }}</a>
                                @else
                                {{ translate('Inhouse product') }}
                                @endif
                            </div>
                            @if (get_setting('conversation_system') == 1)
                            <div class="col-auto">
                                <button class="btn btn-sm btn-soft-primary" onclick="show_chat_modal()">{{
                                    translate('Message Seller') }}</button>
                            </div>
                            @endif

                            @if ($detailedProduct->brand != null)
                            <div class="col-auto">
                                <a href="{{ route('products.brand', $detailedProduct->brand->slug) }}">
                                    <img src="{{ uploaded_asset($detailedProduct->brand->logo) }}"
                                        alt="{{ $detailedProduct->brand->getTranslation('name') }}" height="30">
                                </a>
                            </div>
                            @endif
                        </div>

                        <hr>


                        @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                        <div class="row no-gutters mt-4">
                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">{{ translate('Club Point') }}:</div>
                            </div>
                            <div class="col-sm-10">
                                <div class="d-inline-block rounded px-2 bg-soft-primary border-soft-primary border">
                                    <span class="strong-700">{{ $detailedProduct->earn_point }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <hr>

                        <form id="option-choice-form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $detailedProduct->id }}">

                            @if ($detailedProduct->choice_options != null)
                            @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                            <div class="row no-gutters">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">
                                        {{ \App\Models\Attribute::find($choice->attribute_id)->getTranslation('name')
                                        }}:
                                    </div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="aiz-radio-inline">
                                        @foreach ($choice->values as $key => $value)
                                        <label class="aiz-megabox pl-0 mr-2">
                                            <input type="radio" name="attribute_id_{{ $choice->attribute_id }}"
                                                value="{{ $value }}" @if ($key == 0) checked @endif>
                                            <span
                                                class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                {{ $value }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif

                            @if (count(json_decode($detailedProduct->colors)) > 0)
                            <div class="row no-gutters">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">{{ translate('Color') }}:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="aiz-radio-inline">
                                        @foreach (json_decode($detailedProduct->colors) as $key => $color)
                                        <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip"
                                            data-title="{{ \App\Models\Color::where('code', $color)->first()->name }}">
                                            <input type="radio" name="color"
                                                value="{{ \App\Models\Color::where('code', $color)->first()->name }}"
                                                @if ($key == 0) checked @endif>
                                            <span
                                                class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                <span class="size-30px d-inline-block rounded"
                                                    style="background: {{ $color }};"></span>
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <hr>
                            @endif

                            <!-- Quantity + Add to cart -->
                            <div class="row no-gutters">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">{{ translate('Quantity') }}:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="product-quantity d-flex align-items-center">
                                        <div class="row no-gutters align-items-center aiz-plus-minus mr-3"
                                            style="width: 130px;">
                                            <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                type="button" data-type="minus" data-field="quantity" disabled="">
                                                <i class="las la-minus"></i>
                                            </button>
                                            <input type="number" name="quantity"
                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                placeholder="1" value="{{ $detailedProduct->min_qty }}"
                                                min="{{ $detailedProduct->min_qty }}" max="10" lang="en">
                                            <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light"
                                                type="button" data-type="plus" data-field="quantity">
                                                <i class="las la-plus"></i>
                                            </button>
                                        </div>
                                        @php
                                        $qty = 0;
                                        foreach ($detailedProduct->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                        }
                                        @endphp
                                        <div class="avialable-amount opacity-60">
                                            @if ($detailedProduct->stock_visibility_state == 'quantity')
                                            (<span id="available-quantity">{{ $qty }}</span>
                                            {{ translate('available') }})
                                            @elseif($detailedProduct->stock_visibility_state == 'text' && $qty >= 1)
                                            (<span id="available-quantity">{{ translate('In Stock') }}</span>)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">{{ translate('Total Price') }}:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="product-price">
                                        <strong id="chosen_price" class="h4 fw-600 text-primary">

                                        </strong>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <div class="mt-3">
                            @if ($detailedProduct->external_link != null)
                            <a type="button" class="btn btn-primary buy-now fw-600"
                                href="{{ $detailedProduct->external_link }}">
                                <i class="la la-share"></i> {{ translate($detailedProduct->external_link_btn) }}
                            </a>
                            @else
                            <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                <i class="la la-shopping-cart"></i> {{ translate('Buy Now') }}
                            </button>
                            @endif
                            <button type="button" class="btn btn-secondary out-of-stock fw-600 d-none" disabled>
                                <i class="la la-cart-arrow-down"></i> {{ translate('Out of Stock') }}
                            </button>
                        </div>
                        <div class="d-table width-100 mt-3">
                            <div class="d-table-cell">
                                @if (Auth::check() && addon_is_activated('affiliate_system') && (\App\Models\AffiliateOption::where('type', 'product_sharing')->first()->status || \App\Models\AffiliateOption::where('type', 'category_wise_affiliate')->first()->status) && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)
                                @php
                                if (Auth::check()) {
                                if (Auth::user()->referral_code == null) {
                                Auth::user()->referral_code = substr(Auth::user()->id . Str::random(10), 0, 10);
                                Auth::user()->save();
                                }
                                $referral_code = Auth::user()->referral_code;
                                $referral_code_url = URL::to('/product') . '/' . $detailedProduct->slug .
                                "?product_referral_code=$referral_code";
                                }
                                @endphp
                                <div>
                                    <button type=button id="ref-cpurl-btn" class="btn btn-sm btn-secondary"
                                        data-attrcpy="{{ translate('Copied') }}" onclick="CopyToClipboard(this)"
                                        data-url="{{ $referral_code_url }}">{{ translate('Copy the Promote Link')
                                        }}</button>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if (get_setting('home_banner1_images') != null)
                        <div class="mb-4 mt-5">
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


                        @php
                        $refund_sticker = get_setting('refund_sticker');
                        @endphp
                        @if (addon_is_activated('refund_request'))
                        <div class="row no-gutters mt-3">
                            <div class="col-2">
                                <div class="opacity-50 mt-2">{{ translate('Refund') }}:</div>
                            </div>
                            <div class="col-10">
                                <a href="{{ route('returnpolicy') }}" target="_blank">
                                    @if ($refund_sticker != null)
                                    <img src="{{ uploaded_asset($refund_sticker) }}" height="36">
                                    @else
                                    <img src="{{ static_asset('assets/img/refund-sticker.jpg') }}" height="36">
                                    @endif
                                </a>
                                <a href="{{ route('returnpolicy') }}" class="ml-2" target="_blank">{{ translate('View
                                    Policy') }}</a>
                            </div>
                        </div>
                        @endif
                        <div class="row no-gutters mt-4">
                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">{{ translate('Share') }}:</div>
                            </div>
                            <div class="col-sm-10">
                                <div class="aiz-share"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        </div>
    </section>
@endsection
