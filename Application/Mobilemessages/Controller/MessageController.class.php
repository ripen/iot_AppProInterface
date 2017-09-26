<?php
namespace Mobilemessages\Controller;

use Think\Controller;
use Common\MyClass\Sms;
class MessageController extends Controller {
	
 	public function __construct(){
		parent::__construct();
		$this->api_clients	= M('api_clients');
	}

	/**
	* 唐管家发送短信API接口
	* 
	* @param client_id:系统分配的 client_id
	* @param client_secret:系统分配的 client_secret
	* @data 
	*/
	public function sendmessage(){
		
		$client_id 		=	I('post.client_id','','trim,htmlspecialchars,strip_tags');
		$client_secret 	=	I('post.client_secret','','trim,htmlspecialchars,strip_tags');

		$mobile	=	I('post.mobile','','trim,htmlspecialchars,strip_tags');
		$val	=	I('post.val','0','trim,htmlspecialchars,strip_tags');
		$url	=	I('post.url','','trim,htmlspecialchars,strip_tags');

		$result	=	array();
		$result['status']	=	0;
		if ( !$client_id || !$client_secret || !$mobile || !$url ) {
			$result['status']	=	'-1';
			exit(json_encode($result));
		}

		// 判断手机号
		if ( !checkphone($mobile) ) {
			$result['status']	=	'-2';
			exit(json_encode($result));
		}

		// 验证身份
		$where 	=	array();
		$where['client_id']		=	$client_id;
		$where['client_secret']	=	$client_secret;

		$check 	=	$this->api_clients->where($where)->find();

		// 无权限
		if ( !$check || $check['status'] != 99) {
			$result['status']	=	'-3';
			exit(json_encode($result));
		}
		$sms	= new Sms();
		
		$url 	=	htmlspecialchars_decode($url);
		// 发送短信
		if( $sms->send_tgj( $mobile , $val , $url  ) ){
			
			// 日志记录
			$mobiles	= array(
				'message' => $val.'----'.$url,
				'mobile'  => $mobile,
				'ip'      => get_client_ip(),
				'datetime'=>time(),
				'client_id'=>$client_id
			);

			//整理数据入库
			M('form_api_mobile_message')->add($mobiles);
			
			// 发送成功
			$result['status']	=	'1';
			exit(json_encode($result));
		}else{
			// 发送失败
			$result['status']	=	'-4';
			exit(json_encode($result));
		}

	}

}