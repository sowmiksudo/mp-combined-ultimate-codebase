<?php

namespace App\Http\Controllers;

use App\Models\AmazonPrice;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\CurrencyRate;

class CurrencyController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:currency_setup'])->only('currency', 'create', 'edit');
    }

    public function addUsdBdt(Request $request)
    {
        if ($request->currency == 'usd') {
            CurrencyRate::create([
                'min_price' => $request->input('min_price'),
                'bdt_rate' => $request->input('bdt_rate'),
                'foreign_currency' => $request->input('currency'),
            ]);
        } else {
            CurrencyRate::create([
                'min_price' => $request->input('min_price'),
                'bdt_rate' => $request->input('bdt_rate'),
                'foreign_currency' => $request->input('currency'),
            ]);
        }
        flash(translate('Added successfully'))->success();
        return back();
    }
    public function removeUsdBdt(Request $request)
    {
        CurrencyRate::destroy($request->input('id'));
        flash(translate('Removed succesfully.'))->success();
        return back();
    }

    public function changeCurrency(Request $request)
    {
        $currency = Currency::where('code', $request->currency_code)->first();
        $request->session()->put('currency_code', $request->currency_code);
        $request->session()->put('currency_symbol', $currency->symbol);
        $request->session()->put('currency_exchange_rate', $currency->exchange_rate);
        flash(translate('Currency changed to ') . $currency->name)->success();
    }

    public function currency(Request $request)
    {
        $sort_search = null;
        $currencies = Currency::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $currencies = $currencies->where('name', 'like', '%' . $sort_search . '%');
        }
        $currencies = $currencies->paginate(10);
        $usds = CurrencyRate::where('foreign_currency', 'usd')->get();
        $myrs = CurrencyRate::where('foreign_currency', 'myr')->get();

        $active_currencies = Currency::where('status', 1)->get();
        return view('backend.setup_configurations.currencies.index', compact('currencies', 'active_currencies', 'sort_search', 'usds', 'myrs'));
    }

    public function updateYourCurrency(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->status = $currency->status;
        if ($currency->save()) {
            flash(translate('Currency updated successfully'))->success();
            return redirect()->route('currency.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return redirect()->route('currency.index');
        }
    }

    public function create()
    {
        return view('backend.setup_configurations.currencies.create');
    }

    public function edit(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        return view('backend.setup_configurations.currencies.edit', compact('currency'));
    }

    public function store(Request $request)
    {
        $currency = new Currency;
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->status = '0';
        if ($currency->save()) {
            flash(translate('Currency updated successfully'))->success();
            return redirect()->route('currency.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return redirect()->route('currency.index');
        }
    }

    public function update_status(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        if ($request->status == 0) {
            if (get_setting('system_default_currency') == $currency->id) {
                return 0;
            }
        }
        $currency->status = $request->status;
        $currency->save();
        return 1;
    }
}
