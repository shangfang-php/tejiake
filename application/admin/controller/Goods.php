<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Paginator;
use think\Db;
class Goods extends Common{
    public function index(){
        $type = input('type');
        if($type == NULL ||empty($type)){
            $type = 0;//默认是全部
        }
        $status = input('status');
        if($status == NULL ||empty($status)){
            $status = 0;//默认看到的就是全部状态
        }
        $condition['is_delete'] = 0;//正常显示
        //获取要查看的状态的类型的商品
        if($status == 0 && $type>0){
            $condition = array('g.type'=>$type);
        }elseif($type == 0 && $status >0){
            $condition = array('g.status'=>$status);
        }elseif($type > 0 && $status >0){
            $condition = array('g.status'=>$status,'g.type'=>$type);
        }
        //获取关键字
        $keyword = input('keyword');
        if(!empty($keyword)){
            $condition['g.title|u.phone'] = array('like',"%$keyword%");
        }
       $pagesize = 1;
        $goods = Db::name('goods')
            ->alias('g')->field('g.*,u.phone')
            ->where($condition)
            ->join('user u','g.uid=u.id','left')
            ->paginate($pagesize,false,['query'=>request()->param()]);
        //分配当前的type及status
        $param = [
            'type'=>$type,
            'status'=>$status
        ];
        $show = $goods->render();
        $this->assign('data',$goods);
        $this->assign('param',$param);
        $this->assign('show',$show);
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
    //删除当前商品
    public function delGood(){
        $gid = input('gid');
        $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0])->find();
        if(empty($info)){
            exit(json_encode(array('status'=>0,'msg'=>'商品不存在OR已删除')));
        }
        $res = Db::name('goods')->where(['id'=>$gid])->update(['is_delete'=>1]);
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }
}