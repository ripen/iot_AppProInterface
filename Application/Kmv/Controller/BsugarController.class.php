<?php
namespace Kmv\Controller;

use Think\Controller;
use Common\MyClass\Hicsdk;
/**
 * 5d-8b4 app 接口
 * @author tangchengqi
 *
 */
class BsugarController extends Controller {
	private  $nologin = array('register','login','checkcode','code','checkmobile','findpwd','regcode','checkregcode','findcode');
	public function __construct(){
		parent::__construct ();
		$this->userstatus();
	}
	
	
	/**
	 * 用户状态判定
	 *
	 */
	public function userstatus(){
		if(!in_array(ACTION_NAME,$this->nologin)){
			$token['access_token'] = I('access_token','','htmlspecialchars');
			if(empty($token['access_token'])){
				//$this->redirect('Index/login');
				$data ['received'] = 0;
				$data ['status'] = 0;
				$data ['msg'] = "请登陆";
				echo $this->ajaxReturn ( $data, 'JSON' );
			}
			$t = $this->get_token($token['access_token']);
			if($token['access_token'] != $t['access_token'] ){
				//$this->redirect('Index/login');
				$data ['received'] = 0;
				$data ['status'] = 0;
				$data ['msg'] = "请登陆";
				echo $this->ajaxReturn ( $data, 'JSON' );
			}
		}
	
	}
	/**
	 *获取登陆后的access_token
	 */
	public function get_token($token=''){
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
	 * 超过7天后,重新登陆
	 *
	 */
	public function set_token($token='',$userid=0){
		if(!S($token)){
			S($token,array('access_token'=>$token,'userid'=>$userid),86400*7);
		}
	}
	
	/**
	 * 短信验证码
	 *  */
	public function sendsms($mobile='',$code=''){
		$sms = new \Common\MyClass\Sms();
		$result = $sms->send($mobile,$code);
		$arr = json_decode($result,true);
		if($arr['res_code']==0){
			return 1;
		}else{
			return 0;
		}
	}
	
	
}