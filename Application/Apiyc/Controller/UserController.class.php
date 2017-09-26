<?php
namespace Apiyc\Controller;
use Think\Controller;

class UserController extends BaseController {
	
 	public function __construct(){
		parent::__construct();

	}
	

	/**
	 * 用户注册
	 * 	注册需要的基本信息：（必填信息）
	 * 		1.userid
	 * 		2.性别（1：男 2：女 ）获取到之后需重新处理成符合网站的数据（网站 
	 * 			0：男 1：女）
	 * 		3.生日（年月日格式）
	 * 		4.身高（CM）
	 * 		5.密码（为了能登录怡成网络医院）
	 *
	 * 		V1.0 版本，注册为使用手机号
	 * 		V1.1 版本，注册使用第三方的用户id
	 *
	 * @version V1.1
	 * @return array
	 */
	public function register(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;
		}

		$userid 	=	I('post.userid','','htmlspecialchars,trim');
		$sex		=	I('post.sex','','intval');
		$birthday	=	I('post.birthday','','htmlspecialchars,trim');
		$height		=	I('post.height','','htmlspecialchars,trim');
		$password	=	I('post.password','','htmlspecialchars,trim');

		$data 		=	array();
		$data['status']	=	'200';
		
		// 校验用户id是否正确
		if ( !$userid || !is_numeric($userid) ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}
		// 校验性别
		if ( !$sex ) {
			$data['status']	=	'-2';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}

		if ($sex == 1 ) {
			$sex =	0;
		}elseif ($sex == 2 ) {
			$sex =	1;
		}

		// 校验生日格式是否正确
		if ( !$birthday || !checkDateIsValid($birthday) ) {
			$data['status']	=	'-3';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}
		
		// 校验身高
		if ( !$height || !is_numeric($height) ) {
			$data['status']	=	'-4';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}
		
		// 校验密码
		if ( !$password ) {
			$data['status']	=	'-5';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}

		// 判断用户id是否已经注册
		$where 	=	array();
		$where['tuserid']	=	$userid;
		$where['apiuserid']	=	$this->apiuserid;
		$check 	=	M('user_api_from')->where($where)->find();
		

		// 如果不存在，进行注册处理
		if ( !$check ) {
			$userinfo 	=	array();
			$userinfo['username']	=	random(8).$userid;
			$userinfo['password']	=	$password;
			$userinfo['birthday']	=	$birthday;
			$userinfo['sex']		=	$sex;
			$userinfo['height']		=	$height;
			$memberuserid 	=	\Apiyc\Model\Member_Model::add($userinfo);
		}else{
			$memberuserid 	=	$check['userid'];
		}

		// 注册失败
		if ( !$memberuserid ) {
			$data['status']	=	'-6';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 绑定注册会员信息到历史记录表中
		$apiuserid	=	$this->apiuserid;
		$where 	=	array();
		$where['apiuserid']	=	$apiuserid;
		$where['userid']	=	$memberuserid;
		$where['tuserid']	=	$userid;
		$check 	=	M('user_api_from')->where($where)->find();
		if ( !$check ) {
			$apiinfo	=	array();
			$apiinfo['apiuserid']	=	$apiuserid;
			$apiinfo['userid']		=	$memberuserid;
			$apiinfo['tuserid']		=	$userid;
			$apiinfo['addtime']		=	time();
			M('user_api_from')->add($apiinfo);
		}
		
		
		$data['status']	=	'200';
		$data['userid']		=	(int)$memberuserid;
		$data['apiuserid']	=	(int)$userid;
		$this->ajaxReturn($data,'JSON');
		exit;
	}


	/**
	 * 判断第三方用户ID是否已经注册
	 *
	 * 
	 * @return [type] [description]
	 */
	public function checkuserid(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;
		}

		$userid 	=	I('post.userid','','intval');
		
		$data 		=	array();
		$data['status']	=	'200';
		
		// 校验用户id是否正确
		if ( !$userid || !is_numeric($userid) ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 判断用户id是否已经注册
		$where 	=	array();
		$where['tuserid']	=	$userid;
		$where['apiuserid']	=	$this->apiuserid;
		$check 	=	M('user_api_from')->where($where)->find();
		
		if ( !$check ) {
			$data['status']	=	'1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		$data['userid']		=	(int)$check['userid'];
		$data['apiuserid']	=	(int)$check['tuserid'];
		$this->ajaxReturn($data,'JSON');
		exit;
	}


	/**
	 * 编辑用户基本信息
	 * 	编辑用户基本信息：（必填信息）
	 * 		1.性别（1：男 2：女 ）获取到之后需重新处理成符合网站的数据（网站 
	 * 			0：男 1：女）
	 * 		2.生日（年月日格式）
	 * 		3.身高（CM）
	 * 		4.密码
	 *
	 * @version V1.1
	 * @return array
	 */
	public function edit(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;
		}

		$userid 	=	I('post.userid','','htmlspecialchars,trim');
		$sex		=	I('post.sex','','intval');
		$birthday	=	I('post.birthday','','htmlspecialchars,trim');
		$height		=	I('post.height','','htmlspecialchars,trim');
		$password	=	I('post.password','','htmlspecialchars,trim');

		$data 		=	array();
		$data['status']	=	'200';
		
		// 校验 应用方用户id 格式是否正确
		if ( !$userid || !is_numeric($userid) ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		$info 	=	array();
		// 校验生日格式是否正确
		if ( $sex && !is_numeric($sex) ) {
			$data['status']	=	'-2';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}

		if ($sex == 1 ) {
			$info['sex'] =	0;
		}elseif ($sex == 2 ) {
			$info['sex'] =	1;
		}

		// 校验生日格式是否正确
		if ( $birthday && !checkDateIsValid($birthday) ) {
			$data['status']	=	'-3';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}elseif ( $birthday ) {
			$info['birthday']	=	$birthday;
		}
		
		// 校验身高
		if ( $height && !is_numeric($height) ) {
			$data['status']	=	'-4';
			$this->ajaxReturn($data,'JSON');
			exit;	
		}elseif ( $height ) {
			$info['height']	=	$height;
		}
		

		// 判断 应用方用户id 是否已经注册
		$where 	=	array();
		$where['tuserid']	=	$userid;
		$where['apiuserid']	=	$this->apiuserid;
		$check 	=	M('user_api_from')->where($where)->find();
		
		if ( !$check ) {
			$data['status']	=	'-5';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		$mup 	=	'';
		$dup	=	'';
		// 更新用户密码
		if ( $password ) {
			$encrypt 	= random ( 6 );
			$pass 		= password ( $password, $encrypt );

			$updateinfo	=	array();
			$updateinfo['encrypt']	=	$encrypt;
			$updateinfo['pwssword']	=	$pass;
			$mup	=	M('member')->where(array('userid'=>$check['userid']))->save($updateinfo);
		}

		// 更新用户基本信息
		if ( $info ) {
			$dup	=	M('member_detail')->where(array('userid'=>$check['userid']))->save($info);
		}
		
		if ( $mup || $dup ) {
			$data['status']	=	'200';
			$this->ajaxReturn($data,'JSON');
		}
		
		$data['status']	=	'-6';
		$this->ajaxReturn($data,'JSON');
		exit;
	}


	/**
	 * 判断手机号是否已经注册
	 *
	 * 
	 * @return [type] [description]
	 */
	private function checkmobile(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;
		}

		$mobile 	=	I('post.mobile','','htmlspecialchars,trim');
		
		$data 		=	array();
		$data['status']	=	'200';
		
		// 校验手机号是否正确
		if ( !$mobile || !checkphone($mobile) ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 判断手机号是否已经注册
		$where 	=	array();
		$where['mobile|username']	=	$mobile;
		$check 	=	M('member')->where($where)->field('userid')->find();
		
		if ( !$check ) {
			$data['status']	=	'-2';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		$data['userid']	=	(string)$check['userid'];
		$this->ajaxReturn($data,'JSON');
		exit;
	}





}