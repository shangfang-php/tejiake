<?php
namespace app\common\validate;
use \think\Validate;

class Goods extends Validate{
    protected $rule = [
        'taobao_goodsId'=>  'require|unique:goods',
        'title'         =>  'require',
        'link'          =>  'require|url',
        'coupon_link'   =>  'require|url',
        'common_type'   =>  'require|in:1,2',
        'main_img'      =>  'require|url',
        'short_title'   =>  'require',
        'price'         =>  'require|float',
        'sell_num'      =>  'require|number',
        'coupon_total_num'=>'require|number',
        'coupon_apply_num'=>'require|number',
        'coupon_money'  =>  'require|float',
        'real_money'    =>  'require|float',
        'taoke_money_percent'=>'require|number|between:1,100',
        'start_time'    =>  'require|<=:end_time',
        'end_time'      =>  'require|<=:coupon_end_time',
        'type'          =>  'require|in:1,2,3,4,5',
        'coupon_start_time'=>   'require|<=:coupon_end_time',
        'coupon_end_time'  =>   'require',
    ];
    
    protected $message  =   [
        'title.require' =>  '标题不能为空',
        'link.require'  =>  '商品链接不能为空',
        'short_title.require'   =>  '短标题不能为空',
        'coupon_total_num.require'=>'优惠券总数不能为空',
        'coupon_apply_num.require'=>'优惠券已领取数不能为空',
        'taoke_money_percent.require'=>'淘客佣金比例不能为空',
        'start_time.require'    =>  '开始时间不能为空',
        'end_time.require'      =>  '结束时间不能为空',
        'coupon_start_time.require' =>  '优惠券开始时间不能为空',
        'coupon_end_time.require'   =>  '优惠券结束时间不能为空',
        'start_time'   =>  '开始时间不能大于结束时间',
        'end_time'   =>  '结束时间不能大于优惠券结束时间',
        'coupon_start_time'   =>  '优惠券开始时间不能大于优惠券结束时间',
        'taobao_goodsId.unique:goods'=>'该商品已存在!',
        'taoke_money_percent.between'=>'淘客佣金比例只能在1-100之间',
    ];
    
    protected $scene = [
        'defalut'     =>  [],
        'flash_sale'  =>  [ ##普通计划限时抢购
                'plan_type'     =>  'require|in:1,2,3,4',
                'activity_type' =>  'require|in:1,2',
                ],
        'video'  =>  [ ##普通计划视频单
                'video_url'     =>  'require|url',
                ],
   ];
}
