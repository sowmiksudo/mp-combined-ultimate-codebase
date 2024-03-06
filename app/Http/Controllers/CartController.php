<?php

namespace App\Http\Controllers;

use Auth;
use Cookie;
use Session;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            if ($request->session()->get('temp_user_id')) {
                Cart::where('temp_user_id', $request->session()->get('temp_user_id'))
                    ->update(
                        [
                            'user_id' => $user_id,
                            'temp_user_id' => null
                        ]
                    );

                Session::forget('temp_user_id');
            }
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            // $carts = Cart::where('temp_user_id', $temp_user_id)->get();
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        }

        return view('frontend.view_cart', compact('carts'));
    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        return view('frontend.partials.addToCart', compact('product'));
    }

    public function showCartModalAuction(Request $request)
    {
        $product = Product::find($request->id);
        return view('auction.frontend.addToCartAuction', compact('product'));
    }

    public function addToCart(Request $request)
    {
        $product = get_product_details($request->identifier, $request->supplier_id);
        $carts = array();
        $data = array();

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $data['user_id'] = $user_id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            if ($request->session()->get('temp_user_id')) {
                $temp_user_id = $request->session()->get('temp_user_id');
            } else {
                $temp_user_id = bin2hex(random_bytes(10));
                $request->session()->put('temp_user_id', $temp_user_id);
            }
            $data['temp_user_id'] = $temp_user_id;
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $data['product_identifier'] = $request->identifier;
        $data['supplier_id'] = $request->supplier_id;
        $str = '';
        $tax = 0;
        if (isset($product['info']->digital) && $product['info']->digital != 1 && $request->quantity < $product['info']->min_qty) {
            return array(
                'status' => 0,
                'cart_count' => count($carts),
                'modal_view' => view('frontend.partials.minQtyNotSatisfied', ['min_qty' => $product['info']->min_qty])->render(),
                'nav_cart_view' => view('frontend.partials.cart')->render(),
            );
        }

        //check the color enabled or disabled for the product
        if ($request->has('color')) {
            $str = $request['color'];
        }

        if ($product['sid'] == Supplier::$INHOUSE) {
            if (isset($product['info']->digital) && $product['info']->digital != 1) {
                //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
                foreach (json_decode(Product::find($request->identifier)->choice_options) as $key => $choice) {
                    if ($str != null) {
                        $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    } else {
                        $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    }
                }
            }
        }

        $data['variation'] = $str;

        if ($product['sid'] == Supplier::$INHOUSE) {
            $product_stock = $product['info']->stocks->where('variant', $str)->first();
            $price = $product_stock->price;
        } else {
            $price = get_price($product);
        }

        if ($product['info']->wholesale_product) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }
        $discount_applicable = false;
        $tax = 0;
        if ($request->supplier_id == Supplier::$INHOUSE) {
            $quantity = $product_stock->qty;

            if ($quantity < $request['quantity']) {
                return array(
                    'status' => 0,
                    'cart_count' => count($carts),
                    'modal_view' => view('frontend.partials.outOfStockCart')->render(),
                    'nav_cart_view' => view('frontend.partials.cart')->render(),
                );
            }

            $price = get_price($product['info']);

            //calculation of taxes
            foreach ($product['info']->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $tax += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $tax += $product_tax->tax;
                }
            }
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product['info']->cash_on_delivery;
            $data['digital'] = $product['info']->digital;

            if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product['info']->id) {
                $data['product_referral_code'] = Cookie::get('product_referral_code');
            }
        } else {
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = false;
            $data['digital'] = false;
        }

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['tax'] = $tax;
        //$data['shipping'] = 0;
        $data['shipping_cost'] = 0;

        if ($request['quantity'] == null) {
            $data['quantity'] = 1;
        }

        if ($carts && count($carts) > 0) {
            $foundInCart = false;

            foreach ($carts as $key => $cartItem) {
                $cart_product = get_product_details($cartItem['product_identifier'], $cartItem['supplier_id'])['info'];
                // if (isset($cart_product->auction_product) && $cart_product->auction_product == 1) {
                //     return array(
                //         'status' => 0,
                //         'cart_count' => count($carts),
                //         'modal_view' => view('frontend.partials.auctionProductAlredayAddedCart')->render(),
                //         'nav_cart_view' => view('frontend.partials.cart')->render(),
                //     );
                // }

                if ($cartItem['product_identifier'] == $request->identifier) {
                    if ($request->supplier_id == Supplier::$INHOUSE) {
                        $product_stock = $cart_product->stocks->where('variant', $str)->first();
                        $quantity = $product_stock->qty;
                        if ($quantity < $cartItem['quantity'] + $request['quantity']) {
                            return array(
                                'status' => 0,
                                'cart_count' => count($carts),
                                'modal_view' => view('frontend.partials.outOfStockCart')->render(),
                                'nav_cart_view' => view('frontend.partials.cart')->render(),
                            );
                        }
                    }
                    if (($str != null && $cartItem['variation'] == $str) || $str == null) {
                        $foundInCart = true;

                        $cartItem['quantity'] += $request['quantity'];

                        if ($cart_product['supplier_id'] == Supplier::$INHOUSE) {
                            if ($cart_product->wholesale_product) {
                                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                                if ($wholesalePrice) {
                                    $price = $wholesalePrice->price;
                                }
                            }
                            $cartItem['price'] = $price;

                            $cartItem->save();
                        }
                    }
                }
            }
            if (!$foundInCart) {
                Cart::create($data);
            }
        } else {
            Cart::create($data);
        }

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return array(
            'status' => 1,
            'cart_count' => count($carts),
            'modal_view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        Cart::destroy($request->id);
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return array(
            'cart_count' => count($carts),
            'cart_view' => view('frontend.partials.cart_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cartItem = Cart::findOrFail($request->id);
        $discount_applicable = false;

        if ($cartItem['id'] == $request->id) {
            if ($cartItem['supplier_id'] == Supplier::$INHOUSE) {
                $product = Product::find($cartItem['product_identifier']);
                $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
                $quantity = $product_stock->qty;
                $price = $product_stock->price;

                if ($product->discount_start_date == null) {
                    $discount_applicable = true;
                } elseif (
                    strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                    strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
                ) {
                    $discount_applicable = true;
                }

                if ($discount_applicable) {
                    if ($product->discount_type == 'percent') {
                        $price -= ($price * $product->discount) / 100;
                    } elseif ($product->discount_type == 'amount') {
                        $price -= $product->discount;
                    }
                }

                if ($quantity >= $request->quantity) {
                    if ($request->quantity >= $product->min_qty) {
                        $cartItem['quantity'] = $request->quantity;
                    }
                }

                if ($product->wholesale_product) {
                    $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                    if ($wholesalePrice) {
                        $price = $wholesalePrice->price;
                    }
                }
            } else {
                $product = get_product_details($cartItem['product_identifier'], $cartItem['supplier_id']);
                $cartItem['quantity'] = $request->quantity;
                $price = get_price($product);
            }


            $cartItem['price'] = $price;
            $cartItem->save();
        }

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return array(
            'cart_count' => count($carts),
            'cart_view' => view('frontend.partials.cart_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }
}
