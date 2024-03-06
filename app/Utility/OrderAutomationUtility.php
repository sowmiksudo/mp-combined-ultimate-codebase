<?php

namespace App\Utility;

use Mpdf\Tag\P;
use App\Models\Order;
use GuzzleHttp\Client;
use App\Models\Supplier;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class OrderAutomationUtility
{

    public $order;

    public function shein_order(Order $order)
    {
        $response = '';
        $arr = [];
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->supplier_id == Supplier::$SHEIN) {
                $order_item = [
                    'goods_id' => $orderDetail->product_identifier,
                    'sku' => $orderDetail->sku,
                    'quantity' => $orderDetail->quantity,
                    'variant' => $orderDetail->variation,
                    'order_details_id' => $orderDetail->id,
                    'order_id' => $order->id,
                ];
                array_push($arr, $order_item);
            }
        }

        if(count($arr) > 0){
            $client = new Client([
                'verify' => false
            ]);
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];
    
            $body = json_encode([
                "order_id" => $order->code,
                "orders" => $arr
            ]);
    
            $request = new Request('POST', env('AUTOMATION_URL').'/api/v1/order/place', $headers, $body);
    
            try {
                $response = $client->sendAsync($request)->wait();
            } catch (ServerException $exception) {
                $response = $exception->getResponse();
            }
           // dd($response->getBody()->getContents());    
            if ($response->getStatusCode() == 200) {
                $order->server_status = 'submitted';
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $sid = json_decode($response->getBody())->orders[0]->id;
                    // dd($sid);
                    $orderDetail->server_status = 'submitted';
                    $orderDetail->server_id = $sid;
                }
            } else {
                $order->server_status = 'failed';
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->server_status = 'failed';
                    $orderDetail->server_id = null;
                }
            }
    
    
            // dump($arr, $response->getBody()->getContents());
    
            $order->save();
            $orderDetail->save();
            return $response;
        }

        return true; 
        
    }
}
