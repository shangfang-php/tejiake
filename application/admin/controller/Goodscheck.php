<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Paginator;
use think\Db;
class Goodscheck extends Common{
    /*
  * 商品审核 类型1爆款单2限时抢购3过夜单4直播单5视频单
  * */
    public function index(){
        return $this->fetch();
    }

    //查看详情 申请审核
    public function info(){
        if(request()->post()){
            $gid = input('post.gid');
            $info = Db::name('goods')->where(['id'=>$gid])->find();
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品不存在OR已删除')));
            }
            //修改审核状态
            $status = input('status');//2展示即审核通过 4即审核不通过
            $remark = input('remark');
            if($info['status'] == $status){
                exit(json_encode(array('status'=>0,'msg'=>'商品有误')));
            }
            $res = Db::name('goods')->where(['id'=>$gid])->update(['status'=>$status,'remark'=>$remark]);
            if($res){
                exit(json_encode(array('status'=>1,'msg'=>'成功')));
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'失败')));
            }
        }else{
            $gid = input('gid');//exit(json_encode(array('status'=>0,'msg'=>$gid)));
            $info = Db::name('goods')->where(['id'=>$gid])->find();
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品不存在OR已删除')));
            }
            //$gid = input('gid');
            //echo '<pre>';
            //print_r($info);exit;
            $this->assign('data',$info);
            return $this->fetch();
        }

    }
}