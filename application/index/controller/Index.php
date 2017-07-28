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
    	self::$goods_type	=	'flash_sale';
        $goods_list =   $this->get_goods_list(2); ##2为限时抢购

        $data   =   array(
                        'goods_list'    =>  $goods_list,
                        'goods_type'    =>  self::$goods_type,
                    );
        $this->assign($data);
        
        return $this->fetch();
    }

    /**
     * 直播单
     */
    public function live(){
        self::$goods_type   =   'live';
        $goods_list =   $this->get_goods_list(3); ##2为限时抢购

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
        $goods_list =   $this->get_goods_list(4); ##2为限时抢购

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
        $goods_list =   $this->get_goods_list(5); ##2为限时抢购

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
     * 获取商品信息
     * @param  [type]  $goodsType [description]
     * @param  integer $start     [description]
     * @param  [type]  $nums      [description]
     * @return [type]             [description]
     */
    function get_goods_list($goodsType, $start = 0, $nums = 20){
        $where  =   array('type'=>$goodsType, 'status'=>2);
        $goods  =   Db::table('goods')->where($where)->order('id', 'desc')->limit($start, $nums)->select();
        return $goods;
    }
}
