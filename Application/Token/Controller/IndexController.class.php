<?php
namespace Token\Controller;
use Think\Controller;

/**
 * API统一获取TOKEN接口
 * 
 * 
 * @author wangyangyang
 * @version V1.0
 */
class IndexController extends Controller {
	
	// 成功状态统一标识
	private $returnOK		= 	'200';
	// 失败状态统一标识
	private $returnError	= 	'-1';

	// 无权限标识
	private $noprev 		=	'-99';
	
	// token 有效期
	private $tokenlifetime	= 	'7200';	//TOKEN生命值 ，单位秒；
	
	// param 缺少参数
	private $noparam 		=	'-98';

 	public function __construct(){
		parent::__construct();
	}


	/**
	 * 获取接口可访问权限
	 * 	提交方式 POST
	 * 
	 * @param string $appid APP_ID
	 * @param string $appsecret APP_SECERT
	 * @return object 返回可访问的标识
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function index( ){
		$appid 		=	I('post.appid','','htmlspecialchars,trim');
		$appsecret 	=	I('post.appsecret','','htmlspecialchars,trim');

		$result 	=	array();

		if ( !$appid || !$appsecret ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}
		
		// 查询用户
		$map 	=	array();
		$map['username']	=	$appid;
		$map['encrypt']		=	$appsecret;
		$map['groupid']		=	array('in','2,5,6');
		$info 	=	M('member')->where( $map )->field('userid,islock')->limit(1)->find();
		if ( !$info || $info['islock'] == 1 ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 设置token
		$token 	=	$this->set_token($info['userid']);
		if ( !$token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		$result['status']	=	$this->returnOK;
		$result['token']	=	$token['token'];
		$result['experise']	=	$this->tokenlifetime;
		$this->ajaxReturn($result);
		exit;
	}


	/**
	 * 设置token
	 * 
	 * @param integer $userid 用户ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	private function set_token( $userid ) {
		if ( !$userid || !is_numeric($userid) ) {
			return false;
		}

		$token	= 	md5(microtime(true));

		$info 	=	array( 'token' => $token , 'userid' => $userid );
		S('APITOKEN',$info,$this->tokenlifetime);
		return $info;
	}

}