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
        $web_title            =   '爆款单';
        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'web_title'     =>  $web_title,
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
        $web_title          =   '限时抢购';
        $goods_list =   $this->get_goods_list(2,$type); ##2为限时抢购

        $extends    =   array();
        if($goods_list){
            foreach($goods_list as &$val){
                $id     =   $val['id'];
                $diff_times = $type == 1 ? $val['end_time'] : $val['show_time'];
                $extends[$id]['diff_times'] = date('m/d/Y H:i:s', $diff_times);

                preg_match('/(.*)\s\S/U', $val['guide_info'], $matches);
                $extends[$id]['short_guide'] =   $matches ? $matches[1] : '';
            }
        }
        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'type'          =>  $type,
                        'extends'       =>  $extends,
                        'web_title'     =>  $web_title,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 直播单
     */
    public function live(){
        self::$goods_type   =   'live';
        $web_title          =   '直播单';
        $goods_list =   $this->get_goods_list(4,0,30); ##4为直播单
        $extends    =   array();
        if($goods_list){
            foreach($goods_list as $val){
                $goods_id   =   $val['id'];
                $extends[$goods_id]['diff_time'] = ( $val['show_time'] > time() ) ? date('m/d/Y H:i:s', $val['show_time']) : '';

                $live_count =   Db::table('goods_live_extends')->field('count(*) as nums, type')->where('gid',$goods_id)->group('type')->select();
                $count  =   [1=>0,2=>0];
                if($live_count){
                    foreach($live_count as $v){
                        $count[$v['type']]  =   $v['nums'];
                    }
                }
                $extends[$goods_id]['live_count']   =   $count;
                //$val['live_count']  =   $count;
            }
        }

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'extends'       =>  $extends,
                        'web_title'     =>  $web_title,
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
        $web_title          =   '视频单';
        $goods_list =   $this->get_goods_list(5); ##5为视频单

        /**获取视频列表*/
        if($goods_list){
            $gids       =   array_column($goods_list->items(), 'id'); ##php5.5及以上才有该函数 获取数组指定字段集合
            $video_urls =   Db::table('goods_video_extends')->field('gid,video_url')->where(['gid'=>['in',$gids]])->select();
            $video_urls =   reverse_array($video_urls, 'gid', 'video_url');
        }else{
            $video_urls =   array();
        }
        

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'video_urls'    =>  $video_urls,
                        'web_title'     =>  $web_title,
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
        $web_title          =   '过夜单';
        $goods_list =   $this->get_goods_list(3); ##3为过夜单

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                        'web_title'     =>  $web_title,
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
    function get_goods_list($goodsType, $flash_type = 1, $nums = 40){
        $where  =   array('type'=>$goodsType, 'status'=>2, 'end_time'=>['>=',time()]);

        if($goodsType == 2){
            if($flash_type == 1){
                $where['show_time'] =   ['<=', time()];
                $order              =   ['end_time'=>'asc']; ##快结束的优先
            }else{
                $where['show_time'] =   ['>', time()];
                $order              =   ['show_time'=>'asc']; ##快开始的优先
            }
        }else{
            $order  =   ['id'=>'desc'];
        }
        $goods  =   Db::table('goods')->where($where)->order($order)->paginate($nums, false,['query'=>$_GET]);
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
            $this->redirect('news/no_goods');
            //$this->error('数据有误');
        }

        //$info['head_img'] = '__STATIC__' . DS . 'user'.DS.$info['head_img'];//头像
        $info['head_img'] = $info['head_img'] ? '__STATIC__' . DS . 'user'.DS.$info['head_img'] : '';//头像
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
        $web_title  =   $info['title'];
        $this->assign('web_title', $web_title);
        
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
            //echo '<pre>';
            //print_r($images);exit;
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
        //print_r($gid);exit;
        if(empty($info)){
            return returnAjaxMsg(0,'数据有误');
        }
        //不可重复收藏
        $spread_info = Db::name('goods_collect')->where(['is_spread'=>0,'gid'=>$gid,'uid'=>$uid])->find();
        if(!empty($spread_info)){
            return returnAjaxMsg(0,'已收藏');
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

    /*
     * 一键转链
     * */
    public function change_link(){
        /* $tkl = '￥yc4h01iBqa8￥';
         $surl = 'http://00o.cn/4KaAPlb ';
         $sata = [
             'coupon_url' => $surl,
             'surl'       => $tkl,
         ];
        $data = "<br/>复制这条信息,".$tkl.",打开【手机淘宝】即可领券下单<a href=".$surl." target='_blank' class='lan'>".$surl."</a>";
        //复制这条信息，￥M4xj01ih3gt￥，打开【手机淘宝】即可领券下单<a href="http://00o.cn/4KaGXfB" target="_blank" class="lan">http://00o.cn/4KaGXfB</a>
        return returnAjaxMsg(200,'成功',array('data'=>$data));*/
        $gid = input('get.gid');
        if(!self::$login_user){
            return returnAjaxMsg(101,'失败');//未登陆
        }
        $uid = self::$login_user['id'];
        //获取当前用户信息
        $username = self::$login_user['taobao_account'];
        $pidinfo = Db::name('user_pid_records')->where(['uid'=>$uid])->find();
        if(empty($pidinfo)){
            return returnAjaxMsg(102,'失败');//PID为空
        }
        $pid = $pidinfo['pid'];
        $pid_arr = explode('_',$pid);
        $memberid = $pid_arr[1];
        //print_r($memberid);
        $param1 = [
            'username'=>$username,
            'memberid'=>$memberid
        ];
        $dat1 = request_post("http://api.00o.cn/info.php",$param1);
        $data1 = json_decode($dat1,true);
        if($data1['code'] == 1){//200
            //成功 轉鏈 根据高佣API获取当前商品数据
            /*
             * 获取参数
             * @param item_id淘客商品id
             * @param adzone_id 推广位id，mm_xx_xx_xx pid三段式中的第三段
             * @param site_id 备案的网站id, mm_xx_xx_xx pid三段式中的第二段
             * */
            $info = Db::name('goods')->where(['id'=>$gid])->find();
            if(empty($info)){
                return returnAjaxMsg(105,'数据有误');//商品为空
            }
            $item_id = $info['taobao_goodsId'];
            $adzone_id = $pid_arr[3];
            $site_id = $pid_arr[2];
            $param2 = [
                'token'=>$data1['token'],
                'item_id'=> $item_id,
                'adzone_id'=> $adzone_id,
                'site_id'=> $site_id,
            ];
            $dat2 = request_post("http://api.00o.cn/highapi.php",$param2);
            $data2 = json_decode($dat2,true);
            //成功返回result 失败返回错误原因
            if(isset($data2['result'])){
                //获取出转链的短网址---->复制这条信息，￥M4xj01ih3gt￥，打开【手机淘宝】即可领券下单http://00o.cn/4KaGXfB
                $coupon_click_url = $gy_data['result']['data']['coupon_click_url'];
                if(!empty($info['coupon_id'])){
                    $url_uland = $coupon_click_url."&activityId=".$info['coupon_id'];
                }else{
                    $url_uland = $coupon_click_url;
                }

                //$url_uland = $gy_data
                $tkl_post['url'] = urlencode($url_uland);
                $tkl_post['logo'] = $info['main_img'];
                $tkl_post['title'] = $info['title'];
                $tkl = request_post('http://kl.00o.cn/index.php',$tkl_post);
                $surl = request_post('http://00o.cn/api.php',array('smallurl'=>$url_uland));
                if(!$surl){
                    $surl = '';
                }else{
                    $surl = json_decode($surl,true);
                    $surl = $surl['url'];
                }
                $data = "<br/>复制这条信息,".$tkl.",打开【手机淘宝】即可领券下单<a href=".$surl." target='_blank' class='lan'>".$surl."</a>";
                //复制这条信息，￥M4xj01ih3gt￥，打开【手机淘宝】即可领券下单<a href="http://00o.cn/4KaGXfB" target="_blank" class="lan">http://00o.cn/4KaGXfB</a>
                return returnAjaxMsg(200,'成功',array('data'=>$data));
            }else{
                return returnAjaxMsg(104,'PID有误1');
            }
        }elseif($data1['code'] == 2){
            //已過期
            return returnAjaxMsg(103,'失败');
        }else{
            //pid有误
            return returnAjaxMsg(104,'PID有误2');
        }
    }

    /*
     * 商品纠错
     * @param gid 商品ID
     * @param uid 用户ID
     * */
    public function addErrorRecovery(){
        if(!self::$login_user){
            return returnAjaxMsg(101,'未登录');//未登陆
        }
        $uid = self::$login_user['id'];
        $gid = input('post.gid');
        $info = Db::name('goods')->where(['id'=>$gid,'is_delete'=>0])->find();
        if(empty($info)){
            return returnAjaxMsg(101,'商品有误');
        }
        $error_info = Db::name('goods_error_recovery')->where(['uid'=>$uid,'gid'=>$gid])->find();
        if(!empty($error_info)){
            return returnAjaxMsg(101,'不可重复提交');
        }
        $error = input('post.error_reason');
        if(empty($error)){
            return returnAjaxMsg(101,'理由不可为空');
        }
        $data = [
            'uid'          => $uid,
            'gid'          => $gid,
            'type'         => input('post.type'),
            'error_reason' => $error,
            'create_time'  => time()
        ];
        $res = Db::name('goods_error_recovery')->insert($data);
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(101,'失败');
        }
    }

    /**
     * 商品搜索
     */
    public function search(){
        $web_title          =   '搜索';    
        $keywords = trim(input('get.keywords'));
        $goods = Db::name('goods');
        //var_dump($keywords);exit;
        if ( empty($keywords) ) {
            $this->redirect('index/index');
            //return file_get_contents(url('index/index/index','',true,true)); 
        } else {

            if ( is_numeric($keywords) ) {
                $where['taobao_goodsId'] = array('eq', $keywords);//根据ID查询
            } else {
                $where['title'] = array('like', '%'.$keywords.'%');//模糊查询
            }
            $where['status']    =   2;
            $goods_list = $goods->where($where)//分页带查询条件
                                    ->paginate(40, false, [
                                     'query' => request()->param(),
                                ]);
            $nums   =   $goods_list->total();//统计数量
            $data   =   array(
                            'goods_list'    =>  $goods_list,
                            'keywords'      =>  $keywords,
                            'goods_type'    =>  '',
                            'nums'          =>  $nums,
                            'web_title'     =>  $web_title,
                        );
            $this->assign($data);

            return $this->fetch('search');
        }

    }

    /**
     * 招商团队展示
     * @Author   Gary
     * @DateTime 2017-08-05T15:32:52+0800
     * @return   [type]                   [description]
     */
    function team_show(){
        $uid    =   intval(trim(input('get.id')));
        if(!$uid){
            $this->redirect('news/no_goods');
        }

        ##团队信息
        $team_info  =   Db::table('merchant_apply_record')->where(array('uid'=>$uid, 'status'=>2))->find();
        if(empty($team_info)){
            $this->redirect('news/no_goods');
        }

        $user_info  =   Db::table('user')->where('id', $uid)->find();
        if(!$user_info){
            $this->redirect('news/no_goods');
        }


        $where       =   array('uid'=>$uid, 'status'=>2);
        $goods_data  =   Db::table('goods')->where($where)->paginate(40, false, [
                                     'query' => request()->param(),
                                ]);
            $nums    =   $goods_data->total();//统计数量
        $create_time =   $user_info['create_time'];
        $times       =   ceil((time()-$create_time)/86400);//入驻天数
        $data        =   array(
                            'goods_data'    =>  $goods_data,
                            'web_title'     =>  '团队展示',
                            'team_info'     =>  $team_info,
                            'user_info'     =>  $user_info,
                            'goods_type'    =>  '',
                            'nums'          =>  $nums,
                            'times'         =>  $times,
                        );
        $this->assign($data);
        return $this->fetch();
    }

}
