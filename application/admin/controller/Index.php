<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
class Index extends Common{
    public function index(){
        return $this->fetch();
    }
    public function index1(){
        //今日上架商品数
        //招商淘客数 , 普通淘客申请招商淘客数
        //商品待审核 各类型数 爆款 限时抢购 直播单 过夜单 视频单
        $data = [
            
        ];
        $this->assign($data);
        return $this->fetch();
    }
}