<?php
namespace app\admin\controller;
use app\admin\controller\Common;
use think\Controller;
class Consult extends Common{
    public function index(){
        $this->assign('data',array());
       return $this->fetch();
    }

    public function addConsult(){
       return $this->fetch();
    }
}