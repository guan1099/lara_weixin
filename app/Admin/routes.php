<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/weixin/user',Weixin\UserController::Class);
    $router->get('/weixin/sendmsg','Weixin\UserController@msg');    //群发视图
    $router->post('/weixin/sendmsg','Weixin\UserController@sendmsg');//群发接口
    $router->get('/weixin/menu','Weixin\UserController@menu');//菜单视图
    $router->post('/weixin/domenu','Weixin\UserController@domenu');//菜单接口
});
