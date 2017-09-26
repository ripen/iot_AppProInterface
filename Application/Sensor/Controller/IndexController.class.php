<?php

namespace Sensor\Controller;
use Think\Controller;

/**
 * 多功能传感器信息化平台
 * 
 * @author wangyangyang
 */
class IndexController extends Controller{

	
	
 	public function __construct(){
		parent::__construct();

	}

	
    /**
     * 当前药店体检状态
     *
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
		$result	=	array();
		$result	=	M('sensor')->where()->order('id desc')->limit(15)->select();

		$this->assign('result',$result);
		$this->display();
    }

    /**
     * 当前药店体检状态
     *
     * @author wangyangyang
     * @version V1.0
     */
    public function add(){
		
		for($i = 1;$i <= 10 ; $i ++ ){
			$data 	=	array();

			$check 	=	false;

			$bp 	=	rand(0,2);
			$data['bp']	=	$bp;
			if ( $bp == 2 ) {
				$check 	=	true;
			}
			$gl 		=	$check ? '1' : rand(0,2) ;
			$data['gl']	=	$gl;

			if ( $gl == 2 ) {
				$check 	=	true;
			}
			$ox 		=	$check ? '1' : rand(0,2) ;
			$data['ox']	=	$ox;

			if ( $ox == 2 ) {
				$check 	=	true;
			}
			$ur 		=	$check ? '1' : rand(0,2) ;
			$data['ur']	=	$ur;

			if ( $ur == 2 ) {
				$check 	=	true;
			}
			$bf 		=	$check ? '1' : rand(0,2) ;
			$data['bf']	=	$bf;

			if ( $bf == 2 ) {
				$check 	=	true;
			}
			$el 		=	$check ? '1' : rand(0,2) ;
			$data['el']	=	$el ;

			if ( $el == 2 ) {
				$check 	=	true;
			}
			$we 		=	$check ? '1' : rand(0,2) ;
			$data['we']	=	$we;

			if ( $we == 2 ) {
				$check 	=	true;
			}
			$tm 		=	$check ? '1' : rand(0,2) ;
			$data['tm']	=	$tm;

			// 计算总体状态
			if ($bp == 0 && $gl == 0 && $ox == 0 && $ur == 0 && $bf == 0 && $el == 0 && $we == 0 && $tm == 0 ) {
				$data['status']	=	0;
			}elseif($bp == 1 || $gl == 1 || $ox == 1 || $ur == 1 || $bf == 1 || $el == 1 || $we == 1 || $tm == 0){
				$data['status']	=	2;
			}else{
				$data['status']	=	1;
			}

			$data['userid']		=	rand(1,1000);
			$data['time']		=	date('Y-m-d');
			$data['updatetime']	=	time() - ( $i * 6);
			$data['gateuuid']	=	'5c-cf-7f-81-a8-'.rand(10,99);
			$data['personid']	=	'13'.str_pad(1,8,rand(1,10000000),STR_PAD_LEFT);

			M("sensor")->add($data);
		}
		redirect('/sensor');
    }


////////////////////设备分布地图
	/**
	* 设备分布地图
	* 请使用 http://echarts.baidu.com/echarts2/extension/BMap/doc/example.html
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
    public function map(){
		$this->display();
    }

	/**
	* 全国今日各种检测项目总量汇报
	* 请使用 http://echarts.baidu.com/echarts2/doc/example/bar4.html
	* 操作表：pf_examstate_history
	* @param 
	* @author ripen_wang@163.com
	* @data 2016/3/23
	*/
	public function itemsbar(){
		$this->display();
	}

	/**
	* 年度开机总量和检测总量汇报
	* 请使用 http://echarts.baidu.com/echarts2/doc/example/bar1.html
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	public function opendateline(){
	
		$this->display();
	}


	/**
	* 
	* 
	* @param itemType:检测项；
	* @author ripen_wang@163.com
	* @data 
	*/
	private function getCountByItemType($itemType){
		 return M("examstate_history")->WHERE('FROM_UNIXTIME(`'.$itemType.'btime`,"%Y")=DATE_FORMAT(NOW(),"%Y")  AND `'.$itemType.'`=0')->count();
	}

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	private function getcitycounts($cityid){
		return M("member_store")->where('`city`='.$cityid)->count();
	}

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	private function getgeoCoord($cityname){
		$cityname = str_replace("市","",$cityname);
		$list	= M("geocoord")->where('`city`="'.$cityname.'"')->getField('longitude,latitude');
		foreach($list AS $key => $val){
			$newlist['x']	= $val;
			$newlist['y']	= $key;
		}
		return $newlist;
	}
}