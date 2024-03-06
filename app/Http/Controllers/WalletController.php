<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WithdrawRequest;
use Auth;
use Session;

class WalletController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_offline_wallet_recharges'])->only('offline_recharge_request');
    }

    public function index()
    {
        $wallets = Wallet::where('user_id', Auth::user()->id)->latest()->paginate(5);
        $withdrawals = WithdrawRequest::where('user_id', auth()->user()->id)->paginate(5);
        return view('frontend.user.wallet.index', compact('wallets', 'withdrawals'));
    }

    public function recharge(Request $request)
    {
        $data['amount'] = $request->amount;
        $data['payment_method'] = $request->payment_option;

        $request->session()->put('payment_type', 'wallet_payment');
        $request->session()->put('payment_data', $data);

        $request->session()->put('payment_type', 'wallet_payment');
        $request->session()->put('payment_data', $data);

        $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
        if (class_exists($decorator)) {
            return (new $decorator)->pay($request);
        }
    }

    public function wallet_payment_done($payment_data, $payment_details)
    {
        $user = Auth::user();
        $user->balance = $user->balance + $payment_data['amount'];
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $payment_data['amount'];
        $wallet->payment_method = $payment_data['payment_method'];
        $wallet->payment_details = $payment_details;
        $wallet->save();

        Session::forget('payment_data');
        Session::forget('payment_type');

        flash(translate('Payment completed'))->success();
        return redirect()->route('wallet.index');
    }

    public function offline_recharge(Request $request)
    {
        $wallet = new Wallet;
        $wallet->user_id = Auth::user()->id;
        $wallet->amount = $request->amount;
        $wallet->payment_method = $request->payment_option;
        $wallet->payment_details = $request->trx_id;
        $wallet->approval = 0;
        $wallet->offline_payment = 1;
        $wallet->reciept = $request->photo;
        $wallet->save();
        flash(translate('Offline Recharge has been done. Please wait for response.'))->success();
        return redirect()->route('wallet.index');
    }

    public function offline_recharge_request()
    {
        $wallets = Wallet::where('offline_payment', 1)->paginate(10);
        return view('manual_payment_methods.wallet_request', compact('wallets'));
    }

    public function updateApproved(Request $request)
    {
        $wallet = Wallet::findOrFail($request->id);
        $wallet->approval = $request->status;
        if ($request->status == 1) {
            $user = $wallet->user;
            $user->balance = $user->balance + $wallet->amount;
            $user->save();
        } else {
            $user = $wallet->user;
            $user->balance = $user->balance - $wallet->amount;
            $user->save();
        }
        if ($wallet->save()) {
            return 1;
        }
        return 0;
    }

    public function withdraw(Request $request){

        $request->validate([
            "amount" => 'required|numeric',
            "account" => 'required',
            'payment_method' => 'required'
        ]);

        $request->merge(['user_id' => auth()->user()->id, 'status' => 'pending']);

        $bal = auth()->user()->balance;
        if($bal > $request->amount){
            $new_bal = $bal - $request->amount;
            $created = WithdrawRequest::create($request->except('_token'));
            $user = auth()->user();
            $user->balance = $new_bal;
            $user->save();
        } else {
            flash('Not enough balance', 'warning');
            return back();
        }
        flash("Withdraw request has been submitted.", 'success');
        return back();
    }
}
