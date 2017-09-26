<?php
namespace Common\Common;
/**
 * @author tangchengqi
 * 2016.1.13
 * 健康数据使用;
 */
class healthredis extends phpredis {
	//设备检测项目简写：血糖：gl 血氧：ox 体温：tm 体成分：we 血压：bp 血脂：bf 尿11项：ur 心电：el 血胴：bk 血尿酸：re 尿微量白蛋白：um
	private 	$health	   =	'health';
	private 	$arr	   =	array('gl','ox','tm','we','bp','bf','ur','el','bk','re','um');//网络医院
	protected  $array	   =	array('1'=>'gl','2'=>'bp','3'=>'ox','4'=>'bf','5'=>'we','6'=>'ur','7'=>'tm','8'=>'el','9'=>'bk','10'=>'re','11'=>'um');
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 生成reids key值
	 * @param number $userid
	 * @return string
	 */
	public function getkey($userid=0){
		if(!$userid){
			return '';
		}
	 return  $this->createkeyname($this->health,$userid);
	}
	
	/**
	 * 生成健康数据redis
	 * @param unknown $data
	 * @param number $userid
	 * @param number $type 检测类型
	 * @param number $time 检测时间
	 */
	public function addhealth($type='gl',$data=array(),$userid=0,$time=0){
		if(!$data){
			return '';
		}
		if($type=='1'){
			$type	=	'gl';
		}
		$keyname			=	$this->getkey($userid);
		$health				=	array();
		$health				=	$this->result($data,$type);
		//键值已存在
		if($tmpdata=$this->formatdataget($keyname)){
			$tmpdata[$type]		=	$health;
		}else{
			$tmpdata			=	array();
			$tmpdata[$type]		=	$health;
		}
		$tmpdata[$type]['extime']	=	$time;
		$this->formatdataset($keyname,$tmpdata);
	}
	
	
	/**
	 * 结果分析成健康数据要的格式
	 * @param unknown $data
	 * @param unknown $type 检测类型
	 * @return string|Ambigous <multitype:, unknown>
	 */
	public function result($data=array(),$type='gl'){
		if(!$data){
			return '';
		}

		// if($type=='el'){
		// 	//心电
		// 	return $data;
		// }
		$arr	=	array();
		foreach($data as $k=>$v){
			if(is_array($v)){
				$arr[$k]['tests']	=	$v['tests'];
				$arr[$k]['msg']		=	$v['msg'];
				$arr[$k]['0']		=	$v['0'] ? $v['0'] : '';
				$arr[$k]['status']	=	$v['status'] ? $v['status'] : 0;
				$arr[$k]['type']	=	$v['type'] ? $v['type'] : 0;
				if ($k == 'tm' ) {
					$arr[$k][$k]		=	$v['tmv'];	
				}elseif( $k == 'el' ){
					$arr[$k][$k]		=	'';	
					$arr[$k]['image']	=	$v['image'] ? $v['image'] : '';	
				}else{
					$arr[$k][$k]		=	$v[$k];
				}
			}
		}
		return $arr;
	}
}