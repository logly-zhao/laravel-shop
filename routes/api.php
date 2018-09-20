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

        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')->name('api.user.show');
            $api->post('cart', 'CartController@add')->name('api.cart.add');
            $api->get('cart', 'CartController@index')->name('api.cart.index');
            $api->delete('cart', 'CartController@remove')->name('api.cart.remove');

            $api->get('user_addresses', 'UserAddressesController@index')->name('api.user_addresses.index');
//            $api->get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
            $api->post('user_addresses', 'UserAddressesController@store')->name('api.user_addresses.store');
            $api->get('user_addresses/{product}', 'UserAddressesController@edit')->name('api.user_addresses.edit');
            $api->put('user_addresses/{product}', 'UserAddressesController@update')->name('api.user_addresses.update');
            $api->delete('user_addresses/{product}', 'UserAddressesController@destroy')->name('api.user_addresses.destroy');
        });

        $api->get('products/{product}', 'ProductsController@show')->name('api.products.show');
    });
});