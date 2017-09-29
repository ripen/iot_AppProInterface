<?php
namespace Kbox\Model;
use Think\Model;
/**
 * @author Administrator
 *  血糖Model
 */
class Member_storeModel extends BaseModel {

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
		return 'member_store';
	}
	
	/**
	 * 获取1条信息,企业编码中的省份码,企业码
	 * @param number $userid 用户id
	 * @return string|\Think\mixed
	 */
	public function getone($userid=0){
		if(!$userid){
			return '';
		}
		$data	=	array();
		$where	=	'1=1';
		$where .=	' AND userid='.$userid.'';
		$data	=	 M(self::tablename())->where($where)->find();
		return $data ? $data['provincecode'].$data['enterprise'] :'';
	}
}	