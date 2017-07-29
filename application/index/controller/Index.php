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
}
