<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 设备管理
 * 
 * @author wangyangyang
 * @version V1.0
 */
class EquipmentModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 设备列表数据
	 * @param  array   $drugs    药店ID
	 * @param  integer $p        当前页
	 * @param  integer $pagesize 每页显示条数
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function lists( $drugs = array(),$p = 1 , $pagesize = 10 ){
		if ( !$drugs || !is_array($drugs) ) {
			return false;
		}

		$curpage=	( $p - 1 ) * $pagesize;

		$map 	=	array();
		$map['userid']	=	array('in',implode(',',$drugs));

		$db 	=	M('equipment');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,gateUUID,wifi,wifipw')->where($map)->limit($curpage.','.$pagesize)->order('id desc')->select();
		

		$data 	=	array();
		$data['p']			=	$p;
		$data['pagesize']	=	$pagesize;
		$data['total']		=	$total;
		$data['info']		=	$info;

		return $data;
	}

	/**
	 * 获取设备数据详情
	 * 
	 * @param  integer $id ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getinfo( $id = 0 ){
		if ( !$id ) {
			return false;
		}

		$info 	=	M('equipment')->field('id,userid,gateUUID,wifi,wifipw')->where(array('id'=>$id))->find();
		return $info ? $info : false;
	}


	/**
	 * 更新设备wifi信息
	 * 
	 * @param  integer $id      ID
	 * @param  string  $wifi    wifi名称
	 * @param  string  $wifipw  wifi密码
	 * @return 
	 */
	public function updates( $id = 0 , $wifi = '',$wifipw = '' ){
		if ( !$id || !$wifi || !$wifipw ) {
			return false;
		}

		$where 	=	array();
		$where['id']	=	$id;

		$data 	=	array();
		$data['wifi']	=	$wifi;
		$data['wifipw']	=	$wifipw;

		$info 	=	M('equipment')->where( $where )->save($data);
		return $info ? true : false;
	}
}