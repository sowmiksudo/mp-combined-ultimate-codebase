<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\AmazonPrice;
use App\Models\CurrencyRate;
use App\Models\ProductStock;
use Illuminate\Support\Facades\Cache;



function get_product_details($pid, $sid)
{
    $response = '';
    if ($sid == Supplier::$AMAZON) {
        $response = Cache::remember("amazon-product-$pid", 24 * 3600 * 1000, function () use ($pid) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://parazun-amazon-data.p.rapidapi.com/product/?asin=$pid&region=US",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "X-RapidAPI-Host: parazun-amazon-data.p.rapidapi.com",
                    "X-RapidAPI-Key: 280674c74dmsh199c46a697c4b28p11209fjsneb15de4565e8"
                ],
            ]);

            $res = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            // dd(json_decode($res));
            return json_decode($res);
        });
    } elseif ($sid == Supplier::$SHEIN) {
        $response = Cache::remember("shein-product-$pid", 24 * 3600 * 1000, function () use ($pid) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://unofficial-shein.p.rapidapi.com/products/detail?goods_id=$pid&language=en&country=MY&currency=MYR",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "X-RapidAPI-Host: unofficial-shein.p.rapidapi.com",
                    "X-RapidAPI-Key: 280674c74dmsh199c46a697c4b28p11209fjsneb15de4565e8"
                ],
            ]);

            $res = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            // dd(json_decode($res));
            return json_decode($res);
        });
    } elseif ($sid == Supplier::$INHOUSE) {
        $response = Product::find($pid);
    }
    return collect([
        'sid' => $sid,
        'pid' => $pid,
        'info' => $response
    ]);
}

function get_thumbnail($product)
{
    if ($product['sid'] == Supplier::$INHOUSE) {
        return uploaded_asset($product['info']->thumbnail_img);
    } elseif ($product['sid'] == Supplier::$SHEIN) {
        return $product['info']->info->goods_img;
    } elseif ($product['sid'] == Supplier::$AMAZON) {
        return $product['info']->images[0]->thumb;
    }
}

function get_name($product)
{
    $name = '';
    switch ($product['sid']) {
        case Supplier::$AMAZON:
            $name = $product['info']->title;
            break;
        case Supplier::$SHEIN:
            $name = $product['info']->info->goods_name;
            break;
        case Supplier::$INHOUSE:
            $name = $product['info']->getTranslation('name');
            break;
    }
    return $name;
}

function get_main_link($product)
{
    switch ($product['sid']) {
        case Supplier::$AMAZON:
            return $product['info']->link;
            break;
        case Supplier::$SHEIN:
            // dd($product['info']);
            return get_shein_url($product['info']->info->goods_id, $product['info']->info->cat_id);
            break;
    }
}


function get_price($product, $discount = false, $cartItem = null)
{
    $val = 0;
    if ($product['sid'] == Supplier::$SHEIN) {
        $myr = $product['info']->info->sale_price->amount ?? $product['info']->info->retail_price->amount ?? 0;
        $prices = CurrencyRate::where('foreign_currency', 'myr')->get();
        $elligables = [];
        foreach ($prices as $price) {
            if ($price->min_price < $myr) {
                array_push($elligables, $price->bdt_rate);
            }
        }
        if (count($elligables) > 0) {
            $min = min($elligables);
        } else {
            $min = $prices->first()->bdt_rate;
        }
        $val = $min * $myr;
    } elseif ($product['sid'] == Supplier::$AMAZON) {
        $prices = CurrencyRate::where('foreign_currency', 'usd')->get();
        $usd = $product['info']->price->amount ?? $product['info']->price->min ?? 0;
        $elligables = [];
        foreach ($prices as $price) {
            if ($price->min_price < $usd) {
                array_push($elligables, $price->bdt_rate);
            }
        }
        if (count($elligables) > 0) {
            $min = min($elligables);
        } else {
            $min = $prices->first()->bdt_rate;
        }
        $val = $min * $usd;
    } elseif ($product['sid'] == Supplier::$INHOUSE) {
        $product = $product['info'];
        $price = $product->unit_price;

        $str = '';
        if ($cartItem['variation'] != null) {
            $str = $cartItem['variation'];
        }
        $product_stock = $product->stocks->where('variant', $str)->first();

        if ($product_stock) {
            $price = $product_stock->price;
        }

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($product->stocks->where('variant', null)->count() == 1 && $discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        } elseif ($product_stock->discount != null && $product_stock->discount_type != null) {
            if ($product_stock->discount_type == 'percent') {
                $price -= ($price * $product_stock->discount) / 100;
            } elseif ($product_stock->discount_type == 'amount') {
                $price -= $product_stock->discount;
            }
        }

        $tax = 0;
        foreach ($product['info']->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $val = preg_replace("/[^0-9.]/", "", $price);
    }

    return number_format((float)$val, 0, '.', '');
}

function get_tax($product)
{
    $tax = 0;
    $price = get_price($product);
    if ($product['sid'] == Supplier::$INHOUSE) {
        foreach ($product['info']->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
    }
    return $tax;
}

function get_shipping($product)
{
    return 200;
}

function get_shein_url($gid, $catid)
{
    return "https://www.shein.com/x-p-$gid-cat-$catid.html";
}
