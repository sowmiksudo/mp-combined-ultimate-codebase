<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ClubPointDetail;
use App\Models\ClubPoint;
use App\Models\Product;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\Supplier;
use Artisan;
use Auth;

class ClubPointController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:club_point_configurations'])->only('configure_index');
        $this->middleware(['permission:set_club_points'])->only('set_point');
        $this->middleware(['permission:view_users_club_points'])->only('index');
    }

    public function configure_index()
    {
        return view('club_points.config');
    }

    public function index()
    {
        $club_points = ClubPoint::latest()->paginate(15);
        return view('club_points.index', compact('club_points'));
    }

    public function userpoint_index()
    {
        $club_points = ClubPoint::where('user_id', Auth::user()->id)->latest()->paginate(15);
        return view('club_points.frontend.index', compact('club_points'));
    }

    public function set_point()
    {
        $products = Product::latest()->paginate(15);
        return view('club_points.set_point', compact('products'));
    }

    public function set_products_point(Request $request)
    {
        $products = Product::whereBetween('unit_price', [$request->min_price, $request->max_price])->get();
        foreach ($products as $product) {
            $product->earn_point = $request->point;
            $product->save();
        }
        flash(translate('Point has been inserted successfully for ') . count($products) . translate(' products'))->success();
        return redirect()->route('set_product_points');
    }

    public function set_all_products_point(Request $request)
    {
        $products = Product::all();
        foreach ($products as $product) {;
            $product->earn_point = $product->unit_price * $request->point;
            $product->save();
        }
        $setting = BusinessSetting::where('type', 'club_points_rate')->first();
        $setting->value = $request->point;
        $setting->save();
        flash(translate('Point has been inserted successfully for ') . count($products) . translate(' products'))->success();
        return redirect()->route('set_product_points');
    }

    public function set_point_edit($id)
    {
        $product = Product::findOrFail(decrypt($id));
        return view('club_points.product_point_edit', compact('product'));
    }

    public function update_product_point(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->earn_point = $request->point;
        $product->save();
        flash(translate('Point has been updated successfully'))->success();
        return redirect()->route('set_product_points');
    }

    public function convert_rate_store(Request $request)
    {
        $club_point_convert_rate = BusinessSetting::where('type', $request->type)->first();
        if ($club_point_convert_rate != null) {
            $club_point_convert_rate->value = $request->value;
        } else {
            $club_point_convert_rate = new BusinessSetting;
            $club_point_convert_rate->type = $request->type;
            $club_point_convert_rate->value = $request->value;
        }
        $club_point_convert_rate->save();

        Artisan::call('cache:clear');

        flash(translate('Point convert rate has been updated successfully'))->success();
        return redirect()->route('club_points.configs');
    }

    public function processClubPoints($order)
    {
        $club_point = new ClubPoint;
        $club_point->user_id = $order->user_id;
        $club_point->points = 0;
        foreach ($order->orderDetails as $key => $orderDetail) {
            $product = get_product_details($orderDetail->product_identifier, $orderDetail->supplier_id);
            // dd($product, $orderDetail->product_identifier, $orderDetail->supplier_id);
            if ($orderDetail->supplier_id == Supplier::$INHOUSE) {
                $total_pts = ($product['info']->earn_point) * $orderDetail->quantity;
                $club_point->points += $total_pts;
            } else {
                $total_pts = get_price($product) * club_points_rate() * $orderDetail->quantity;
                $club_point->points += $total_pts;
            }
        }
        $club_point->order_id = $order->id;
        $club_point->convert_status = 0;
        $club_point->save();

        foreach ($order->orderDetails as $key => $orderDetail) {
            $product = get_product_details($orderDetail->product_identifier, $orderDetail->supplier_id);
            if ($orderDetail->supplier_id == Supplier::$INHOUSE) {
                $club_point_detail = new ClubPointDetail;
                $club_point_detail->club_point_id = $club_point->id;
                $club_point_detail->product_identifier = $orderDetail->product_identifier;
                $club_point_detail->supplier_id = $orderDetail->supplier_id;
                $club_point_detail->point = ($product['info']->earn_point) * $orderDetail->quantity;
                $club_point_detail->save();
            } else {
                $club_point_detail = new ClubPointDetail;
                $club_point_detail->club_point_id = $club_point->id;
                $club_point_detail->product_identifier = $orderDetail->product_identifier;
                $club_point_detail->supplier_id = $orderDetail->supplier_id;
                $club_point_detail->point = get_price($product) * $orderDetail->quantity;
                $club_point_detail->save();
            }
        }
    }

    public function club_point_detail($id)
    {
        $club_point_details = ClubPointDetail::where('club_point_id', decrypt($id))->paginate(12);
        return view('club_points.club_point_details', compact('club_point_details'));
    }

    public function convert_point_into_wallet(Request $request)
    {
        $club_point = ClubPoint::findOrFail($request->el);
        if ($club_point->convert_status == 0) {
            $wallet = new Wallet;
            $wallet->user_id = Auth::user()->id;
            $wallet->amount = floatval($club_point->points / get_setting('club_point_convert_rate'));
            $wallet->payment_method = 'Club Point Convert';
            $wallet->payment_details = 'Club Point Convert';
            $wallet->save();
            $user = Auth::user();
            $user->balance = $user->balance + floatval($club_point->points / get_setting('club_point_convert_rate'));
            $user->save();
            $club_point->convert_status = 1;
        }

        if ($club_point->save()) {
            return 1;
        } else {
            return 0;
        }
    }
}
