<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use App\Services\CartService;
use Yansongda\Pay\Log;

class CartController extends Controller
{
    protected $cartService;

    // 利用 Laravel 的自动解析功能注入 CartService 类
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $count = 0;
        $data = [];
        $shoplists = [];
        $cartItems = $this->cartService->get();
        foreach($cartItems as $cartItem) {
            $shoplist = [];
            $shoplist['active'] = true;
            $shoplist['goodsId'] = $cartItem->productSku->id;
            $shoplist['label'] = $cartItem->productSku->product->title;
            $shoplist['pic'] = 'https://www.52hairycrab.com/storage/'.$cartItem->productSku->product->image;
            $shoplist['name'] = $cartItem->productSku->title;
            $shoplist['price'] = $cartItem->productSku->price;
            $shoplist['number'] = $cartItem->amount;
            array_push($shoplists, $shoplist);
            $count ++;
        }
        $data['shopList'] = $shoplists;
        $data['shopNum'] = $count;
        return $data;
    }

    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));
        return [];
    }

    public function remove(Request $request)
    {
        $list = substr($request->delete_list,0,strlen($request->delete_list)-1);
        $arr = explode(",", $list);
        $this->cartService->remove($arr);
        return [];
    }
}
