<?php
namespace app\admin\Controller;
use app\admin\Controller\Common;
use think\Controller;
class Index extends Common{
    public function index(){
        return $this->fetch();
    }
    public function index1(){
        //echo 88;exit;
        return $this->fetch();
    }
}