<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Common{
    private $online_num = 0; ##在线人数

    public function _initialize(){
        parent::_initialize();
        $this->online_num   =   getRandTime();
        $this->assign('online_num', $this->online_num);
    }
	
    public function index()
    {	
        $goods_list =   $this->get_goods_list(1); ##1为爆款单

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                    );
        $this->assign($data);
    	
        return $this->fetch();
    }

    /**
     * 限时抢购
     */
    public function flash_sale(){
    	$this->goods_type	=	'flash_sale';
        $goods_list =   $this->get_goods_list(2); ##2为限时抢购

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 展示具体商品信息
     * @return [type] [description]
     */
    public function detail(){
    	$online_num	=	getRandTime();
    	$this->assign('online_num', $online_num);
        return $this->fetch();
    }

    /**
     * 获取商品信息
     * @param  [type]  $goodsType [description]
     * @param  integer $start     [description]
     * @param  [type]  $nums      [description]
     * @return [type]             [description]
     */
    function get_goods_list($goodsType, $start = 0, $nums = 20){
        $where  =   array('type'=>$goodsType, 'status'=>2);
        $goods  =   Db::table('goods')->where($where)->order('id', 'desc')->limit($start, $nums)->select();
        return $goods;
    }
}
