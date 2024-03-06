<div class="modal website-popup removable-session d-none" data-key="website-popup" data-value="removed">
    <div class="absolute-full bg-black opacity-60"></div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md">
        <div class="modal-content position-relative border-0 rounded-0">
            <div class="aiz-editor-data">
                @php
                    $coupons = \App\Models\Coupon::all();
                @endphp
                @foreach ($coupons as $coupon)
                    @if ($coupon->type == 'product_base')
                        @php
                            $products = json_decode($coupon->details);
                            $coupon_products = [];
                            foreach ($products as $product) {
                                array_push($coupon_products, $product->product_id);
                            }
                        @endphp
                    @else
                        @php
                            $order_discount = json_decode($coupon->details);
                        @endphp
                    @endif
                    @php
                        if ($coupon->user->user_type != 'admin') {
                            $shop = $coupon->user->shop;
                            $name = $shop->name;
                        } else {
                            $name = get_setting('website_name');
                        }
                    @endphp
                    @if ($coupon->user->user_type == 'admin' || ($shop->verification_status && $shop != null))
                        <div class="">
                            <div class="bg-white h-100 overflow-hidden" style="border-color: #2d3748 !important">
                                <div class="row no-gutters h-100">
                                    {{-- @if ($coupon->type == 'product_base')
                                        <div class="col-md-8 text-dark align-self-center ">
                                            <div class="px-4 py-3">
                                                <div class="fs-10 mb-3 fw-700 text-truncate">{{ $name }}</div>
                                                <div class="row gutters-5">
                                                    @php $products = App\Models\Product::whereIn('id', $coupon_products)->get(); @endphp
                                                    @foreach ($products as $key => $product)
                                                        @if ($product != null && $key < 3)
                                                            <a href="{{ route('product', $product->slug) }}"
                                                                class='col-4 text-center text-reset mb-3'
                                                                target="_blank">
                                                                <img class="img-fit mw-100 h-90px h-sm-120px h-lg-80px h-xl-100px h-xxl-130px mb-2 rounded"
                                                                    src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                    alt="">

                                                                <div class="lh-1-2">
                                                                    <span
                                                                        class="fw-600 text-primary fs-6">{{ home_discounted_base_price($product) }}</span>
                                                                    @if (home_base_price($product) != home_discounted_base_price($product))
                                                                        <del
                                                                            class="fw-500 opacity-50 fs-4">{{ home_base_price($product) }}</del>
                                                                    @endif
                                                                </div>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 bg-primary d-flex">
                                            <div class="align-self-center p-3 flex-grow-1 text-center">
                                                @if ($coupon->discount_type == 'amount')
                                                    <p class="fs-10 fw-700 mb-1">{{ single_price($coupon->discount) }}
                                                        {{ translate('OFF') }}</p>
                                                @else
                                                    <p class="fs-10 fw-700 mb-1">{{ $coupon->discount }}%
                                                        {{ translate('OFF') }}</p>
                                                @endif
                                                <span class="fs-6 d-block mb-3">
                                                    {{ translate('Code') }}:
                                                    <span class="fw-600"
                                                        id="coupon_code_{{ $coupon->id }}">{{ $coupon->code }}</span>
                                                </span>
                                                <p data-clipboard-target="#coupon_code_{{ $coupon->id }}"
                                                    class="btn btn-sm bg-white fw-700 copybtn">
                                                    Copy Code
                                                </p>
                                            </div>
                                        </div>
                                    @else --}}
                                        <div class="col-md-12 bg-primary d-flex text-white">
                                            <div class="col-md-8 text-white align-self-center">
                                                <div class="px-4 py-3">
                                                    {{-- <div class="fs-10 mb-3 fw-700 text-truncate">{{ $name }}</div> --}}
                                                    <span class="text-center d-block m-auto bg-soft-info p-3 rounded">
                                                        @if ($coupon->discount_type == 'amount')
                                                            {{ translate('Min Spend ') }}
                                                            {{ single_price($order_discount->min_buy) }}
                                                            {{ translate('from') }} {{ $name }}
                                                            {{ translate('to get') }}
                                                            {{ single_price($coupon->discount) }}
                                                            {{ translate('OFF on total orders') }}
                                                        @else
                                                            {{ translate('Min Spend ') }}
                                                            {{ single_price($order_discount->min_buy) }}
                                                            {{ translate('from') }} {{ $name }}
                                                            {{ translate('to get') }} {{ $coupon->discount }}%
                                                            {{ translate('OFF on total orders') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="align-self-center py-0 px-2 flex-grow-1 text-center">
                                                <span class="fw-700">{{ translate('Max Discount') }}:
                                                    {{ single_price($order_discount->max_discount) }}</span>
                                                <span class="fs-6 d-block mb-1">
                                                    {{ translate('Code') }}:
                                                    <span class="fw-600"
                                                        id="coupon_code_{{ $coupon->id }}">{{ $coupon->code }}</span>
                                                </span>
                                                <p data-clipboard-target="#coupon_code_{{ $coupon->id }}"
                                                    class="btn btn-xs bg-white fw-700 copybtn">
                                                    Copy Code
                                                </p>
                                            </div>
                                        </div>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                        @if (!$loop->last)
                        {{-- <div class="border-1 border-black"></div> --}}
                        @endif
                    @endif
                @endforeach
            </div>
            <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session"
                data-key="website-popup" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                <i class="la la-close fs-20"></i>
            </button>
        </div>
    </div>
</div>
