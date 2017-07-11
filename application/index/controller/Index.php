<?php
namespace app\index\controller;
use think\Controller;
class Index extends Common{
	
    public function index()
    {	
    	$goodsType	=	'hot';
    	$online_num	=	getRandTime();
    	$this->assign('online_num', $online_num);
        return $this->fetch();
    }

    /**
     * 显示抢购
     */
    public function flashSale(){
    	$goodsType	=	'flashSale';
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
}
