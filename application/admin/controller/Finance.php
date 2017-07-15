<?php
namespace app\admin\controller;
use app\admin\controller\Common;
use think\Controller;
use think\Db;
use think\paginator;
class Finance extends Common{
    /*
     * 财务明细
     * */
    public function index(){
        $keyword = input('keyword');
        $where = array();
        if(!empty($keyword)){
            $where['ucr.ali_trade_no|u.phone'] = array("like","%$keyword%");
        }
        $list = Db::name('user_charge_records')
            ->alias('ucr')
            ->field('ucr.*,u.phone')
            ->where($where)
            ->join('user u','ucr.uid=u.id','left')
            ->order('id desc')
            ->paginate(10,false,['query'=>request()->param()]);
        //echo '<pre>';
       // print_r($list);exit;
        $show = $list->render();
        $this->assign('data',$list);
        $this->assign('show',$show);
        return $this->fetch();
    }

    /*
     * 积分详情
     * */
    public function scoreDetail(){
        $where['usr.is_delete'] = 0;
        $keyword = input('keyword');
        if(!empty($keyword)){
            $where['u.phone'] = array('like',"%$keyword%");
        }
        $list = Db::name('user_score_record')
            ->alias('usr')
            ->field('usr.*,u.phone')
            ->where($where)
            ->join('user u','usr.uid=u.id','left')
            ->order('id desc')
            ->paginate(10,false,['query'=>request()->param()]);
        $show = $list->render();
        $score_type = Db::name('common_config')->where(['name'=>'score_type'])->find();
        //print_r(json_decode($score_type['content'],true));exit;
        $score_type = json_decode($score_type['content'],true);
        $this->assign('scoretype',$score_type);
        $this->assign('show',$show);
        $this->assign('data',$list);
        return $this->fetch();
    }


    /*
     * ajax获取会员积分
     * @param phone账号
     * */
    public function getScore(){
        $phone = input('phone');
        $info = Db::name('user')->field('score')->where(['phone'=>$phone,'is_delete'=>0])->find();
        if(!empty($info)){
            if($info['status'] == 1){
                return json_encode(array('status'=>1,'msg'=>'成功','score'=>$info['score']));
            }else{
                return json_encode(array('status'=>0,'msg'=>'当前账号已封禁'));
            }
        }else{
            return json_encode(array('status'=>0,'msg'=>'当前账号不存在'));
        }
    }

}