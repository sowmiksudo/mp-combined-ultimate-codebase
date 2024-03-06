@extends('frontend.layouts.app')

@section('content')

    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                            </div>
                        </div>
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-map"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info') }}</h3>
                            </div>
                        </div>
                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-truck"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Delivery info') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('4. Payment') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4 gry-bg">
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 col-xl-10 mx-auto">
                    <form class="form-default" action="{{ route('checkout.store_delivery_info') }}" role="form"
                        method="POST">
                        @csrf
                        @php
                            $admin_products = [];
                            $international_products = [];
                            foreach ($carts as $key => $cartItem) {
                                // $product = get_product_details($cartItem['product_identifier'], $cartItem['supplier_id']);

                                if ($cartItem['supplier_id'] == \App\Models\Supplier::$INHOUSE) {
                                    array_push($admin_products, $cartItem['product_identifier']);
                                } else {
                                    if (isset($international_products[$cartItem['supplier_id']])) {
                                        $arr = $international_products[$cartItem['supplier_id']];
                                        array_push($arr, $cartItem['product_identifier']);
                                        $international_products[$cartItem['supplier_id']] = $arr;
                                    } else {
                                        $international_products[$cartItem['supplier_id']] = [
                                            $cartItem['product_identifier']
                                        ];
                                    }
                                    // array_push($product_ids, $cartItem['product_identifier']);
                                }
                            }

                            $pickup_point_list = [];
                            if (get_setting('pickup_point') == 1) {
                                $pickup_point_list = \App\Models\PickupPoint::where('pick_up_status', 1)->get();
                            }
                        @endphp
                        @if (!empty($international_products))
                        <div class="alert alert-warning mb-3 rounded">
                            <strong>Note:</strong> Cash on delivery is only available on Inhouse products. If you want to purchase by cash on delivery, please keep only inhouse products in your cart.
                        </div>
                        @endif
                        @if (!empty($admin_products))
                            <div class="card mb-3 shadow-sm border-0 rounded">
                                <div class="card-header p-3">
                                    <h5 class="fs-16 fw-600 mb-0">{{ translate('Inhouse Products') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($admin_products as $key => $identifier)
                                            @php
                                                $product = \App\Models\Product::find($identifier);
                                            @endphp
                                            <li class="list-group-item">
                                                <div class="d-flex">
                                                    <span class="mr-2">
                                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                            class="img-fit size-60px rounded"
                                                            alt="{{ $product->getTranslation('name') }}">
                                                    </span>
                                                    <span
                                                        class="fs-14 opacity-60">{{ $product->getTranslation('name') }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="row border-top pt-3">
                                        <div class="col-md-6">
                                            <h6 class="fs-15 fw-600">{{ translate('Choose Delivery Type') }}</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row gutters-5">
                                                @if (get_setting('shipping_type') != 'carrier_wise_shipping')
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio"
                                                                name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                                value="home_delivery"
                                                                onchange="show_pickup_point(this, 'admin')"
                                                                data-target=".pickup_point_id_admin" checked>
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span
                                                                    class="flex-grow-1 pl-3 fw-600">{{ translate('Home Delivery') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @else
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio"
                                                                name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                                value="carrier" onchange="show_pickup_point(this, 'admin')"
                                                                data-target=".pickup_point_id_admin" checked>
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span
                                                                    class="flex-grow-1 pl-3 fw-600">{{ translate('Carrier') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif

                                                @if ($pickup_point_list)
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio"
                                                                name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                                value="pickup_point"
                                                                onchange="show_pickup_point(this, 'admin')"
                                                                data-target=".pickup_point_id_admin">
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span
                                                                    class="flex-grow-1 pl-3 fw-600">{{ translate('Local Pickup') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif

                                            </div>
                                            @if ($pickup_point_list)
                                                <div class="mt-4 pickup_point_id_admin d-none">
                                                    <select class="form-control aiz-selectpicker"
                                                        name="pickup_point_id_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                        data-live-search="true">
                                                        <option>{{ translate('Select your nearest pickup point') }}
                                                        </option>
                                                        @foreach ($pickup_point_list as $pick_up_point)
                                                            <option value="{{ $pick_up_point->id }}"
                                                                data-content="<span class='d-block'>
                                                                    <span class='d-block fs-16 fw-600 mb-2'>{{ $pick_up_point->getTranslation('name') }}</span>
                                                                    <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ $pick_up_point->getTranslation('address') }}</span>
                                                                    <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i>{{ $pick_up_point->phone }}</span>
                                                                </span>">
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if (get_setting('shipping_type') == 'carrier_wise_shipping')
                                        <div class="row pt-3 carrier_id_admin">
                                            @foreach ($carrier_list as $carrier_key => $carrier)
                                                <div class="col-md-12 mb-2">
                                                    <label class="aiz-megabox d-block bg-white mb-0">
                                                        <input type="radio"
                                                            name="carrier_id_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                            value="{{ $carrier->id }}"
                                                            @if ($carrier_key == 0) checked @endif>
                                                        <span class="d-flex p-3 aiz-megabox-elem">
                                                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                            <span class="flex-grow-1 pl-3 fw-600">
                                                                <img src="{{ uploaded_asset($carrier->logo) }}"
                                                                    alt="Image" class="w-50px img-fit">
                                                            </span>
                                                            <span
                                                                class="flex-grow-1 pl-3 fw-700">{{ $carrier->name }}</span>
                                                            <span
                                                                class="flex-grow-1 pl-3 fw-600">{{ translate('Transit in') . ' ' . $carrier->transit_time . ' ' . translate('days') }}</span>
                                                            {{-- <span class="flex-grow-1 pl-3 fw-600">{{ Str::headline($carrier->carrier_ranges->first()->billing_type) }}</span> --}}
                                                            <span
                                                                class="flex-grow-1 pl-3 fw-600">{{ single_price(carrier_base_price($carts, $carrier->id, \App\Models\User::where('user_type', 'admin')->first()->id)) }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if (!empty($international_products))
                            @foreach ($international_products as $supplier => $product_identifiers)
                                <div class="card mb-3 shadow-sm border-0 rounded">
                                    <div class="card-header p-3">
                                        <h5 class="fs-16 fw-600 mb-0">
                                            {{ ucfirst(\App\Models\Supplier::find($supplier)->name) }}
                                            {{ translate('Products') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            @foreach ($product_identifiers as $identifier)
                                                @php
                                                    $product = get_product_details($identifier, $supplier);
                                                @endphp
                                                <li class="list-group-item">
                                                    <div class="d-flex">
                                                        <span class="mr-2">
                                                            <img src="{{ get_thumbnail($product) }}"
                                                                class="img-fit size-60px rounded"
                                                                alt="{{ get_name($product) }}">
                                                        </span>
                                                        <span class="fs-14 opacity-60">{{ get_name($product) }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="row border-top pt-3">
                                            <div class="col-md-6">
                                                <h6 class="fs-15 fw-600">{{ translate('Choose Delivery Type') }}</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row gutters-5">
                                                    @if (get_setting('shipping_type') != 'carrier_wise_shipping')
                                                        <div class="col-6">
                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                <input type="radio"
                                                                    name="shipping_type_{{ $supplier }}"
                                                                    value="home_delivery"
                                                                    onchange="show_pickup_point(this, {{ $supplier }})"
                                                                    data-target=".pickup_point_id_{{ $supplier }}"
                                                                    checked>
                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                    <span
                                                                        class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                    <span
                                                                        class="flex-grow-1 pl-3 fw-600">{{ translate('Home Delivery') }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        <div class="col-6">
                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                <input type="radio"
                                                                    name="shipping_type_{{ $supplier }}"
                                                                    value="carrier"
                                                                    onchange="show_pickup_point(this, {{ $supplier }})"
                                                                    data-target=".pickup_point_id_{{ $supplier }}"
                                                                    checked>
                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                    <span
                                                                        class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                    <span
                                                                        class="flex-grow-1 pl-3 fw-600">{{ translate('Carrier') }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endif

                                                    @if ($pickup_point_list)
                                                        <div class="col-6">
                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                <input type="radio"
                                                                    name="shipping_type_{{ $supplier }}"
                                                                    value="pickup_point"
                                                                    onchange="show_pickup_point(this, {{ $supplier }})"
                                                                    data-target=".pickup_point_id_{{ $supplier }}">
                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                    <span
                                                                        class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                    <span
                                                                        class="flex-grow-1 pl-3 fw-600">{{ translate('Local Pickup') }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if ($pickup_point_list)
                                                    <div class="mt-4 pickup_point_id_{{ $supplier }} d-none">
                                                        <select class="form-control aiz-selectpicker"
                                                            name="pickup_point_id_{{ $supplier }}"
                                                            data-live-search="true">
                                                            <option>{{ translate('Select your nearest pickup point') }}
                                                            </option>
                                                            @foreach ($pickup_point_list as $pick_up_point)
                                                                <option value="{{ $pick_up_point->id }}"
                                                                    data-content="<span class='d-block'>
                                                                                <span class='d-block fs-16 fw-600 mb-2'>{{ $pick_up_point->getTranslation('name') }}</span>
                                                                                <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ $pick_up_point->getTranslation('address') }}</span>
                                                                                <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i>{{ $pick_up_point->phone }}</span>
                                                                            </span>">
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if (get_setting('shipping_type') == 'carrier_wise_shipping')
                                            <div class="row pt-3 carrier_id_{{ $supplier }}">
                                                @foreach ($carrier_list as $carrier_key => $carrier)
                                                    <div class="col-md-12 mb-2">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio" name="carrier_id_{{ $key }}"
                                                                value="{{ $carrier->id }}"
                                                                @if ($carrier_key == 0) checked @endif>
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span class="flex-grow-1 pl-3 fw-600">
                                                                    <img src="{{ uploaded_asset($carrier->logo) }}"
                                                                        alt="Image" class="w-50px img-fit">
                                                                </span>
                                                                <span
                                                                    class="flex-grow-1 pl-3 fw-600">{{ $carrier->name }}</span>
                                                                <span
                                                                    class="flex-grow-1 pl-3 fw-600">{{ translate('Transit in') . ' ' . $carrier->transit_time . ' ' . translate('days') }}</span>
                                                                {{-- <span class="flex-grow-1 pl-3 fw-600">{{ Str::headline($carrier->carrier_ranges->first()->billing_type) }}</span> --}}
                                                                <span
                                                                    class="flex-grow-1 pl-3 fw-600">{{ single_price(carrier_base_price($carts, $carrier->id, $key)) }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="pt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('home') }}">
                                <i class="la la-angle-left"></i>
                                {{ translate('Return to shop') }}
                            </a>
                            <button type="submit"
                                class="btn fw-600 btn-primary">{{ translate('Continue to Payment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function display_option(key) {

        }

        function show_pickup_point(el, type) {
            var value = $(el).val();
            var target = $(el).data('target');

            // console.log(value);

            if (value == 'home_delivery' || value == 'carrier') {
                if (!$(target).hasClass('d-none')) {
                    $(target).addClass('d-none');
                }
                $('.carrier_id_' + type).removeClass('d-none');
            } else {
                $(target).removeClass('d-none');
                $('.carrier_id_' + type).addClass('d-none');
            }
        }
    </script>
@endsection
