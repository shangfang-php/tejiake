<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Db;
class Index extends Common{
    public function index(){
        return $this->fetch();
    }
    public function index1(){
        //今日上架商品数
        $goods_num = Db::name('goods')->where(array('create_time'=>array('egt',strtotime(date('Y-m-d')))))->count();
        //招商淘客数 , 普通淘客申请招商淘客数
        $merchant_num = Db::name('user')->where(array('type'=>2))->count();
        $apply_merchant_num = Db::name('merchant_apply_record')->where(array('status'=>1))->count();
        //商品待审核 各类型数 1爆款单2限时抢购3过夜单4直播单5视频单
        $count_1 = Db::name('goods')->where(['status'=>1,'type'=>1])->count();
        $count_2 = Db::name('goods')->where(['status'=>1,'type'=>2])->count();
        $count_3 = Db::name('goods')->where(['status'=>1,'type'=>3])->count();
        $count_4 = Db::name('goods')->where(['status'=>1,'type'=>4])->count();
        $count_5 = Db::name('goods')->where(['status'=>1,'type'=>5])->count();
        $data = [
            'goods_count' => $goods_num,
            'merchant_count' => $merchant_num,
            'apply_merchant_count' => $apply_merchant_num,
            'wait_for_check' =>[
                1=>$count_1,
                2=>$count_2,
                3=>$count_3,
                4=>$count_4,
                5=>$count_5
            ]
        ];
        $this->assign($data);
        return $this->fetch();
/*
        $real_url 	=	get_headers("https://s.click.taobao.com/euIubdw?pid=mm_0_0_0");
        echo '<pre>';
        print_r($real_url);*/
    }


}