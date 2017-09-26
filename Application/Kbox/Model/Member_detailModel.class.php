<?php
namespace Kbox\Model;
use Think\Model;
/**
 * @author Administrator
 *  尿11项Model
 */
class Member_detailModel extends BaseModel {

	//页数
	private static  $pagesize = 10;
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'Member_detail';
	}
	
	/**
	 * 获取性别
	 * @param number $userid
	 * @return string|\Think\mixed
	 */
	public function getsex($userid=0){
		
		if(!$userid){
			return '';
		}
		return M(self::tablename())->where('userid='.$userid.'')->find();
	}
}