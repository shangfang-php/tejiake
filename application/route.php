<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],

    'register'  =>  'index/login/register', ##用户注册
    'user_login'=>  'index/login/index', ##用户登录
    'flashSale' =>  'index/index/flashSale', ##显示抢购

    /*'[user]'     => [
        ':'   => ['index/index/detail',['method' => 'get']]
    ],
*/
];
