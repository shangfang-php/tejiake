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
        $goods_num = Db::name('goods')
            ->alias('g')
            ->field('g.*,u.phone')
            ->where($condition)
            ->join('user u','g.uid=u.id','left')
            ->count();
        //echo '<pre>';
        //print_r($goods);exit;
        $show = $goods->render();
        $data = [
            'data'=>$goods,
            'param'=>$param,
            'show'=>$show,
            'goods_num'=>$goods_num
        ];
        $this->assign($data);
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
        $num = Db::name('goods')->where(['type'=>$type,'status'=>1,'is_delete'=>0])->count();
        $data = [
            'data'=>$list,
            'type'=>$type,
            'show'=>$show,
            'num'=>$num
        ];
        $this->assign($data);
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
        $admin_uid  =   $this->admininfo['id'];
        $type   = input('type');
        $gid    = intval(trim(input('post.gid')));
        $gids   = input('post.gids/a');
        $score  = intval(trim(input('post.score')));
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

        /**合并gid和gids**/
        $goods_ids  =   array();
        $gid && $goods_ids[] = $gid;
        ( !empty($gids) && is_array($gids) ) && $goods_ids = array_merge($goods_ids, $gids);
        if(empty($goods_ids)){
            exit(json_encode(array('status'=>0,'msg'=>'没有要操作的商品')));
        }

        Db::startTrans();
        $info   =   Db::name('goods')->where('id','in',$goods_ids)->update($data);
        if($info === false){
            DB::rollback();
            exit(json_encode(array('status'=>0,'msg'=>'更新状态失败!')));
        }

        if($type == 3){ ##拒绝操作
            /***循环更新积分**/
            foreach($goods_ids as $goods_id){
                $goods_info     =   Db::table('goods')->field('id,status,uid,scores')->where('id', $goods_id)->find();
                if(empty($goods_info)){
                    continue;
                }
                $uid        =   $goods_info['uid'];
                $goods_score=   $goods_info['scores'];
                if($score){
                    $info   =   updateUserScore($uid, '-'.$score, 4, '发布商品'.$goods_id.'未通过,扣除积分',$admin_uid, 0, $goods_id);
                    if(!$info){
                        Db::rollback();
                        exit(json_encode(array('status'=>0,'msg'=>'扣除用户积分失败!')));
                    }
                }

                $info   =   updateUserScore($uid, $goods_score, 6, '审核未通过返还冻结积分',$admin_uid, '-'.$goods_score, $goods_id);
                if(!$info){
                    Db::rollback();
                    exit(json_encode(array('status'=>0,'msg'=>'返还冻结积分失败!')));
                }
            }
        }

        Db::commit();
        exit(json_encode(array('status'=>1,'msg'=>'成功')));
    }

    /*
     * 商品纠错列表
     * */
    public function wrong(){
        $status = input('status');

        if( $status == NULL || empty($status)){
            $status = 0;//默认看到的就是全部状态
        }
        $keyword = input('keyword');
        if(!empty($keyword)){
            $condition['g.short_title'] = array('like',"%$keyword%");
        }
        $condition = array();
        if($status > 0){//已处理
            $condition['ge.status'] = $status;
        }
        $list = Db::name('goods_error_recovery')
            ->alias('ge')
            ->field('ge.*,u.phone,g.short_title')
            //->where(['g.remark'=>array('neq','null')])
            ->where($condition)
            ->join('user u','ge.uid=u.id','left')
            ->join('goods g','ge.gid=g.id','left')
            ->order('id desc')
            ->paginate(10,false,['query'=>request()->param()]);
        $show = $list->render();
        $data = [
            'show'=>$show,
            'data'=>$list,
            'type'=>$status
        ];
        $this->assign($data);
        return $this->fetch();
    }

    /*
     * 处理纠错
     * @param geids
     * */
    public function handleError(){
        $geids = input('post.geids/a');
       /* $ids = implode(',',$geids);*/
        $data = ['status'=>1];
        $res = Db::name('goods_error_recovery')->where('id','in',$geids)->update($data);
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(0,'失败');
        }

    }

    /*
     * 商品修改1 跳转页面 get 获取ID  1爆款单2限时抢购3过夜单4直播单5视频单
     * @param gid
     * */
    public function edit1(){
        if(request()->post()){
            //获取时间
            $data = input('post.');
            $data['coupon_start_time'] = @strtotime($data['coupon_start_time']);
            $data['coupon_end_time'] = @strtotime($data['coupon_end_time']);
            $data['start_time'] = @strtotime($data['start_time']);
            $data['end_time'] = @strtotime($data['end_time']);
            $gid = $data['gid'];
            $goodsinfo = Db::name('goods')->find($gid);
            //$long_img = input('file.long_img');
            if(input('file.long_img')){
                $file = request()->file('long_img');
                // 移动到框架应用根目录/public/uploads/ 目录下
                $dir = ROOT_PATH . "public/static". DS ."goods". DS .$goodsinfo['uid'];
                if (!is_dir($dir)){
                    //createDir(dirname($dir));
                    mkdir($dir, 0777,true);
                }
                $info = $file->move($dir);
                if($info){
                    // 成功上传后 获取上传信息
                    $data['long_img'] = "/static". DS ."goods". DS .$goodsinfo['uid'].DS.$info->getSaveName();
                }else{
                    // 上传失败获取错误信息
                    $data['long_img'] = '';
                }
            }
            unset($data['gid']);
            //保存视频扩展信信息
            if($goodsinfo['type'] == 5 && !empty($data['video_url'])){
                $video_url = $data['video_url'];
                Db::name('goods_video_extends')->where(['gid'=>$gid])->update(['video_url'=>$video_url]);
            }
            unset($data['video_url']);
            //保存直播扩展信息
            if($goodsinfo['type'] == 4){
                //$pic_url = input('file.pic_url');
                //获取直播图文
                if(input('file.pic_url')){
                    $pic_content = $data['pic_content'];
                    $pic_url = request()->file('pic_url');
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $live_dir = ROOT_PATH . "public/static". DS ."live". DS .$goodsinfo['uid'];
                    if (!is_dir($live_dir)){
                        mkdir($live_dir, 0777,true);
                    }
                    $live_data = array();
                    //$live_url = $pic_url->move($live_dir);
                    foreach($pic_url as $k=>$v){
                        // 移动到框架应用根目录/public/uploads/ 目录下
                        $pic = $v->move($live_dir);
                        if($pic){
                            // 成功上传后 获取上传信息
                            $live_data[$k]['url'] = "/static". DS ."live". DS .$goodsinfo['uid'].DS.$pic->getSaveName();
                            $live_data[$k]['type'] = 1;
                            $live_data[$k]['content'] = $pic_content[$k];
                            $live_data[$k]['gid'] = $gid;
                            $live_data[$k]['create_time'] = time();
                        }
                    }
                }else{
                    $live_data = array();
                }
                //直播视频
                if(!empty($data['video_url'])){
                    $video_data = array();
                    $video_url = $data['video_url'];
                    foreach($video_url as $k=>$v){
                        $video_data[$k]['url'] = $v;
                        $video_data[$k]['type'] = 2;
                        $video_data[$k]['content'] = '';
                        $video_data[$k]['gid'] = $gid;
                        $video_data[$k]['create_time'] = time();
                    }
                }else{
                    $video_data = array();
                }
                $data_live = array();
                if(!empty($live_data)&&!empty($video_data)){
                    $data_live = array_merge($live_data,$video_data);
                }elseif(empty($live_data)&&!empty($video_data)){
                    $data_live = $video_data;
                }elseif(!empty($live_data)&&empty($video_data)){
                    $data_live = $live_data;
                }
                if(!empty($data_live)){
                    Db::startTrans();
                    $res1 = Db::name('goods_live_extends')->where(['gid'=>$gid])->delete();
                    $res2 = Db::name('goods_live_extends')->insertAll($data_live);
                    if($res1 && $res2){
                        Db::commit();
                    }else{
                        Db::rollback();
                    }
                }
                //echo '<pre>';
                //print_r($data_live);exit;
            }
            unset($data['pic_content']);
            unset($data['video_url']);
            //echo '<pre>';
            //print_r($data);exit;
            $res = Db::name('goods')->where(['id'=>$gid])->update($data);
            //if($res){
                //exit(json_encode(array('status'=>1,'msg'=>'成功')));
                //$this->redirect(url('Goods'));
                echo '<script> window.location.href = history.go(-1);</script>';exit;
           // }/*else{
               // $this->error('修改失败');
                //exit(json_encode(array('status'=>0,'msg'=>'失败')));
           // }*/
        }else{
            $gid = input('gid');
            $info = Db::name('goods')
                ->alias('g')
                ->field('g.*,u.phone')
                ->join('user u','g.uid=u.id','left')
                /*->join('goods_extend ge','ge.gid = g.id','left')*/
                ->find($gid);
            //echo '<pre>';
            //print_r($info);exit;  //getGoodsExtendsInfo获取扩展数据
            $goods_images = Db::name('goods_image')->where(['gid'=>$gid,'is_delete'=>0])->select();
            //获取直播或者视频单的扩展数据
            $goods_live_extend = getGoodsExtendsInfo($gid,4);
            $goods_video_extend = getGoodsExtendsInfo($gid,5);
            $data = [
                'data'=>$info,
                'goods_images'=>$goods_images,
                'live_data' =>$goods_live_extend,
                'video_data' =>$goods_video_extend,
            ];
            $this->assign($data);
            return $this->fetch();
            //return view();
        }
    }

}