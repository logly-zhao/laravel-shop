<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

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
    });
});