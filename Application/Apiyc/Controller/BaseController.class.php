<?php
namespace Apiyc\Controller;

use Think\Controller;


class BaseController extends Controller {

	private  $nologin = array('index');

	public $access_token;

	public $apiuserid;

	public function __construct(){
		parent::__construct ();
		$this->userstatus();
	}
	
	
	/**
	 * 用户状态判定
	 *
	 */
	public function userstatus(){
		if( !in_array( strtolower(CONTROLLER_NAME), $this->nologin ) ){
			$token['access_token'] 	=	I('access_token','','htmlspecialchars');
			if(empty($token['access_token'])){
				$data ['status'] 	= 	'-99';
				$data ['msg'] 		= 	"无权访问";
				$this->ajaxReturn ( $data, 'JSON' );
				exit;
			}

			$info = $this->get_token($token['access_token']);

			if($token['access_token'] != $info['access_token'] ){
				$data ['status'] 	= '-99';
				$data ['msg'] 		= "无权访问";
				$this->ajaxReturn ( $data, 'JSON' );
			}
			
			$this->access_token	=	$info['access_token'];
			$this->apiuserid	=	$info['userid'];
			
		}
	
	}
	/**
	 *获取登陆后的access_token
	 */
	public function get_token( $token='' ){
		return S($token);
	}
	
	/**
	 * 生成token;
	 *   */
	public function create_token(){
		return 	md5(uniqid(time()));
	}
	
	/**
	 * 用户登陆后生成token
	 * 超过 1 天后,重新登陆
	 *
	 */
	public function set_token( $token='' ,$data = array() ){
		if (!$data) {
			return false;
		}
		S($token,$data,86400);
		return $data;
	}
	
	
	/**
	 * 错误提交方式统一处理
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return json
	 */
	public function errorpost(){
		$data ['status'] 	= '-98';
		$data ['msg'] 		= "获取方式有误";
		$this->ajaxReturn ( $data, 'JSON' );
	}


	/**
	 * token有误
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return json
	 */
	public function errortoken(){
		$data ['status'] 	= '-97';
		$data ['msg'] 		= "无权访问";
		$this->ajaxReturn ( $data, 'JSON' );
	}

}