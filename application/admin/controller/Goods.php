<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Paginator;
use think\Db;
class Goods extends Common{
    /*
     * 商品管理
     * */
    public function index(){
        $type = input('type');
        if($type == NULL ||empty($type)||$type == 0){
            $type = 0;//默认是全部
        }
        $status = input('status');
        if($status == NULL ||empty($status)){
            $status = 0;//默认看到的就是全部状态
        }
        $condition['g.is_delete'] = 0;//正常显示
        //获取要查看的状态的类型的商品
        if($status == 0 && $type>0){
            $condition['g.type'] = $type;
            $condition['g.status'] = array('in',"2,3,5");
        }elseif($type == 0 && $status >0){
            $condition['g.status'] = $status;
        }elseif($type > 0 && $status >0){
            //$condition = array('g.status'=>$status,'g.type'=>$type);
            $condition['g.type'] = $type;
            $condition['g.status'] = $status;
        }elseif($status == 0 && $type == 0){
            $condition['g.status'] = array('in',"2,3,5");
        }
        //echo '<pre>';
       // print_r($condition);exit;
        //获取关键字
        $keyword = input('keyword');
        if(!empty($keyword)){
            $condition['g.title|u.phone'] = array('like',"%$keyword%");
        }
       $pagesize = 10;
        $goods = Db::name('goods')
            ->alias('g')
            ->field('g.*,u.phone')
            ->where($condition)
            ->order('g.id desc')
            ->join('user u','g.uid=u.id','left')
            ->paginate($pagesize,false,['query'=>request()->param()]);
        //分配当前的type及status
        $param = [
            'type'=>$type,
            'status'=>$status
        ];

        //echo '<pre>';
        //print_r($goods);exit;
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
           /* if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品不存在OR已删除')));
            }*/
            //$gid = input('gid');
            //echo '<pre>';
            //print_r($info);exit;
            $this->assign('data',$info);
            return view();
        }

    }

    /*
     * 删除当前商品 post传递
     * @param gid OR gids
     * */
    public function delGood(){
        $gid = input('post.gid');
        $gids = input('post.gids/a');
        $data = ['is_delete'=>1];
        //print_r($gids);exit;
        if(!empty($gid)){
            $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0])->find();
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品不存在OR已删除')));
            }
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
        }elseif(!empty($gids) && is_array($gids)){
            $gid_str = implode(',',$gids);
            $res = Db::name('goods')->where('id','in',$gid_str)->update($data);
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作有误')));
        }
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }

    /*
     * 商品管理下的修改 get传递
     * @param gid 商品ID
     * */
    public function edit(){
        if(request()->post()){
            //echo '<pre>';
            //print_r(request()->post());exit;
            $gid = input('post.gid');
            $data = [
                'link'               =>input('post.link'),
                'coupon_link'        =>input('post.coupon_link'),
                'title'              =>input('post.title'),
                'short_title'        =>input('post.short_title'),
                'real_money'         =>input('post.real_money'),
                'price'              =>input('post.price'),
                'sell_num'           =>input('post.sell_num'),
                'coupon_money'       =>input('post.coupon_money'),
                'coupon_total_num'   =>input('post.coupon_total_num'),
                'coupon_apply_num'   =>input('post.coupon_apply_num'),
                'plan_type'          =>input('post.plan_type'),
                'plan_link'          =>input('post.plan_link'),
                'taoke_money_percent'=>input('post.taoke_money_percent'),
                'guide_info'         =>input('post.guide_info'),
                'submit_message'     =>input('post.submit_message')
            ];
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
              if($res){
                  //exit(json_encode(array('status'=>1,'msg'=>'成功')));
                  //$this->redirect(url('Goods'));
                  echo '<script> window.location.href = history.go(-1);</script>';exit;
              }else{
                  $this->error('修改失败');
                  //exit(json_encode(array('status'=>0,'msg'=>'失败')));
              }

        }else{
            $gid = input('gid');
            $info = Db::name('goods')
                  ->alias('g')
                  ->field('g.*,u.phone')
                  ->join('user u','g.uid=u.id','left')
                  ->find($gid);
            //echo '<pre>';
            //print_r($info);exit;
            $this->assign('data',$info);
            return view();
        }
    }

    /*
     * 获取各类型的需审核的数据
     * @param type 1爆款单2限时抢购3过夜单4直播单5视频单
     * */
    public function getCheck(){
        $type = input('type');
        $condition['g.type'] = $type;
        $condition['g.status'] = 1;
        $condition['g.is_delete'] = 0;
        $keyword = input('keyword');
        if(!empty($keyword)){
            $condition['g.short_title'] = array("like","%$keyword%");
        }
        /*$list = Db::name('goods')
            ->where(['type'=>$type,'status'=>1,'is_delete'=>0])
            ->paginate(10,false,['query'=>request()->param()]);*/
        $list = Db::name('goods')
            ->alias('g')->field('g.*,u.phone')
            ->where($condition)
            ->join('user u','g.uid=u.id','left')
            ->paginate(10,false,['query'=>request()->param()]);
        $show = $list->render();
        $this->assign('data',$list);
        $this->assign('type',$type);
        $this->assign('show',$show);
        return $this->fetch();
    }

    /*
     * 审核不通过---拒绝 post传递
     * @param gid OR gids
     * */
    public function rejectGood(){
        $gid = input('post.gid');
        $gids = input('post.gids/a');
        $data = ['status'=>4];
        //print_r($gids);exit;
        if(!empty($gid)){
            $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0,'status'=>1])->find();
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品数据有误')));
            }
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
        }elseif(!empty($gids) && is_array($gids)){
            $gid_str = implode(',',$gids);
            $res = Db::name('goods')->where('id','in',$gid_str)->update($data);
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作有误')));
        }
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }

    /*
     * 商品通过审核 post传递
     * @param gid OR gids
     * */
    public function passGood(){
        $gid = input('post.gid');
        $gids = input('post.gids/a');
        $data = ['status'=>2];
        //print_r($gids);exit;
        if(!empty($gid)){
            $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0,'status'=>1])->find();
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品数据有误')));
            }
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
        }elseif(!empty($gids) && is_array($gids)){
            $gid_str = implode(',',$gids);
            $res = Db::name('goods')->where('id','in',$gid_str)->update($data);
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作有误')));
        }
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }

    /*
     * 修改商品的审核状态OR删除 post传递
     * @param type 删除1 通过2 拒绝3
     * @param gid OR gids
     * */
    public function changeStatus(){
        $type = input('type');
        $gid = input('post.gid');
        $gids = input('post.gids/a');
        if($type == 1){
            $data = ['is_delete'=>1];//修改删除状态
        }elseif($type == 2){
            $data = ['status'=>2];//通过审核
        }elseif($type == 3){
            $remark = input('remark');
            $data = ['status'=>4,'remark'=>$remark];//商品的审核不通过
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作有误')));
        }
        if(!empty($gid)){
            if($type == 1){
                $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0])->find();
            }else{
                $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0,'status'=>1])->find();
            }
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'商品数据有误')));
            }
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
        }elseif(!empty($gids) && is_array($gids)){
            $gid_str = implode(',',$gids);
            $res = Db::name('goods')->where('id','in',$gid_str)->update($data);
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作有误')));
        }
        if($res){
            if($type == 3){//拒绝要扣分
                $score = intval(input('score'));
                $info2 = Db::name('goods')
                    ->alias('g')
                    ->field('g.*,u.id,u.score')
                    ->where(['g.id'=>$gid,'g.is_delete'=>0])
                    ->join('user u','g.uid=u.id','left')
                    ->find();
                if($score > $info2['score']){
                    $score = $info2['score'];
                    $diff = 0;
                }else{
                    $diff = $info2['score']-$score;
                }
                Db::name('user_score_record')->insert(['uid'=>$info2['uid'],'score'=>$score,'type'=>4,'remark'=>$remark,'time'=>time(),'operator'=>$this->admininfo['id']]);
                Db::name('user')->where(['id'=>$info2['uid']])->update(['score'=>$diff]);
            }
            exit(json_encode(array('status'=>1,'msg'=>'成功')));

        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }

    /*
     * 商品纠错列表
     * */
    public function wrong(){
        $list = Db::name('goods')
            ->alias('g')->field('g.*,u.phone')
            //->where(['g.remark'=>array('neq','null')])
            ->where(['g.status'=>4])
            ->join('user u','g.uid=u.id','left')
            ->order('id desc')
            ->paginate(10,false,['query'=>request()->param()]);
        $show = $list->render();
        $this->assign('show',$show);
        $this->assign('data',$list);
        return $this->fetch();
    }

    /*
     * 商品修改1 跳转页面 get 获取ID
     * @param gid
     * */
    public function edit1(){
        if(request()->post()){
            //获取时间
            $timet = input('post.timet');
            $dated = input('post.dated');
            $tt = strtotime($dated.$timet);
            $dd = date('Y-m-d H:i:s',$tt);
            //获取图片数据
            $ff = input('file.images/a');

            echo '<pre>';
            print_r($ff);exit;
            $gid = input('post.gid');
            $data = [
                'link'               =>input('post.link'),
                'coupon_link'        =>input('post.coupon_link'),
                'title'              =>input('post.title'),
                'short_title'        =>input('post.short_title'),
                'real_money'         =>input('post.real_money'),
                'price'              =>input('post.price'),
                'sell_num'           =>input('post.sell_num'),
                'coupon_money'       =>input('post.coupon_money'),
                'coupon_total_num'   =>input('post.coupon_total_num'),
                'coupon_apply_num'   =>input('post.coupon_apply_num'),
                'plan_type'          =>input('post.plan_type'),
                'plan_link'          =>input('post.plan_link'),
                'taoke_money_percent'=>input('post.taoke_money_percent'),
                'guide_info'         =>input('post.guide_info'),
                'submit_message'     =>input('post.submit_message')
            ];
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
            if($res){
                //exit(json_encode(array('status'=>1,'msg'=>'成功')));
                //$this->redirect(url('Goods'));
                echo '<script> window.location.href = history.go(-1);</script>';exit;
            }else{
                $this->error('修改失败');
                //exit(json_encode(array('status'=>0,'msg'=>'失败')));
            }

        }else{
            $gid = input('gid');
            $info = Db::name('goods')
                ->alias('g')
                ->field('g.*,u.phone')
                ->join('user u','g.uid=u.id','left')
                ->join('goods_extend ge','ge.gid = g.id','left')
                ->find($gid);
           // echo '<pre>';
            //print_r($info);exit;
            $this->assign('data',$info);
            return view();
        }
        return $this->fetch();
    }

}