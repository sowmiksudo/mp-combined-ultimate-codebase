<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BannerCollection;
use Illuminate\Http\Request;

class BannerController extends Controller
{

    public function index(Request $request)
    {
        if ($request->query('position') == 1) {
            return new BannerCollection(json_decode(get_setting('home_banner1_images'), true));
        } else if ($request->query('position') == 2) {
            return new BannerCollection(json_decode(get_setting('home_banner2_images'), true));
        } else if ($request->query('position') == 3) {
            return new BannerCollection(json_decode(get_setting('home_banner3_images'), true));
        } else {
            return response()->json(["success" => false, "message" => "No such position."]);
        }
    }
}
