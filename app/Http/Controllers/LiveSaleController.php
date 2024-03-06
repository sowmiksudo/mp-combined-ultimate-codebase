<?php

namespace App\Http\Controllers;

use App\Models\LiveSale;
use App\Models\LiveOrder;
use App\Models\SmsTemplate;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use App\Utility\NotificationUtility;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V2\DeliveryBoyController;
use Iyzipay\Model\Reward;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class LiveSaleController extends Controller
{
    public function index()
    {
        $running_live = LiveSale::where('is_running', 1)->latest()->first();
        if(!$running_live){
            flash('No live sales found.')->error();
            return back();
        }
        return view('frontend.live-sale', [
            'live' => $running_live
        ]);
    }

    public function order(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
        ]);
        $data = [
            'accessor' => 'product_id',
            'payment_status' => 'unpaid'
        ];
        $data["live_sale_id"] = $request->live_sale_id;
        if (auth()->check()) {
            $data['user_id'] = auth()->user()->id;
            $data['customer_info'] = json_encode([
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? $request->phone,
                'address' => $request->address
            ]);
        } else {
            $data['customer_info'] = json_encode([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address
            ]);
            $data['user_id'] = null;
        }
        // dump($data);
        LiveOrder::create($data);
        flash('Your order have been placed successfully. We\'ll contact you soon.')->success();
        return back();
    }

    public function orders(Request $request)
    {
        $orders = LiveOrder::paginate(15);
        $delivery_status = 'pending';
        $date = $request->date;
        return view('backend.sales.fb.index', compact('orders', 'delivery_status', 'date'));
    }

    public function show(Request $request, $id)
    {
        $id = decrypt($id);
        $order = LiveOrder::findOrFail($id);
        return view('backend.sales.fb.show', compact('order'));
    }

    public function upStatus(Request $request)
    {
        $order = LiveOrder::find($request->order_id);
        $order->delivery_status = $request->order_status;
        $order->save();
        flash('Status updated')->success();
        return back();
    }

    public function destroy($id)
    {
        $order = LiveOrder::findOrFail($id);
        if ($order != null) {
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function update_payment_status(Request $request)
    {
        $order = LiveOrder::findOrFail($request->order_id);

        $order->save();

        $order->payment_status = $request->status;
        $status = 'paid';
        $order->payment_status = $status;
        $order->save();

        // sends Notifications to user
        // NotificationUtility::sendNotification($order, $request->status);
        // if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
        //     $request->device_token = $order->user->device_token;
        //     $request->title = "Order updated !";
        //     $status = str_replace("_", "", $order->payment_status);
        //     $request->text = " Your order {$order->code} has been {$status}";

        //     $request->type = "order";
        //     $request->id = $order->id;
        //     $request->user_id = $order->user->id;

        //     NotificationUtility::sendFirebaseNotification($request);
        // }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->customer_info)->phone, $order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    public function update_delivery_status(Request $request)
    {
        $order = LiveOrder::findOrFail($request->order_id);
        $order->delivery_status = $request->status;
        $order->save();

        $order->delivery_status = $request->status;
        $order->save();

        // foreach ($order->orderDetails as $key => $orderDetail) {



        //     if ($request->status == 'cancelled') {
        //         $variant = $orderDetail->variation;
        //         if ($orderDetail->variation == null) {
        //             $variant = '';
        //         }

        //         $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
        //             ->where('variant', $variant)
        //             ->first();

        //         if ($product_stock != null) {
        //             $product_stock->qty += $orderDetail->quantity;
        //             $product_stock->save();
        //         }
        //     }

        //     if (addon_is_activated('affiliate_system')) {
        //         if (($request->status == 'delivered' || $request->status == 'cancelled') &&
        //             $orderDetail->product_referral_code
        //         ) {

        //             $no_of_delivered = 0;
        //             $no_of_canceled = 0;

        //             if ($request->status == 'delivered') {
        //                 $no_of_delivered = $orderDetail->quantity;
        //             }
        //             if ($request->status == 'cancelled') {
        //                 $no_of_canceled = $orderDetail->quantity;
        //             }

        //             $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

        //             $affiliateController = new AffiliateController;
        //             $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
        //         }
        //     }
        // }

        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {
            }
        }

        //sends Notifications to user
        // NotificationUtility::sendNotification($order, $request->status);
        // if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
        //     $request->device_token = $order->user->device_token;
        //     $request->title = "Order updated !";
        //     $status = str_replace("_", "", $order->delivery_status);
        //     $request->text = " Your order {$order->code} has been {$status}";

        //     $request->type = "order";
        //     $request->id = $order->id;
        //     $request->user_id = $order->user->id;

        //     NotificationUtility::sendFirebaseNotification($request);
        // }


        if (addon_is_activated('delivery_boy')) {
            if (Auth::user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        }

        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        $order = LiveOrder::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
    }


    public function live_sales(Request $request){
        $lives = LiveSale::orderBy('is_running', 'desc')->orderBy('id', 'desc')->paginate(15);
        return view('backend.sales.fb.live-sales', compact('lives'));
    }

    public function add_sales(Request $request){
        $live = LiveSale::create($request->all());
        flash('Sale started!')->success();
        return back();
    }

    public function show_sales(Request $request, $id){
        $id = decrypt($id);
        $live = LiveSale::findOrFail($id);
        $orders = $live->orders;
        // dd($orders);
        return view('backend.sales.fb.live-show', compact('live', 'orders'));
    }

    public function delete_sales(Request $request, $id){
        LiveSale::destroy($id);
        flash('Deleted live sale '.$id)->success();
        return back();
    }

    public function update_sales(Request $request, $id){
        $id = decrypt($id);
        $live = LiveSale::findOrFail($id);
        $live->is_running = $request->is_running;
        $live->embed_code = $request->embed_code;
        $live->save();
        flash('Updated successfully.')->success();
        return back();
    }

}
