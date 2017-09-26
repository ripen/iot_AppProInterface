<?php
namespace Video\Controller;
use Think\Controller;
class IndexController extends Controller {
	private $returnOK		= 'resultOK';
	private $returnError	= 'resultError';
	private $resultOKmsg	= '获取Token成功!';
	private $resultErrormsg	= '获取Token失败!请检查服务地址、访问帐号和访问口令是否正确!';
	private $tokenlifetime	= '3000';	//TOKEN生命值 ，单位秒；

	private	$serverAddr		= "v.yicheng120.com";
	private	$serverPort		= "8906";
	private	$serverAds		= "http://api.yicheng120.com/Public/Video/images/bg.jpg";


	private $apptype	= '';
	
 	public function __construct(){
		parent::__construct();
		$this->memberdb	= M('member');
		$roomnum	= I('roomnum') ? intval(I('roomnum')) : mt_rand(100000, 999999);
	}
/**
* 
* 
* @param 
* @author ripen_wang@163.com
* @data 2015/8/26
* @return JSON
*/
	public function index(){
		$where	= I();
		$userid	= $this->memberdb->where($where)->getField('userid');
		if (!empty($userid)) {
			$this->set_token();

			echo json_encode(array(	'result'=>$this->returnOK,
									'resultmsg'=>$this->resultOKmsg,
									'token'=>$this->get_token(),
									'authid'=>$userid,
							));
		}else{
			echo json_encode(array(	'result'=>$this->returnError,
									'resultmsg'=>$this->resultErrormsg
							));
		}
    }

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2015/8/26
	*/
	public function getVideo(){
		$infoArr	= I();

		if ($infoArr['token'] === $this->get_token()) {
			$this->assign("web_site",C('WEB_SITE'));
			$this->assign("username",$infoArr['username']);
			$this->assign("token",$infoArr['token']);
			$this->assign("roomnum",mt_rand(1000,9999).$infoArr['roomnum']);
			$html	= $this->fetch();
			echo json_encode(array('status'=>'1','html'=>$html));
		}else{
			echo json_encode(array('status'=>'0'));
		}
	}


	private function set_token() {
		$token	= md5(microtime(true));
		S('token',$token,$this->tokenlifetime);
	}

	private function get_token() {
		return S('token');
	}

}