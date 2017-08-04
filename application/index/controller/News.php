<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class News extends Common{
    private $online_num = 0; ##在线人数

    public function _initialize(){
        parent::_initialize();
        $this->online_num   =   getRandTime();
        $this->assign('online_num', $this->online_num);
        $this->assign('goods_type', self::$goods_type);
    }
	
    /**
     * 招商淘客资料审核说明
     * @return [type] [description]
     */
    public function zs_news()
    {	
        return $this->fetch();
    }

    /**
     * 无商品或商品出错时展示页面
     * @Author   Gary
     * @DateTime 2017-08-02T17:54:37+0800
     * @return   [type]
     */
    public function no_goods(){
        return $this->fetch();
    }

    /**
     * api接口文档
     * @Author   Gary
     * @DateTime 2017-08-04T09:53:00+0800
     * @return   [type]                   [description]
     */
    public function api(){
        return $this->fetch();
    }

    
}
