<?php
namespace Common\Common;
/**
 * @author tangchengqi
 * 2016.1.8
 * redis;
 */
class phpredis extends \Think\Cache\Driver\Redis {
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 生成redis key 名称  如 report::$userid%10::$userid;
	 * @param string $keyname 
	 * @param number $userid
	 */
	public function createkeyname($prvkeyname='report',$userid=0){
		if(!$userid){
			return '';
		}
		$key			=	'';
		$middlekeyname	=	$this->createmod($userid);
		$key			=	$prvkeyname.':'.$middlekeyname.':'.$userid;
		return $key;
	}
	
	
	/**
	 * 用用户id取余 % 10
	 * @param number $userid
	 * @param number $number 
	 */
	private function createmod($userid=0,$number=10){
		return $userid % $number;		
	}
	
	/**
	 * 把数据先序列化,在生成json存储
	 *  @param unknown $name redis 键
	 * @param unknown $value redis 值
	 */
	public function formatdataset($name,$value){
	 	$value	=	(is_object($value) || is_array($value)) ? serialize($value) : $value;
	 	return $this->set($name,$value);
	}
	
	/**
	 * 数据反序列化
	 * @param unknown $name redis 键
	 */
	public function formatdataget($name){
		if($name = $this->get($name)){
			return unserialize($name);
		}
	}
	
}