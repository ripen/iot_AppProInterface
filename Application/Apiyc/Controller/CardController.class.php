<?php
namespace Apiyc\Controller;
use Think\Controller;

/**
 * 绑卡
 *
 * @author      wangyangyang
 * @version     V1.0
 */
class CardController extends BaseController {
	
 	public function __construct(){
		parent::__construct();

	}
	

	/**
	 *	绑定卡操作
	 * 		1.0 绑卡时候传递的为网站用户ID
	 * 		1.1 绑卡时候传递的为第三方网站用户ID
	 *
	 * 
	 * @author wangyangyang
	 * @version V1.1
	 */
	public function add(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;
		}

		$data 	=	array();
		$data['status']	=	'200';

		// 卡号判断
		$card 		=	I('post.card','','htmlspecialchars,trim');
		$cardinfo	=	$this->checkstatus($card);


		// 用户id
		$userid	=	I('post.userid','','intval');
		if ( !$userid ) {
			$data['status']	=	'-4';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 判断第三方用户是否存在
		$where 	=	array();
		$where['tuserid']	=	$userid;
		$where['apiuserid']	=	$this->apiuserid;
		$check	=	M('user_api_from')->where($where)->find();
		if ( !$check ) {
			$data['status']	=	'-5';
			$this->ajaxReturn($data,'JSON');
			exit;
		}
		
		// 判断第三方用户绑定的本站用户是否存在
		$where 	=	array();
		$where['userid']	=	$check['userid'];
		$userinfo	=	M('member')->where($where)->field('userid,groupid')->find();

		if ( !$userinfo ) {
			$data['status']	=	'-5';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		if ($userinfo['groupid'] != 7 ) {
			$data['status']	=	'-5';
			$this->ajaxReturn($data,'JSON');
			exit;
		}
		
		
		// 卡类型
		$cardtypes 	=	I('post.types','','intval');
		if ( !$cardtypes ) {
			$data['status']	=	'-6';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 判断传递过来的卡类型是否正确
		$check 	=	M('card_category')->where(array('id'=>$cardtypes))->find();
		if ( !$check ) {
			$data['status']	=	'-7';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 绑卡流程处理
		$info 	=	array();
		$info['userid']	=	$userinfo['userid'];
		$info['status']	=	1;
		$info['acttime']=	time();

		// 绑定卡的时候，卡类型如何处理？
		$info['cardtype']	=	$cardtypes;

		$update	=	M('drug_card_user')->where(array('wholecard'=>$card))->save($info);

		if ( !$update ) {
			$data['status']	=	'-8';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 用户已经注册情况下，直接进行的绑卡操作时候，需要先记录一下用户与api之间的关系
		$apiuserid	=	$this->apiuserid;
		$where 		=	array();
		$where['apiuserid']	=	$apiuserid;
		$where['userid']	=	$userinfo['userid'];
		$where['drugid']	=	$cardinfo['drugid'];
		$where['tuserid']	=	$userid;
		$check 	=	M('user_api_from')->where($where)->find();
		if ( !$check ) {
			$apiinfo	=	array();
			$apiinfo['apiuserid']	=	$apiuserid;
			$apiinfo['userid']		=	$userinfo['userid'];
			$apiinfo['addtime']		=	time();
			$apiinfo['drugid']		=	$cardinfo['drugid'];
			$apiinfo['tuserid']		=	$userid;
			M('user_api_from')->add($apiinfo);
		}


		$this->ajaxReturn($data,'JSON');
		exit;
	}

	/**
	 * 获取卡类型信息
	 *
	 * 
	 * @return [type] [description]
	 */
	public function types(){
		$data	=	array();
		$data['status']	=	'200';

		$info	=	M('card_category')->field('id,name,note')->select();
		if ( !$info ) {
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		$data['info']	=	$info;
		$this->ajaxReturn($data,'JSON');
		exit;
	}


	/**
	 *	检测卡是否能够使用
	 * 	
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function checkcard(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;
		}

		$data 	=	array();
		$data['status']	=	'200';

		$card 	=	I('post.card','','htmlspecialchars,trim');
		
		$this->checkstatus($card);

		$this->ajaxReturn($data,'JSON');
		exit;
	}


	/**
	 * 公用方法判断卡号状态信息
	 * 
	 * @param  string $card [description]
	 * @return [type]       [description]
	 */
	private function checkstatus( $card = '' ){
		if ( !$card ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 判断卡片状态
		$where 	=	array();
		$where['wholecard']	=	$card;
		$info	=	M('drug_card_user')->where($where)->find();
		if ( !$info ) {
			$data['status']	=	'-2';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 如果卡片为白卡、已被绑定、异常、禁用，则不能进行绑定
		if ( $info['status'] != 0 ) {
			$data['status']	=	'-3';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		return $info;
	}
}