<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use App\Models\User;
use App\Models\Product;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;

class OTPVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verification(Request $request)
    {
        if (Auth::check() && Auth::user()->email_verified_at == null) {
            return view('otp_systems.frontend.user_verification', [
                'user' => Auth::user()
            ]);
        } else {
            flash('You have already verified your number')->warning();
            return redirect()->route('home');
        }
    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function verify_phone(Request $request)
    {
        $user = Auth::user();
        if ($user->verification_code == $request->verification_code) {
            $credential = $request->credential;
            $user->email_verified_at = date('Y-m-d h:m:s');
            $user->save();

            flash('Your ' . $credential . ' has been verified successfully')->success();
            return redirect()->route('home');
        } else {
            flash('Invalid Code')->error();
            return back();
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function resend_verificcation_code(Request $request)
    {
        $user = Auth::user();
        $user->verification_code = rand(100000, 999999);
        $user->save();
        if ($user->email == null) {
            SmsUtility::phone_number_verification($user);
        } else {
            $request->user()->sendEmailVerificationNotification();
        }

        return back();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function reset_password_with_code(Request $request)
    {
        $phone = "+{$request['country_code']}{$request['phone']}";

        if (($user = User::where('phone', $phone)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    flash("Password has been reset successfully")->success();
                    return redirect()->route('admin.dashboard');
                }
                flash("Password has been reset successfully")->success();
                return redirect()->route('home');
            } else {
                flash("Password and confirm password didn't match")->warning();
                return view('otp_systems.frontend.auth.passwords.reset_with_phone');
            }
        } else {
            flash("Verification code mismatch")->error();
            return view('otp_systems.frontend.auth.passwords.reset_with_phone');
        }
    }


    /**
     * @param  User $user
     * @return void
     */

    public function send_code($user)
    {
        SmsUtility::phone_number_verification($user);
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_order_code($order)
    {
        $phone = json_decode($order->shipping_address)->phone;
        if (strlen($phone) == 11) {
            $phone = '+88' . $phone;
        }
        if ($phone != null) {
            SmsUtility::order_placement($phone, $order);
        }
    }

    public function send_preloved_order_code($order)
    {
        foreach ($order->orderDetails as $key => $order) {
            if (Product::find($order->product_identifier)->preloved) {
                $user_id = Product::find($order->product_identifier)->preloved_owner_id;
            }
        }
        $phone = User::find($user_id)->phone;
        if (strlen($phone) == 11) {
            $phone = '+88' . $phone;
        }
        if ($phone != null) {
            SmsUtility::preloved_order_placement($phone, $order);
        }
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_delivery_status($order)
    {
        $phone = json_decode($order->shipping_address)->phone;
        if (strlen($phone) == 11) {
            $phone = '+88' . $phone;
        }
        if ($phone != null) {
            SmsUtility::delivery_status_change($phone, $order);
        }
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_payment_status($order)
    {
        $phone = json_decode($order->shipping_address)->phone;
        if (strlen($phone) == 11) {
            $phone = '+88' . $phone;
        }
        if ($phone != null) {
            SmsUtility::payment_status_change($phone, $order);
        }
    }
}
