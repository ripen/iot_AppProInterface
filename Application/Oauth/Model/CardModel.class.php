<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 卡号相关操作模块
 * 
 * @author wangyangyang
 * @version V1.0
 */
class CardModel extends Model {  

	Protected $autoCheckFields = false;


	/**
	 * 获取卡号信息
	 * @param  string $card 卡号
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getcard( $card = '' ){
		if( !$card ){
			return false;
		}

		$info 	=	M('drug_card_user')->where( array('wholecard' => $card))->find();

		if ( !$info ) {
			return false;
		}

		return $info;
	}
	
	
	/**
	 * 绑定卡号到具体用户
	 * @param  integer $id     卡号ID
	 * @param  integer $userid 用户ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function updates( $id = 0 , $userid = 0 ){
		if (!$id || !$userid ) {
			return false;
		}

		$data 	=	array();
		$data['status']	=	1;
		$data['acttime']=	time();
		$data['userid']	=	$userid;
		$info 	=	M('drug_card_user')->where( array('id'=>$id))->save($data);

		return $info ? true : false;
	}

	/**
	 * 通过id查询卡号基本信息
	 * @param  array  $id 卡号id
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getcardbyidarr( $id = array() ){
		if (!$id || !is_array($id) ) {
			return false;
		}

		$db 	=	M('drug_card_user');
		$map 	=	array();
		$map['id']	=	array('in',implode(',',$id));
		// 查询总数
		$info	=	$db->where($map)->field('id,wholecard')->select();

		return $info ? handleArrayKey($info,'id') : false;
	}

	/**
	 * 通过id查询卡号基本信息
	 * @param  integer  $id 卡号id
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getcardbyid( $id = 0 ){
		if (!$id || !is_numeric($id) ) {
			return false;
		}

		$db 	=	M('drug_card_user');
		$map 	=	array();
		$map['id']	=	$id;
		// 查询总数
		$info	=	$db->where($map)->field('id,wholecard')->find();

		return $info ? $info : false;
	}




}