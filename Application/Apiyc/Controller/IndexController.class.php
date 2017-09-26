<?php
namespace Apiyc\Controller;
use Think\Controller;

class IndexController extends BaseController {
	
 	public function __construct(){
		parent::__construct();
	}
	

	/**
	 * 获取token
	 * 	访问接口，先获取到token之后，才能有访问权限
	 * 
	 * @return array
	 */
	public function login(){
		// 访问密钥key
		$client_key		=	I('post.client_key','','htmlspecialchars,trim');
		// 请求密钥
		$client_secret	=	I('post.client_secret','','htmlspecialchars,trim');

		$data 			=	array();
		$data['status']	=	'200';

		// 密钥信息不能为空
		if ( !$client_key || !$client_secret ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 判断访问的密钥是否可以正常使用
		$where 	=	array();
		$where['username']	=	$client_key;
		$where['encrypt']	=	$client_secret;
		$info 	=	M('member')->where($where)->field('userid,islock')->find();

		// 密钥信息有误
		if ( !$info ) {
			$data['status']	=	'-2';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		// 无权进行访问
		if ( $info['islock'] ) {
			$data['status']	=	'-3';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		$token 	=	$this->create_token();
		$reuslt =	array('access_token'=>$token,'expires'=>86400,'userid'=>$info['userid']);
		$this->set_token($token,$reuslt);

		$data['status']	=	'200';
		$data['access_token']	=	$reuslt['access_token'];
		$data['expires']		=	$reuslt['expires'];
		$this->ajaxReturn($data,'JSON');
		exit;
	}
}