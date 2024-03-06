@extends('frontend.layouts.app')

@section('meta_title'){{ $product->goods_name }}@stop

@section('meta_description'){{ $product->goods_name }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $product->goods_name }}">
    <meta itemprop="description" content="{{ $product->goods_name }}">
    <meta itemprop="image" content="{{ $product->goods_img }}">
    <meta property="og:site_name" content="{{ get_setting('meta_title') }}" />
    <meta property="og:price:amount" content="{{ amazon_get_price($product->sale_price->amount) }}" />
    <meta property="product:price:currency"
        content="{{ \App\Models\Currency::findOrFail(get_setting('system_default_currency'))->code }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
@endsection

@section('content')
    <section class="mb-4 pt-3">
        <div class="container">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="row">
                    <div class="col-xl-5 col-lg-6 mb-4">
                        <div class="sticky-top z-3 row gutters-10">
                            @php
                                // $arr = (array)$product
                                $photos = [];
                                foreach ((array) $product->nowater_gallery->detail_image as $info) {
                                    foreach ((array) $info as $key => $value) {
                                        array_push($photos, $value);
                                        // var_dump($value);
                                    }
                                }
                            @endphp
                            <div class="col order-1 order-md-2">
                                <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb'
                                    data-fade='true' data-auto-height='true'>
                                    @foreach ($photos as $key => $photo)
                                        <div class="carousel-box img-zoom rounded">
                                            <img class="img-fluid lazyload"
                                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ $photo }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                        </div>
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
                                                data-src="{{ $photo }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7 col-lg-6">
                        <div class="text-left">
                            <h1 class="mb-2 fs-20 fw-600">
                                {{ $product->goods_name }}
                            </h1>
                            @php
                                $reviews = shein_get_review($product->goods_id, $product->goods_sn, $product->productRelationID);
                            @endphp
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <span class="rating">
                                        {{ renderStarRating($reviews->info->comments_overview->comment_rank_average ?? 0) }}
                                        {{ $reviews->info->comments_overview->comment_rank_average ?? 0 }} from
                                        {{ $reviews->info->comments_overview->comment_num ?? 0 }} reviews
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <small class="mr-2 opacity-50">{{ translate('Ships from') }}: </small><br>
                                    <a href="{{ route('home') }}" class="text-reset">Shein.com</a>
                                </div>
                                @if (get_setting('conversation_system') == 1)
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-soft-primary"
                                            onclick="show_chat_modal()">{{ translate('Message Seller') }}</button>
                                    </div>
                                @endif
                                <div class="col-auto">
                                    <p>
                                        Brand: {{ $product->brand_badge }}
                                    </p>
                                </div>
                            </div>
                            <hr>
                            @if ($product->retail_price->amount != $product->sale_price->amount)
                                <div class="row no-gutters mt-3">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">{{ translate('Price') }}:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="fs-20 opacity-60">
                                            <del>
                                                ৳{{ amazon_get_price($product->retail_price->amount) }}
                                                <span>/item</span>
                                            </del>
                                        </div>
                                    </div>
                                </div>

                                <div class="row no-gutters my-2">
                                    <div class="col-sm-2">
                                        <div class="opacity-50">{{ translate('Discount Price') }}:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="">
                                            <strong class="h2 fw-600 text-primary">
                                                ৳{{ amazon_get_price($product->sale_price->amount) }}
                                            </strong>
                                            <span class="opacity-70">/item</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row no-gutters mt-3">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">{{ translate('Price') }}:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="">
                                            <strong class="h2 fw-600 text-primary">
                                                ৳{{ amazon_get_price($product->retail_price->amount) }}
                                            </strong>
                                            <span class="opacity-70">/unit</span>
                                            {{-- @if ($product->unit != null)
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (true)
                                <div class="row no-gutters mt-4">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">{{ translate('Club Point') }}:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="d-inline-block rounded px-2 bg-soft-primary border-soft-primary border">
                                            <span
                                                class="strong-700">{{ amazon_get_price($product->sale_price->amount ?? amazon_get_price($product->retail_price->amount)) * club_point_rate() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (isset($product->mainSaleAttribute) && count($product->mainSaleAttribute->info) > 0)
                                <div class="row no-gutters">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">{{ translate('Color') }}:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="aiz-radio-inline">
                                            @foreach ($product->mainSaleAttribute->info as $key => $color)
                                                <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip"
                                                    data-title="{{ $color->attr_value }}">
                                                    <a href="{{ route('product', $color->goods_id) }}">
                                                        <span name="color" value="{{ $color->attr_value }}"
                                                            @once checked @endonce>

                                                            <span
                                                                class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                                <img height="20" src="{{ $color->goods_color_image }}"
                                                                    alt="">
                                                            </span>
                                                    </a>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-xl-9 order-0 order-xl-1">
                                <div class="bg-white mb-3 shadow-sm rounded">
                                    <div class="nav border-bottom aiz-nav-tabs">
                                        <a href="#tab_default_1" data-toggle="tab"
                                            class="p-3 fs-16 fw-600 text-reset active show">{{ translate('Description') }}</a>
                                        @if (isset($product->attrSizeDict))
                                            <a href="#tab_default_2" data-toggle="tab"
                                                class="p-3 fs-16 fw-600 text-reset">{{ translate('Size Table') }}</a>
                                        @endif
                                    </div>
                                    <di class="tab-content pt-0">
                                        <div class="tab-pane fade active show" id="tab_default_1">
                                            <div class="p-4">
                                                <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                                    {{-- {{ implode('<br>', $product->description) }} --}}
                                                    <strong>Product information</strong>
                                                    <ul>
                                                        @forelse ($product->productDetails as $feature)
                                                            <li>{{ $feature->attr_name }}: {{ $feature->attr_value }}</li>
                                                        @empty
                                                            <li>No product details found</li>
                                                        @endforelse
                                                    </ul>
                                                    <strong>Material information</strong>
                                                    <ul>
                                                        @forelse ($product->materialDetails as $feature)
                                                            <li>{{ $feature->attr_name }}: {{ $feature->attr_value }}</li>
                                                        @empty
                                                            <li>No product details found</li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Size tab  --}}
                                        @if (isset($product->attrSizeDict))
                                            <div class="tab-pane fade" id="tab_default_2">
                                                <div class="p-4">
                                                    <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                                        <div class="nav border-bottom aiz-nav-tabs">
                                                            @for ($i = 1; $i <= count((array) $product->attrSizeDict); $i++)
                                                                <a href="#tab_size_{{ $i }}" data-toggle="tab"
                                                                    class="p-3 fs-14 fw-600 text-reset @once active show @endonce">{{ array_keys((array) $product->attrSizeDict)[$i - 1] }}</a>
                                                            @endfor
                                                        </div>

                                                        <div class="tab-content">
                                                            @for ($i = 1; $i <= count((array) $product->attrSizeDict); $i++)
                                                                @php
                                                                    $value = array_values(((array) $product->attrSizeDict))[$i - 1];
                                                                @endphp
                                                                <div class="tab-pane fade @once active show @endonce"
                                                                    id="tab_size_{{ $i }}">
                                                                    <div class="p-4">
                                                                        <table class="table table-bordered">
                                                                            <tbody>
                                                                                @foreach ($value as $item)
                                                                                    <tr>
                                                                                        <td>{{ array_keys((array) $item)[0] }}
                                                                                        </td>
                                                                                        <td>{{ array_values((array) $item)[0] }}
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                </div>


                            </div>

                            <hr>

                            <form id="option-choice-form">
                                @csrf
                                <input type="hidden" name="identifier" value="{{ $product->goods_id }}">
                                <input type="hidden" name="supplier_id" value="{{ \App\Models\Supplier::$SHEIN }}">
                                @if (isset($product->attrSizeDict) && count($product->multiLevelSaleAttribute->sku_list) > 0)
                                    <div class="row no-gutters">
                                        <div class="col-sm-2">
                                            <div class="opacity-50 my-2">
                                                Variations
                                            </div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="aiz-radio-inline">
                                                @foreach ($product->multiLevelSaleAttribute->sku_list as $key => $info)
                                                    @php
                                                        $str = '';
                                                        foreach ($info->sku_sale_attr as $key => $attr) {
                                                            $str .= $attr->attr_name_en . '-' . $attr->attr_value_name_en;
                                                        }
                                                    @endphp
                                                    <label class="aiz-megabox pl-0 mr-2">
                                                        <input type="radio" name="variant"
                                                            value="{{ $str }}::{{ $info->sku_code }}" @once checked @endonce>
                                                        <span
                                                            class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                            {{ $str }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
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
                                                    type="button" data-type="minus" data-field="quantity"
                                                    disabled="">
                                                    <i class="las la-minus"></i>
                                                </button>
                                                <input type="number" name="quantity"
                                                    class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                    placeholder="1" value="1" min="1" max="10"
                                                    lang="en">
                                                <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="plus" data-field="quantity">
                                                    <i class="las la-plus"></i>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                                        <div class="col-sm-2">
                                            <div class="opacity-50 my-2">{{ translate('Total Price') }}:</div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="product-price">
                                                <strong id="chosen_price" class="h4 fw-600 text-primary">

                                                </strong>
                                            </div>
                                        </div>
                                    </div> --}}

                            </form>

                            <div class="mt-3">
                                <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600"
                                    onclick="addToCart()">
                                    <i class="las la-shopping-bag"></i>
                                    <span class="d-none d-md-inline-block"> {{ translate('Add to cart') }}</span>
                                </button>
                                <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                    <i class="la la-shopping-cart"></i> {{ translate('Buy Now') }}
                                </button>
                            </div>
                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    {{-- <!-- Add to wishlist button -->
                                <button type="button" class="btn pl-0 btn-link fw-600"
                                    onclick="addToWishList({{ $product->asin }})">
                                    {{ translate('Add to wishlist') }}
                                </button> --}}
                                    <!-- Add to compare button -->
                                    {{-- <button type="button" class="btn btn-link btn-icon-left fw-600"
                                    onclick="addToCompare({{ $product->asin }})">
                                    {{ translate('Add to compare') }}
                                </button> --}}
                                    @if (Auth::check() &&
                                        addon_is_activated('affiliate_system') &&
                                        (\App\Models\AffiliateOption::where('type', 'product_sharing')->first()->status ||
                                            \App\Models\AffiliateOption::where('type', 'category_wise_affiliate')->first()->status) &&
                                        Auth::user()->affiliate_user != null &&
                                        Auth::user()->affiliate_user->status)
                                        @php
                                            if (Auth::check()) {
                                                if (Auth::user()->referral_code == null) {
                                                    Auth::user()->referral_code = substr(Auth::user()->id . Str::random(10), 0, 10);
                                                    Auth::user()->save();
                                                }
                                                $referral_code = Auth::user()->referral_code;
                                                $referral_code_url = URL::to('/product') . '/' . $product->slug . "?product_referral_code=$referral_code";
                                            }
                                        @endphp
                                        <div>
                                            <button type=button id="ref-cpurl-btn" class="btn btn-sm btn-secondary"
                                                data-attrcpy="{{ translate('Copied') }}" onclick="CopyToClipboard(this)"
                                                data-url="{{ $referral_code_url }}">{{ translate('Copy the Promote Link') }}</button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Banner section 1 --}}
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
                                                                alt="{{ env('APP_NAME') }} promo"
                                                                class="img-fluid lazyload w-100">
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                                                                alt="{{ env('APP_NAME') }} promo"
                                                                class="img-fluid lazyload w-100">
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
                                                                alt="{{ env('APP_NAME') }} promo"
                                                                class="img-fluid lazyload w-100">
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
                                                <img src="{{ static_asset('assets/img/refund-sticker.jpg') }}"
                                                    height="36">
                                            @endif
                                        </a>
                                        <a href="{{ route('returnpolicy') }}" class="ml-2"
                                            target="_blank">{{ translate('View
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
            </div>
        </div>
    </section>


@endsection

@section('modal')
    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5">{{ translate('Any query about this product') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->goods_id }}">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3" name="title"
                                value="{{ $product->goods_name }}" placeholder="{{ translate('Product Name') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required
                                placeholder="{{ translate('Your Question') }}">{{ route('product', ['asin' => $product->goods_id]) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-600">{{ translate('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">{{ translate('Login') }}</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="{{ route('cart.login.submit') }}"
                            method="POST">
                            @csrf
                            {{-- <input type="hidden" name="redirect" value="{{ route('carts') }}"> --}}
                            <div class="form-group">
                                @if (addon_is_activated('otp_system'))
                                    <input type="text"
                                        class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ translate('Email Or Phone') }}"
                                        name="email" id="email">
                                @else
                                    <input type="email"
                                        class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ translate('Email') }}"
                                        name="email">
                                @endif
                                @if (addon_is_activated('otp_system'))
                                    <span class="opacity-60">{{ translate('Use country code before number') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control h-auto form-control-lg"
                                    placeholder="{{ translate('Password') }}">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class=opacity-60>{{ translate('Remember Me') }}</span>
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}"
                                        class="text-reset opacity-60 fs-14">{{ translate('Forgot password?') }}</a>
                                </div>
                            </div>

                            <div class="mb-5">
                                <button type="submit"
                                    class="btn btn-primary btn-block fw-600">{{ translate('Login') }}</button>
                            </div>
                        </form>

                        <div class="text-center mb-3">
                            <p class="text-muted mb-0">{{ translate('Dont have an account?') }}</p>
                            <a href="{{ route('user.registration') }}">{{ translate('Register Now') }}</a>
                        </div>
                        @if (get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1)
                            <div class="separator mb-3">
                                <span class="bg-white px-3 opacity-60">{{ translate('Or Login With') }}</span>
                            </div>
                            <ul class="list-inline social colored text-center mb-5">
                                @if (get_setting('facebook_login') == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                            class="facebook">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (get_setting('google_login') == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                            <i class="lab la-google"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (get_setting('twitter_login') == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'twitter']) }}"
                                            class="twitter">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            getVariantPrice();
        });

        function CopyToClipboard(e) {
            var url = $(e).data('url');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(url).select();
            try {
                document.execCommand("copy");
                AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
            } catch (err) {
                AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
            }
            $temp.remove();
        }

        function show_chat_modal() {
            @if (Auth::check())
                $('#chat_modal').modal('show');
            @else
                $('#login_modal').modal('show');
            @endif
        }

        // Pagination using ajax
        $(window).on('hashchange', function() {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
                if (page == Number.NaN || page <= 0) {
                    return false;
                } else {
                    getQuestions(page);
                }
            }
        });

        $(document).ready(function() {
            $(document).on('click', '.pagination a', function(e) {
                getQuestions($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });
        });

        function getQuestions(page) {
            $.ajax({
                url: '?page=' + page,
                dataType: 'json',
            }).done(function(data) {
                $('.pagination-area').html(data);
                location.hash = page;
            }).fail(function() {
                alert('Something went worng! Questions could not be loaded.');
            });
        }
        // Pagination end
    </script>
@endsection
