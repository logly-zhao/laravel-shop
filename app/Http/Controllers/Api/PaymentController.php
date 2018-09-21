<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Models\Order;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;
use Illuminate\Validation\Rule;
use function EasyWeChat\Kernel\Support\generate_sign;


class PaymentController extends Controller
{
    public function payByWechat(Request $request)
    {
        $order = Order::query()->where('id', $request->order)->first();
        $this->authorize('own', $order);
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 之前是直接返回，现在把返回值放到一个变量里

        $payment = \EasyWeChat::payment();
        $openid = $request->user()->weapp_openid;
        $result = $payment->order->unify([
            'body'         => '支付 Laravel Shop 的订单：'.$order->no,
            'out_trade_no' => $order->no,
            'trade_type'   => 'JSAPI',  // 必须为JSAPI
            'openid'       => $openid, // 这里的openid为付款人的openid
            'total_fee'    => (floatval($request->money))*100, // 总价
        ]);

        $data = [];
        $data['code'] = "999";
        if ($result['return_code'] === 'SUCCESS') {
            $data['code'] = 0;
            // 二次签名的参数必须与下面相同
            $params = [
    //            'appId'     => '你的小程序的appid',
                'timeStamp' => time(),
                'nonceStr'  => $result['nonce_str'],
                'prepayId'  => $result['prepay_id'],
                'signType'  => 'MD5',
            ];

            // config('wechat.payment.default.key')为商户的key
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));
            $data['data'] = $params;
        } else
            $data['data'] = $result;
        return $data;

        /*
        $wechatOrder = app('wechat_pay')->scan([
            'out_trade_no' => $order->no,
            'total_fee'    => $order->total_amount * 100,
            'body'         => '支付 Laravel Shop 的订单：'.$order->no,
        ]);*/

        // 把要转换的字符串作为 QrCode 的构造函数参数
        //$qrCode = new QrCode($wechatOrder->code_url);

        // 将生成的二维码图片数据以字符串形式输出，并带上相应的响应类型
        //return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

}
