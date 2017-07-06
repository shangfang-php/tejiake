<?php
namespace app\common\validate;
use \think\Validate;

class User extends Validate{
    protected $rule = [
        'phone'     =>  'require|/^1[3578]\d{9}$/|unique:user',
        'password'  =>  'require',
        'password1' =>  'require|confirm:password',
    ];
    
    protected $message  =   [
        'phone.require'             => '手机号不能为空',
        'phone./^1[3578]\d{9}$/'    => '手机号格式不正确',
        'password.require'          => '密码不能为空',
        'password1.confirm:password'=> '两次密码不一致!',
        'phone.unique:user'         =>  '该手机号已注册',
    ];
    
    //protected $scene = [
//        'parentAdd'  =>  ['name'],
//        'addDiscount'=>  array(
//                            'agentId'   =>  'require|number',
//                            'name'      =>  'require',
//                            'operator'  =>  'in:1,2,3',
//                            'trafficType'=> 'in:1,2,3',
//                            'discount'  =>  'number|between:0,10',
//                            'isLimit'   =>  'in:0,1',
//                            'isTicket'  =>  'in:0,1',
//                        ),
//    ];
}
