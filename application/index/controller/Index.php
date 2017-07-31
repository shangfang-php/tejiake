<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Common{
    private $online_num = 0; ##在线人数

    public function _initialize(){
        parent::_initialize();
        $this->online_num   =   getRandTime();
        $this->assign('online_num', $this->online_num);
    }
	
    public function index()
    {	
        $goods_list =   $this->get_goods_list(1); ##1为爆款单

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                    );
        $this->assign($data);
    	
        return $this->fetch();
    }

    /**
     * 限时抢购
     */
    public function flash_sale(){
        $type               =   isset($_GET['type']) ? intval(trim(input('get.type'))) : 1; ##1正在抢购 2预告
    	self::$goods_type	=	'flash_sale';
        $goods_list =   $this->get_goods_list(2,$type); ##2为限时抢购
        if($goods_list){
            foreach($goods_list as &$val){
                $diff_times = $type == 1 ? $val['end_time'] : $val['show_time'];
                $val['diff_times'] = date('m/d/Y H:i:s', $diff_times);

                preg_match('/(.*)\s/', $val['guide_info'], $matches);
                $val['short_guide'] =   $matches ? $matches[1] : '';
            }
        }
        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'type'          =>  $type,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 直播单
     */
    public function live(){
        self::$goods_type   =   'live';
        $goods_list =   $this->get_goods_list(4); ##2为限时抢购
        if($goods_list){
            foreach($goods_list as &$val){
                $goods_id   =   $val['id'];
                $val['diff_time'] = ( $val['show_time'] > time() ) ? date('m/d/Y H:i:s', $val['show_time']) : '';
                $live_count =   Db::table('goods_live_extends')->field('count(*) as nums, type')->where('gid',$goods_id)->group('type')->select();
                $count  =   [1=>0,2=>0];
                if($live_count){
                    foreach($live_count as $v){
                        $count[$v['type']]  =   $v['nums'];
                    }
                }
                $val['live_count']  =   $count;
            }
        }

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 视频单
     * @return [type] [description]
     */
    public function video(){
        self::$goods_type   =   'video';
        $goods_list =   $this->get_goods_list(5); ##5为视频单

        /**获取视频列表*/
        if($goods_list){
            $gids       =   array_column($goods_list, 'id'); ##php5.5及以上才有该函数 获取数组指定字段集合
            $video_urls =   Db::table('goods_video_extends')->field('gid,video_url')->where(['gid'=>['in',$gids]])->select();
            $video_urls =   reverse_array($video_urls, 'gid', 'video_url');
        }else{
            $video_urls =   array();
        }
        

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'video_urls'    =>  $video_urls,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 过夜单
     * @return [type] [description]
     */
    public function night(){
        self::$goods_type   =   'night';
        $goods_list =   $this->get_goods_list(3); ##3为过夜单

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 展示具体商品信息
     * @return [type] [description]
     */
    public function detail(){
    	$online_num	=	getRandTime();
    	$this->assign('online_num', $online_num);
        return $this->fetch();
    }

    /**
     * 获取展示的产品信息
     * @param  [type]  $goodsType  [产品类型]
     * @param  integer $start      [从第几条开始获取]
     * @param  integer $nums       [获取的条数]
     * @param  integer $flash_type [限时抢购的类型]
     * @return [type]              [description]
     */
    function get_goods_list($goodsType, $flash_type = 1, $start = 0, $nums = 20){
        $where  =   array('type'=>$goodsType, 'status'=>2, 'end_time'=>['>=',time()]);
        if($goodsType == 2){
            if($flash_type == 1){
                $where['show_time'] =   ['<=', time()];
            }else{
                $where['show_time'] =   ['>', time()];
            }
        }
        $goods  =   Db::table('goods')->where($where)->order('id', 'desc')->limit($start, $nums)->select();
        return $goods;
    }

    /*
      * 商品详情 get传递数据
      * @param id商品ID
      * http://ceshi.tejiake.com/goods?id=5
      * */
    public function goods(){
        $id = input('get.id');
        $info = Db::name('goods')
            ->alias('g')
            ->field('g.*,u.head_img,u.create_time ucreate_time,m.nickname,m.type mtype')
            ->join('user u','g.uid=u.id','left')
            ->join('merchant_apply_record m','g.uid=m.uid','left')
            ->where(['g.id'=>$id,'g.is_delete'=>0])
            ->find();
        if(empty($info)){
            $this->error('数据有误');
        }

        //$info['head_img'] = '__STATIC__' . DS . 'user_temp'.DS.'avatar.png';//头像
        $info['addday'] = ceil((time()-$info['ucreate_time'])/86400);//入驻天数
        $goods_num = Db::name('goods')->where("uid=".$info['uid']." and is_delete=0 and status=2 and start_time<=".time()." and end_time>=".time()."")->count();
        $info['line_goods_num'] = $goods_num;//线上商品 状态为展示中 当前时间在商品的活动期

        if(self::$login_user){
            $uid = self::$login_user['id'];
            $collect_info = Db::name('goods_collect')->where(['is_spread'=>0,'gid'=>$id,'uid'=>$uid])->find();//当前商品是否被当前用户所收藏
            if(!empty($collect_info)){
                $info['is_collect'] = 1;
            }else{
                $info['is_collect'] = 0;
            }
        }else{
            $info['is_collect'] = 0;
        }
        //1爆款单2限时抢购3过夜单4直播单5视频单
        if($info['type'] == 1){
            $goods_type = 'hot';
        }elseif($info['type'] == 2){
            $goods_type = 'flash_sale';
        }elseif($info['type'] == 3){
            $goods_type = 'night';
        }elseif($info['type'] == 4){
            $goods_type = 'live';
        }else{
            $goods_type = 'video';
        }
       // echo '<pre>';
        //print_r($goods_type);exit;
        $this->assign('goods_type', $goods_type);
        $this->assign('data',$info);
        
        if($info['type'] == 4){
            //直播单
            $goods_live_extends = getGoodsExtendsInfo($id,4);
            //$goods_live_extends = Db::name('goods_live_extends')->where(['gid'=>$id])->select();
            $this->assign('extends_data',$goods_live_extends);

            return $this->fetch('liveinfo');
        }else{
            if($info['type'] == 5){
                $goods_video_extends = getGoodsExtendsInfo($id,5);
                //$goods_video_extends = Db::name('goods_video_extends')->where(['gid'=>$id])->select();
                $this->assign('extends_data',$goods_video_extends);
                //echo '<pre>';
                //print_r($goods_video_extends);exit;
            }
            //获取多图
            $images = Db::name('goods_image')->where(['gid'=>$id])->select();
            $this->assign('goods_image',$images);
            return $this->fetch('goodsinfo');
        }

    }

    /*
     * 添加收藏
     * @param gid商品ID
     * */
    public function addCollect(){
        $gid = input('get.gid');
        if(!self::$login_user){
            return returnAjaxMsg(101,'失败',array('url'=>"/user_login"));
        }
        $uid = self::$login_user['id'];
        $info = Db::name('goods')->where(['is_delete'=>0,'id'=>$gid])->find();
        if(empty($info)){
            return returnAjaxMsg(0,'数据有误');
        }
        $data = [
            'gid'=>$gid,
            'uid'=>$uid,
            'time'=>time(),
        ];
        $res = Db::name('goods_collect')->insert($data);
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(0,'失败');
        }
    }

}
