<?php

namespace App\Http\Controllers\Api;
use App\Exceptions\InternalException;
use App\Http\Requests\CrowdFundingOrderRequest;
use App\Http\Requests\SeckillOrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Events\OrderReviewd;
use App\Http\Requests\ApplyRefundRequest;
use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        /*
        $orders = Order::all()
  //          ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id);
 //           ->orderBy('created_at', 'desc');
*/

        $orders = Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        $data = [];
        $lists = [];
        $type = intval($request->input('type'));

        switch($type){
            case 0://未支付
                $orders = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $request->user()->id)->where('closed', 0)
                    ->where('paid_at', NULL)
                    ->orderBy('created_at', 'desc')->get();
                break;
            case 1://待发货、待收货
                $orders = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $request->user()->id)->where('closed', 0)
                    ->whereNotNull('paid_at')->whereIn('ship_status',['pending','delivered'])
                    ->orderBy('created_at', 'desc')->get();
                break;
            case 2://待评价
                $orders = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $request->user()->id)->where('closed', 0)
                    ->whereNotNull('paid_at')->where('ship_status','received')->where('reviewed',0)
                    ->orderBy('created_at', 'desc')->get();
                break;
            case 3://已完成
                $orders = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $request->user()->id)->where('closed', 0)
                    ->whereNotNull('paid_at')->where('ship_status','received')->where('reviewed',1)
                    ->orderBy('created_at', 'desc')->get();
                break;
            case 4:
                $orders = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $request->user()->id)->where('closed', 1)
                    ->orderBy('created_at', 'desc')->get();
                break;
        }

        foreach($orders as $order) {
            $pitem = [];
            $pitem['dateAdd'] = $order->created_at->format('Y-m-d H:i:s');
            $pitem['orderNumber'] = $order->no;
            $pitem['status'] = 0;
            $pitem['statusStr'] = '';
            $pitem['remark'] = '';
            if($order->remark)
                $pitem['remark'] = $order->remark;
            $pitem['amountReal'] = $order->total_amount;
            $pitem['score'] = 0;
            $pitem['id'] = $order->id;
            $pitem['pics'] = [];
            foreach($order->items as $index => $item) {
                $pic = [];
                $pic['pic'] = $item->product->image_url;
                array_push($pitem['pics'],$pic);
            }
            $pitem['status'] = $type;

            switch($type){
                case 0://未支付
                    $pitem['statusStr'] = '请于'.$order->created_at->addSeconds(config('app.order_ttl'))->format('H:i').'前完成支付';
                    break;
                case 1://待发货、待收货
                    if($order->ship_status == 'pending') {
                        $pitem['statusStr'] = "待发货";
                    } else {
                        $pitem['statusStr'] = "待收货";
                        $pitem['status'] = 5;
                    }
                    break;
                case 2://待评价
                    $pitem['statusStr'] = "待评价";
                    break;
                case 3://已完成
                    break;
                case 4:
                    $pitem['statusStr'] = "已关闭";
                    break;
            }
            array_push($lists, $pitem);
        }
        $data['orderList'] = $lists;
        $data['code'] = 0;
        $data['type'] = $type;
        return $data;
    }

    public function count(Request $request)
    {
        $orders = Order::all()
            //          ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id);
        //           ->orderBy('created_at', 'desc');
/*
        $orders = Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc');*/
        $data = [];
        $paras = [];
        $no_pay_count = 0;
        $close_count = 0;
        $no_confirm_count = 0;
        $no_reputation_count = 0;
        $success_count = 0;
        foreach($orders as $order) {
            if($order->closed == 1)
                $close_count++;
            else if(!$order->paid_at)
                $no_pay_count++;
            else {
                switch($order->ship_status) {
                    case 'pending':
                    case 'delivered':
                        $no_confirm_count++;
                        break;
                    case 'received';
                        if(($order->reviewed) > 0)
                            $success_count++;
                        else
                            $no_reputation_count++;
                        break;
                }
            }
        }
        $paras['count_id_no_pay'] = $no_pay_count;
        $paras['count_id_no_confirm'] = $no_confirm_count;
        $paras['count_id_no_reputation'] = $no_reputation_count;
        $paras['count_id_success'] = $success_count;
        $paras['count_id_closed'] = $close_count;

        $data['data'] = $paras;
        $data['code'] = 0;
        return $data;
    }

    public function show(Request $request)
    {
        $order = Order::query()->with(['items.product', 'items.productSku'])->where('id', $request->order)->get()->first();
        $this->authorize('own', $order);
        $data = [];
        $para = [];
        $data['code'] = 0;
        $info = [];

        $info['statusStr'] = '';
        $info['status'] = 0;
        if($order->closed) {
            $info['status'] = 4;
            $info['statusStr'] = '订单已关闭';
        } else if(!$order->paid_at) {
            $info['status'] = 0;
            $info['statusStr'] = '请于'.$order->created_at->addSeconds(config('app.order_ttl'))->format('H:i').'前完成支付';
        } else if($order->ship_status == 'pending') {
            $info['status'] = 1;
            $info['statusStr'] = '待发货';
        } else if($order->ship_status == 'delivered') {
            $info['status'] = 5;
            $info['statusStr'] = '已发货待确认';
        } else if($order->ship_status == 'received') {
            if($order->reviewed == 0) {
                $info['status'] = 2;
                $info['statusStr'] = '待评价';
            } else {
                $info['status'] = 3;
                $info['statusStr'] = '已完成';
            }
        }
        $info['id'] = $order->id;
        $info['amount'] = $order->total_amount;
        $info['amountReal'] = $order->total_amount;
        $para['orderInfo'] = $info;

        $wuliu = [];
        $wuliu['trackingNumber'] = $order->ship_data['express_company'].$order->ship_data['express_no'];
        $wuliu['linkMan'] = $order->address['contact_name'];
        $wuliu['mobile'] = $order->address['contact_phone'];
        $wuliu['address'] = $order->address['address'];
        $para['logistics'] =$wuliu;

        $goods = [];
        foreach($order->items as $index => $item) {
            $good = [];
            $good['pic'] = $item->product->image_url;
            $good['goodsName'] = $item->product->title;
            $good['amount'] = $item->price;
            $good['number'] = $item->amount;
            $good['property'] = $item->productSku->title;
            array_push($goods, $good);
        }
        $para['goods'] = $goods;
        $data['data'] = $para;
        return $data;
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $data = [];
        $user    = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $coupon  = null;

        // 如果用户提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }
        if($calculate = $request->input('calculate')) {
             if($calculate == true) {
                 $totalAmount = 0;
                 // 遍历用户提交的 SKU
                 $items = $request->input('items');
                 foreach ($items as $item) {
                     $sku  = ProductSku::find($item['sku_id']);
                     $totalAmount += $sku->price * $item['amount'];
                     if ($sku->decreaseStock($item['amount']) <= 0) {
                         $data['code'] = 999;
                         return $data;
                     }
                 }
                 $data['code'] = 0;
                 $data['amountTotle'] = $totalAmount;
                 return $data;
             }
        }
        try {
            $order = $orderService->store($user, $address, $request->input('remark'), $request->input('items'), $coupon);
            $data['code'] = 0;
        }catch (Exception $e) {
            $data['code'] = 999;
        }
        return $data;
    }

    public function received(Request $request)
    {
        $data = [];
        $data['code'] = 0;
        $order = Order::query()->where('id', $request->order)->first();
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            $data['code'] = 999;
            $data['msg'] = '发货状态不正确';
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return $data;
    }

    public function close(Request $request)
    {
        $data = [];
        $data['code'] = 0;
        $order = Order::query()->where('id', $request->order)->first();
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->paid_at) {
            $data['code'] = 999;
            $data['msg'] = '订单已支付不能取消，请申请退款';
        }

        // 关闭订单
        $order->update(['closed' => 1]);

        return $data;
    }

    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
            event(new OrderReviewd($order));
        });

        return redirect()->back();
    }

    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        // 众筹订单不允许申请退款
        if ($order->type === Order::TYPE_CROWDFUNDING) {
            throw new InvalidRequestException('众筹订单不支持退款');
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra                  = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra,
        ]);

        return $order;
    }

    // 创建一个新的方法用于接受众筹商品下单请求
    public function crowdfunding(CrowdFundingOrderRequest $request, OrderService $orderService)
    {
        $user    = $request->user();
        $sku     = ProductSku::find($request->input('sku_id'));
        $address = UserAddress::find($request->input('address_id'));
        $amount  = $request->input('amount');

        return $orderService->crowdfunding($user, $address, $sku, $amount);
    }

    public function seckill(SeckillOrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $sku  = ProductSku::find($request->input('sku_id'));

        return $orderService->seckill($user, $request->input('address'), $sku);
    }
}
