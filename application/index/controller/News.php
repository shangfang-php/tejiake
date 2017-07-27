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

    
}
