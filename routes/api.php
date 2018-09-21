<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        $api->post('version', function() {
            return response('this is version v1');
        });
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        $api->post('weapp/register', 'UsersController@weappStore')
            ->name('api.weapp.users.store');

        //yewu route
        $api->get('users', 'UsersController@store')
            ->name('api.users.store');
        //分类
        $api->get('categories', 'CategoriesController@index')
            ->name('api.categories.index');
        //轮播图片
        $api->get('banners', 'BannersController@index')
            ->name('api.banners.index');
        $api->get('products', 'ProductsController@index')
            ->name('api.banners.index');
        //公司信息
        $api->get('company', 'CompanyController@index')
            ->name('api.company.index');
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->post('check_token', 'UsersController@check')->name('api.user.check');
            $api->post('cart', 'CartController@add')->name('api.cart.add');
            $api->get('cart', 'CartController@index')->name('api.cart.index');
            $api->delete('cart', 'CartController@remove')->name('api.cart.remove');

            $api->get('user_addresses', 'UserAddressesController@index')->name('api.user_addresses.index');
            $api->get('first_user_address', 'UserAddressesController@first')->name('api.user_addresses.first');
            $api->get('update_first_user_address', 'UserAddressesController@updatefirst')->name('api.user_addresses.updatefirst');
            $api->post('user_addresses', 'UserAddressesController@store')->name('api.user_addresses.store');
            $api->get('user_addresses/{product}', 'UserAddressesController@edit')->name('api.user_addresses.edit');
            $api->put('user_addresses/{product}', 'UserAddressesController@update')->name('api.user_addresses.update');
            $api->delete('user_addresses/{product}', 'UserAddressesController@destroy')->name('api.user_addresses.destroy');

            $api->post('orders', 'OrdersController@store')->name('api.orders.store');
            $api->get('orders', 'OrdersController@index')->name('api.orders.index');
            $api->get('orders_count', 'OrdersController@count')->name('api.orders.count');
            $api->get('orders/{order}', 'OrdersController@show')->name('api.orders.show');
            $api->post('orders/{order}/received', 'OrdersController@received')->name('api.orders.received');
            $api->post('orders/{order}/close', 'OrdersController@close')->name('api.orders.close');
            $api->get('payment/{order}/wechat', 'PaymentController@payByWechat')->name('api.payment.wechat');
        });

        $api->get('products/{product}', 'ProductsController@show')->name('api.products.show');
    });
});