<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannersController extends Controller
{
    public function index() {
        $data['code'] = 0;
        $para = [];
        $banner1['businessId'] = 1;
        $banner1['picUrl'] = "https://www.52hairycrab.com/storage/img/banner4.jpg";
        $banner2['businessId'] = 2;
        $banner2['picUrl'] = "https://www.52hairycrab.com/storage/img/banner3.jpg";
        $banner3['businessId'] = 3;
        $banner3['picUrl'] = "https://www.52hairycrab.com/storage/img/banner2.jpg";
        $banner4['businessId'] = 4;
        $banner4['picUrl'] = "https://www.52hairycrab.com/storage/img/banner1.jpg";
        array_push($para, $banner1, $banner2, $banner3, $banner4);
        $data['data'] = $para;
        return $data;
    }
}
