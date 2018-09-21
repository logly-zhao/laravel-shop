<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    public function check()
    {
        $data['code'] = 0;
        return ;
    }
    public function store(Request $request)
    {
        $user = User::all()->first();
        $para = [];
        $para['token'] = \Auth::guard('api')->fromUser($user);
        $para['uid'] = $user->id;
        $para['code'] = 0;
        $data['data'] = $para;
        return $data;
    }
    public function weappStore(UserRequest $request)
    {
        $data = [];
        /*
        // 缓存中是否存在对应的 key
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // 判断验证码是否相等，不相等反回 401 错误
        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }
*/

        // 获取微信的 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data_code = $miniProgram->auth->session($request->code);

        if (isset($data_code['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        $decryptedData = $miniProgram->encryptor->decryptData($data_code['session_key'], $request->iv, $request->encryptedData);
        // 如果 openid 对应的用户已存在，报错403
        $user = User::where('weapp_openid', $data_code['openid'])->first();

        if ($user) {
            return $this->response->errorForbidden('微信已绑定其他用户，请直接登录');
        }

        // 创建用户
        $user = User::create([
            'name' => $decryptedData['nickName'],
            'email' => $data_code['openid'].'@qq.com',
    //        'phone' => $verifyData['phone'],
            'password' => bcrypt('default'),
            'weapp_openid' => $data_code['openid'],
            'weixin_session_key' => $data_code['session_key'],
        ]);

        // 清除验证码缓存
     //   \Cache::forget($request->verification_key);
/*
        // meta 中返回 Token 信息
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60,
            ])
            ->setStatusCode(201);
*/

        $para = [];
        $para['token'] = \Auth::guard('api')->fromUser($user);
        $para['uid'] = $user->id;
        $data['data'] = $para;
        return $data;
    }
}
