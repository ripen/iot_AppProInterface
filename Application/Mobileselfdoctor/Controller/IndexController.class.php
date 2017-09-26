<?php
namespace Mobileselfdoctor\Controller;

use Think\Controller;
use Common\MyClass\Hicsdk;
class IndexController extends Controller {
	private $datas;
	//所属接口用户组,接口vip组
	//private $groupid ="IN (5,6)";
	//血糖仪用户组id
	private $groupid = 9;
 	public function __construct(){
		parent::__construct();
		$this->memberdb	= M('member');
	}
	/**
	 * 用户状态判定
	 * 
	 */
	public function userstatus(){
		$token['access_token'] = I('access_token','','htmlspecialchars');
		if(empty($token['access_token'])){
			$this->loginshow();
		}
		if( $token['access_token'] != $this->get_token()){
			$this->login();
		}
		
	}
	/**
	 *获取登陆后的access_token
	 */
	 private function get_token(){
		return S('bluebooth_user_token'); 
	 }
	 
	 /**
	 * 用户登陆后生成token
	 * 超过7天后,重新登陆 
	 * 
	 */
	private function set_token(){
		if(!S('bluebooth_user_token')){
			$token = md5(uniqid(time()));
			S('bluebooth_user_token',$token,86400*7);
		}
	}
	 
	/**
	* http://local.api.yicheng120.com/Mobileselfdoctor/index/index/authid/14535555/sn/23456789dfghj/attrs/4/bloodsugar/12/userecode/15010905653
	* @param userecode:被检测的用户信息
	* @param authid:合作方用户在登录APP时候的ID 可以通过给定的账号在app登录的时候获取到
	* @param sn:设备码
	* @param attrs:测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
	* @param bloodsugar:血糖值
	* @param cardid:卡号ID
	* @author ripen_wang@163.com
	* @data 2015/8/31
	*/
	public function index(){
		$Infoarr = array();
		//会员卡号
		$Infoarr['userecode']	=  I('cardnum','','htmlspecialchars')  ;
		//设备号
		$sn	        			= I('deviceid','','htmlspecialchars') ;
		//转成32字符串
		$Infoarr['sn']	        = str_replace('-','',$sn);
		//血糖值
		$Infoarr['bloodsugar']	=  I('examdata','','htmlspecialchars')  ;
		//检测血糖状态
		$attrs	   			    =  I('examtime','1','intval') ;
		$Infoarr['attrs']	=	max(1,$attrs);
		$Infoarr['authid']      = I('authid') ? I('authid'):1;

		if ($Infoarr['userecode']=='' || $Infoarr['bloodsugar']=='') {
				$data['received'] = 0;
				$data['status']   =0;
				$data['msg']      = "上传失败";
				echo $this->ajaxReturn ($data,'JSON');
		}
		
		$Collecterinfos	= $this->getUserinfos($Infoarr['userecode']);
		if(!$Collecterinfos){
			$data['received'] = 0;
			$data['status']   =2;
			$data['msg']      = "卡号不正确";
			echo $this->ajaxReturn ($data,'JSON');
		}	
		$Insertdatas	= Array(
								'userid'		=> $Collecterinfos['uid'],
								'username'		=> '被采集者ID为'.$Collecterinfos['uid'],
								'datetime'		=> Time(),
								'ip'			=> get_client_ip(),
								'bloodsugar'	=> $Infoarr['bloodsugar'],
								'userecode'		=> $Infoarr['userecode'],
								'attr'			=> $Infoarr['attrs'],
								'otherinfos'	=> Serialize($Collecterinfos),
								'sn'			=> $Infoarr['sn'],
								'appauthid'		=> $Infoarr['authid'],
								'collectuserid'	=> $Collecterinfos['uid'],
							);
		
			$insertid	= M("form_api_collect_bgdata")->add($Insertdatas);
			if ($insertid) {
					$analyseDatas	= $this->getDatas($Infoarr['bloodsugar'],$Infoarr['attrs'],$Collecterinfos);
					//检测结果状态
					//转成数组
					$Datas   = json_decode($analyseDatas,true);
					$data['received'] = 1;
					$data['status']   = 1;
					$data['msg']      = "上传成功";
					//给远程服务器返回数据
					if($Insertdatas['attrs']==4){
						//餐后是2,远程服务器要的
						$Insertdatas['attrs']	=	2;
					}
					$remotedata = array(
							'apptype'=>"bgdata",
							'bloodsugar'=>$Insertdatas['bloodsugar'],
							'sn'       =>$Insertdatas['sn'],
							'dates'    =>date('Y-m-d H:i:s',$Insertdatas['datetime']),
							'attrs'    =>$Insertdatas['attrs'],
							'analysedata'=>array(
									'conclusion'=>$Datas['analysedata']['conclusion'],
									'reason'=>$Datas['analysedata']['reason'],
									'suggest'=>$Datas['analysedata']['suggest'],
								),
							'otherinfos'=>$Collecterinfos,
					);
					$this->remotedata($remotedata);
					//给远程服务器返回数据
					echo $this->ajaxReturn ($data,'JSON');
			}else{
				$data['received'] = 0;
				$data['status']   =0;
				$data['msg']      = "上传失败";
				echo $this->ajaxReturn ($data,'JSON');
			}
			/* echo "<PRE>";
			print_r($analyseDatas);
			exit();		 */
		//接下来，会让对方给定一个接口，然后把获取到的信息直接插入到对方的数据里
	}
	/**
	 * 返回给远程服务器的数据
	 * @param unknown $data   */
	public function remotedata($data = array()){
		//远程服务器的url
		if($data){
			$url = "http://www.renjk.com/api/device/receive_yc_bloodsugar_data";
			$result = Spost($url,array('data'=>json_encode($data)));
			
			$result = json_decode($result,true);
			if($result['status']==0){
				$msgdata['msg'] = $result['description'];
				//echo $this->ajaxReturn ($msgdata,'JSON');
			}else{
				$msgdata['msg'] = $result['description'];
				//echo $this->ajaxReturn ($msgdata,'JSON');
			}
		}else{
			$msgdata['msg'] = '没传数据';
			$msgdata['status'] = 10;
			echo $this->ajaxReturn ($msgdata,'JSON');
		}
	}
	
	/**
	 * 检测血糖成功后,跳转的页面
	 *   */
	public function getresult(){
		//血糖值
		$Infoarr['bloodsugar']	  =  I('examdata','','htmlspecialchars')  ;
		//检测血糖状态
		$Infoarr['attrs']	    =  I('examtime','1','intval') ;
		$info = \Mobileselfdoctor\Model\Health_data_analyseModel::getinfo($Infoarr['bloodsugar'],$Infoarr['attrs']);
		$data = $info ? $info :array();
		$this->assign('bloodsugar',$Infoarr['bloodsugar']);
		$this->assign('data',$data);
		$this->display('info');
	}
	
	/**
	*用户登陆
	*@param $username 用户名
	*@param $password 密码
	*
	*/

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	public function loginshow(){
	$this->display();
	}
	
	public function login(){
		$condition['username'] = $username = I('username','','htmlspecialchars');
		// 测试账户
		//$condition['username'] = $username = 'testapi';
		$password = I('password','','htmlspecialchars');
		//$password = '123456';
		//获取用户encrypt
		$encrypt = $this->memberdb->where('username="'.$username.'" AND groupid="'.$this->groupid.'" ')->getField('encrypt');
		
		if(!$encrypt){
			$data['status'] = 2;
			$data['msg'] = '用户名不存在';
			echo $this->ajaxReturn ($data);
		}else{
			$condition['password'] = $this->userpassword($password,$encrypt);
		}
		if($user = $this->memberdb->where($condition)->find()){
			$this->set_token();
			$data['access_token'] = S('bluebooth_user_token');
			$data['userid'] = $user['userid'];
			$data['username'] = $user['username'];
			$data['nickname'] = $user['nickname'];
			$data['email'] = $user['email'];
			$data['status'] = 1;
			$data['msg'] = '用户登陆成功';
			echo $this->ajaxReturn ($data); 
		}else{
			$data['status'] = 0;
			$data['msg'] = '用户名或密码错误,请重新登陆';
			echo $this->ajaxReturn ($data);
		}
	}
	
	
	
	
	/**
	* 获取分析结果
	* 
	* @param values 测试的血糖值[必填项]
	* @param attrs	测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
	* @param dateArray	其他信息 array()
	* @author ripen_wang@163.com
	* @data 
	*/
	private function getDatas($values,$attrs,$dateArray=array()){
		//接口访问帐号
		$username="user1";
		//接口访问口令
		$encrypt="7MxCxF";

		$hissdk	= new Hicsdk();
		// 指定服务地址
		$hissdk->api_url=$_SERVER['SERVER_NAME'];
		//$encrypt="12346";
		// 验证身份, Token值
		$infos	= $hissdk->checkAuth($username,$encrypt);
		$infos	= json_decode($infos,1);//将Json值转化为PHP 数组格式
		//获取验证成功，获取到Token值
		if($infos['result'] =='resultOK'){
			$authid	= array('authid'=>$infos['authid']);//接口访问者身份ID[必填项]
			$others	= array_merge($authid,$dateArray);
			return $hissdk->getData($infos['token'],$values,$attrs,$others);
			//echo $hissdk->getData($infos['token'],$values,$attrs,$others);
		}
	}

/**
		用户密码加密规则
	*/
	public function userpassword($password='',$encrypt=''){
		if(empty($password) && empty($encrypt)){
			return "";
		}
		return md5(md5($password).$encrypt);
	}

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	private function getUserinfos($usercode){
		$uid	= '15010905653';
		$pwd	= '000000';
		$user	= Spost('http://www.renjk.com/api/mem/login',array('uid'=>$uid,'pwd'=>$pwd));
		$infos	=  Spost('http://www.renjk.com/api/mem/get_tmmem_info',array('uid'=>$usercode,'mtoken'=>$user['mtoken']));
		$infoArr	= json_decode($infos,1);
		if ($infoArr['status']==0) {
			return $infoArr['data'];
		}
	}
}