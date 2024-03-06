<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Order;
use App\Models\SheinOrder;
use App\Models\AmazonOrder;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('code', 'desc')->paginate(9);
        return view('frontend.user.purchase_history', compact('orders'));
    }

    public function digital_index()
    {
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_identifier', '=', 'products.id')
            ->where('orders.user_id', Auth::user()->id)
            ->where('products.digital', '1')
            ->where('order_details.payment_status', 'paid')
            ->select('order_details.id')
            ->paginate(15);
        return view('frontend.user.digital_purchase_history', compact('orders'));
    }

    public function purchase_history_details($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->delivery_viewed = 1;
        $order->payment_status_viewed = 1;
        $order->save();
        return view('frontend.user.order_details_customer', compact('order'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function order_cancel($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->user()->id)->first();
        if ($order && ($order->delivery_status == 'pending' && $order->payment_status == 'unpaid')) {
            $order->delivery_status = 'cancelled';
            $order->save();

            flash(translate('Order has been canceled successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return back();
    }

    public function preloved_sales(Request $request){
        $orders = Order::where('supplier_id', Supplier::$INHOUSE)->get();
        $preloved_orderDetails = [];
        foreach ($orders as $key => $order) {
            foreach ($order->orderDetails as $orderDetail) {
                $product = Product::find($orderDetail->product_identifier);
                if($product && $product->preloved_owner_id == auth()->user()->id){
                    array_push($preloved_orderDetails, $orderDetail);
                }
            }
        }
        return view('frontend.user.preloved_sales_history', ["preloved_orderDetails"=>$preloved_orderDetails]);
    }
}
