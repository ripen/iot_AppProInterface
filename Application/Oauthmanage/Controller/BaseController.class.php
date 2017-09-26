<?php
/**
 * 控制器基类
 */
namespace Oauthmanage\Controller;
use Think\Controller;
class BaseController extends Controller {
	
	public $cookie_userid;
	public $cookie_username;

	public function __construct()
	{
		parent::__construct();
		#检测登录状态
		#
		$this->cookie_userid 	=	cookie('_userid');
		$this->cookie_username	=	cookie('_username');

		if ( !$this->cookie_userid ) {
			redirect('/Oauthmanage/Public/login');
        	exit;
		}

		$this->assign('username',$this->cookie_username);
	}

}