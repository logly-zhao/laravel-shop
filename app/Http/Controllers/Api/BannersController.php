<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannersController extends Controller
{
    public function index() {
        $banners = Banner::query()->where('is_front', true)->get();

        $data['code'] = 0;
        $para = [];
        foreach($banners as $banner) {
            $item = [];
            $item['businessId'] = $banner->id;
            //$item['picUrl'] = "https://www.52hairycrab.com/storage/".$banner->image;
            $item['picUrl'] = "http://shop.test/storage/".$banner->image;
            array_push($para, $item);
        }
        $data['data'] = $para;
        return $data;
    }
}
