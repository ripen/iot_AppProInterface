<?php
namespace Mobileastronautic\Controller;
use Think\Controller;
use Common\MyClass\Hicsdk;
use Mobileastronautic\Common\Sms;
use Mobileastronautic\Model\Health_data_analyseModel;
use Mobileastronautic\Model\Form_nfc_bloodsugarModel;
use Mobileastronautic\Model\Form_consultModel;
class IndexController extends Controller {
	//不需要登陆的action
	private  $nologin = array('register','login','checkcode','code','checkmobile');
	// cookie $key;
	private $cookie_key = 'yicheng_api';
	
 	public function __construct(){
		parent::__construct();
		$this->memberdb	= M('member');
		//判定用户是否登陆
		$this->userstatus();
	}

	/**
	 * 用户登陆状态判定
	 *@param token 用户登陆状态
	 * 
	 */
	private function userstatus(){
		if(!in_array(ACTION_NAME,$this->nologin)){
			  $cookie = $this->getcookie();
			  if(empty($cookie['userid'])){
			  	$this->redirect('login');
			  }
			  /* if($cookie['userid'] != $this->get_token()){
			  	 $this->login();
			  }  */
		}   
		
	}
	
	
	
	/**  
	 *  基于html5的app,用cookie来判定用户的登陆状态 
	 *  获取用户cookie
	 * @param $name cookie名称
	 */
	 public  function getcookie($name='yicheng_api'){
	 	$cookie =  cookie($name);
	 	$user = array();
	 	list($userid,$username) = explode("\t",authcode($cookie, 'DECODE',$this->cookie_key));
	 	$user['userid'] = $userid;
	 	$user['username'] = $username;
	 	return $user;
	 } 
	
	 /**  
	  *设置用户cookie 
	  * @param $name cookie名称
	  * @param $value cookie值、
	  * @param $time cookie时间
	  */
	private function setcookie($name='',$value='',$time= '7*86400'){
		cookie($name,$value,$time);
	} 
	 
	 
	/**
	 * 获取用户token值 
	 * 
	 * 
	 */
	/* private function get_token(){
		return S('nfc_user_token');	
	}  */
	/**
	 * 用户登陆后生成token
	 * @param $userid 用户id
	 * @param $time  有效时间 
	 * 
	 */
	/* private function set_token($userid = '',$time = '7*86400'){
		 if(!S('nfc_user_token')){ 
			S('nfc_user_token',$userid,$time);
		} 
	} */
	/**
	* http://local.api.yicheng120.com/Mobileastronautic/Index/index
	* @param authid:合作方用户ID
	* @param sn:设备码
	* @param attrs:检测状态
	* @param bloodsugar:血糖值
	* @param cardid:卡号ID
	* @author ripen_wang@163.com
	* @data 
	*/
	public function index(){
		//跳转到
		$this->redirect('healthrecord');
		//已下暂时不用
		$infoArr	= I();

		$hissdk	= new Hicsdk();
		// 指定服务地址
		$hissdk->api_url=$_SERVER['SERVER_NAME'];

		//访问帐号
		$username="user1";
		//访问口令
		$encrypt="7MxCxF";
		//$encrypt="12346";
		// 验证身份, Token值
		$infos	= $hissdk->checkAuth($username,$encrypt);
		$infos	= json_decode($infos,1);//将Json值转化为PHP 数组格式
		//获取验证成功，获取到Token值
		if($infos['result'] =='resultOK'){
			$values	= 9;								//测试的血糖值[必填项]
			$attrs	= 4;								//测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
			$authid	= array('authid'=>$infos['authid']);//访问者身份ID[必填项]

			//其他自带信息[选填项]，放在这个数组里，例如用户id、身份信息。
			$dates	= array('userid'=>12,'sexy'=>'男'); 

			$others	= array_merge($authid,$dates);
			echo $infos['resultmsg'];
			echo "Token为: ".$infos['token']." <br/>";

			echo "获取JSON值: <br />";
			echo $getData	= $hissdk->getData($infos['token'],$values,$attrs,$others);

			echo " <br />输出数组值:";
			echo '<pre>';
			print_r(json_decode($getData,1));

		}else{
			echo $infos['resultmsg'];
			return false;
		}

	}
	
	
	/**
    *生成手机验证码 
    * 
    */
   private  function mobilecode(){
   		$chars='0123456789abcdefjhijklmnopqrstuvwxyz';
		$len = 6;
		$code = "";
		while (strlen($code)<$len){
			$code.=substr($chars,(mt_rand()%strlen($chars)),1);
		}
		return $code;
   }
	
	/**
	 * 发送短信验证码
	 *@param $mobile 手机号  
	 * 
	 */
	private function sendsms($mobile='',$code=''){
		$sms = new Sms();
	    $sms->send($mobile,$code);
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
	 *用户已登陆,登陆,注册页,页面跳转
	 *
	 */
	private function userlogged(){
		$cookie = $this->getcookie();
		if(!empty($cookie['userid']) /* && ($cookie['userid']==$this->get_token()) */){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 用户登陆
	 * @param mobile 手机号
	 * @param password 密码
	 */
	public function login(){
		//用户已登陆直接跳转
		 if($this->userlogged()){
			$this->redirect('healthrecord');
		} 		 
		 if(IS_POST){ 		 	
			$condition['mobile'] = I('mobile');
			$password = I('password');
			//判定手机号和密码
			if(!checkphone($condition['mobile'])){
				$data['status'] = 0;
				$data['msg'] = '请输入正确的手机号';
				echo $this->ajaxReturn($data);
			}
			$encrypt = $this->memberdb->where('mobile="'.$condition['mobile'].'"')->getField('encrypt');
			if(!$encrypt){
				$data['status'] = 2;
				$data['msg'] = '手机号不存在';
				echo $this->ajaxReturn($data);
			}   
			//测试登陆;
			//$condition['mobile'] ='13800000002';
			//$password = '123456';
			//$encrypt = '7vawrt'; 
			$condition['password'] = $this->userpassword($password,$encrypt);
			$member = $this->memberdb->where($condition)->find();
			
			if($member){
				//加入到cookie
				$uid = $member['userid'];
				$username = $member['username'];
				$this->setcookie('yicheng_api',authcode("$uid\t$username", 'ENCODE',$this->cookie_key));
				//设置缓存
				//$this->set_token($uid);
				$data['status'] = 1;
				$data['msg'] = '登陆成功';
				echo $this->ajaxReturn($data);
			}else{
				$data['status'] = 3;
				$data['msg'] = '用户不存在';
				echo $this->ajaxReturn($data);
			}
		   } 
		$this->display();  
	}

	
	/**
	 * 用户注册最终入库
	 * @param mobile 手机号
	 * @param username 用户名
	 * @param password 密码
	 */
	public function register(){
		//用户已登陆直接跳转
		 if($this->userlogged()){
			$this->redirect('healthrecord');
		}	 
		if(IS_POST){  
			$userinfo = I();			
			//测试账户
			//$userinfo['mobile']='13100000000';
			
			//注册第一步
				if($userinfo['step']==1){
					if(!checkphone($userinfo['mobile'])){
						//不是正确手机号
						$data['status'] = 0;
						$data['msg'] = '请输入正确的手机号';
						echo $this->ajaxReturn($data);
					}
					
					$this->assign('mobile',$userinfo['mobile']);
					$this->display('register-2');
					//注册第二部
				}else if($userinfo['step']==2){		  
					$userinfo = I();
					$userinfo['username'] = $username = I('username','','htmlspecialchars');
					//测试
					//$userinfo['username'] = $username = 'test2';
					//$userinfo['mobile'] = '13800000002';
					//$userinfo['password'] = '123456';
					$encrypt = $this->mobilecode();
					$password = $this->userpassword($userinfo['password'],$encrypt);
					//插入到sso_members表
					$sso_members     = array(
							'username'=>$userinfo['username'],
							'password'=>$password,
							'random'=>$encrypt,
							'regdate'=>time(),
							'regip' =>get_client_ip()
					);
					$phpssouid = M('sso_members')->add($sso_members);				
					$user     = array(
						'username'=>$userinfo['username'],
						'password'=>$password,
						'encrypt'=>$encrypt,
						'phpssouid'=>$phpssouid,
						'regdate'=>time(),
						'regip' =>get_client_ip(),
						'mobile'=>$userinfo['mobile'],
						//用户所属组
						'groupid'=>'8',
					); 
					if($uid = $this->memberdb->add($user)){
						//用户细节插入member_detail表
						$userdetail = array(
							'userid'=>$uid,
						);
						M("member_detail")->add($userdetail);
						//加入到cookie
						$this->setcookie('yicheng_api',authcode("$uid\t$username", 'ENCODE',$this->cookie_key));
						//设置缓存
						//$this->set_token($uid);
						
						$this->redirect('healthrecord');
						//$data['status'] = 1;
						//$data['msg'] = '注册成功';
						//echo $this->ajaxReturn($data);
					}else{
						$data['status'] = 4;
						$data['msg'] = '注册失败';
						echo $this->ajaxReturn($data);
				 	} 
			  }
		 }else{
			 $this->display();
		 }
	}
	/**
	 *检查手机号 
	 *   
	 */
	public function checkmobile(){
		$userinfo = I();
		if(!checkphone(trim($userinfo['mobile']))){
			//不是正确手机号
			$data['status'] = 0;
			$data['msg'] = '请输入正确的手机号';
			echo $this->ajaxReturn($data);
		}
		if($userinfo['mobile']){
			$condition['mobile'] = $userinfo['mobile'];
			if($this->memberdb->where($condition)->find()){
				//手机号存在
				$data['status'] = 2;
				$data['msg'] = '手机号已存在';
				echo $this->ajaxReturn($data);
			}else{
				$data['status'] = 1;
				$data['msg'] = '手机号可以注册';
				echo $this->ajaxReturn($data);
			}
		}
	}
	/**
	 * 获取手机验证码 
	 * 
	 * 
	 */
	public function code(){
		$userinfo = I();
		if(!checkphone(trim($userinfo['mobile']))){
			//不是正确手机号
			$data['status'] = 0;
			$data['msg'] = '请输入正确的手机号';
			echo $this->ajaxReturn($data);
		}	
		//发送验证码
		$code = $this->mobilecode();
		//验证码延时无法使用
		//if($this->sendsms($userinfo['mobile'],$code)){
		if($code){
			$mobiles = array(
					'code'=>$code,
					'mobile'=>$userinfo['mobile'],
					'ip'=>get_client_ip(),
					'datetime'=>time()
			);
			//整理数据入库
			M('form_sms_code')->add($mobiles);
			$this->sendsms($userinfo['mobile'],$code);
			$data['status'] = 1;
			$data['msg'] = '手机验证码已发送';
			echo $this->ajaxReturn($data);
		}else{
			$data['status'] = 3;
			$data['msg'] = '重新发送手机验证码';
			echo $this->ajaxReturn($data);
		}	
				
		}  

	/**
	 *判定手机验证码 是否存在或过期
	 *
	 *
	 */
	public function checkcode(){
		$code = I('code','','htmlspecialchars');
		$mobile = I('mobile','','htmlspecialchars');
		//十五分钟只能有效
		$dtime = time()-900;
		
		$code = M("form_sms_code")->where("code='{$code}'AND mobile='{$mobile}' AND datetime>='{$dtime}'")->getField('code');
		if(!$code){
			$data['status'] = 3;
			$data['msg'] = '手机验证码不存在或已过期';
			echo $this->ajaxReturn($data);
		}else{
			$data['status'] =1;
			$data['msg'] = '手机验证码正确';
			echo $this->ajaxReturn($data);
		}
	}
	/**
	 * 血糖数据获取,插入数据库
	 *   
	 */
	public function getbloodsugar(){
		$info = I();
		
		$userid = $this->get_token();
		$data = array(
			'sn'=>$info['sn'],
			'bloodsugar'=>$info['bloodsugar'],
			'datetime'=>time(),
			'userid'=>$userid	
		);
		if(Form_nfc_bloodsugarModel::add($data,Form_nfc_bloodsugarModel::tablename())){
			//采集成功
			$data['status'] = 1;
			$data['msg'] = "数据采集成功";
			echo $this->Ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$data['msg'] = "数据采集失败";
			echo $this->Ajaxreturn($data);
		}
	}
	
	/**
	 *健康档案分类列表 
	 *   
	 *   
	 */
	public function healthrecord(){
		$linkage = D('Linkage');
		$data = $linkage->getlinkage();
		$this->assign('data',$data);
		$this->display('index');
	}
	/**  
	 *检测血糖列表 
	 * 
	 * 
	 */
	public function getbloodsugarlist(){
		$page = I('page',1,'intval');
		$cookie = $this->getcookie();
		$data =Form_nfc_bloodsugarModel::getlist($cookie['userid'],$page);
		$this->assign('bloodsugar',$data);
		$this->display('data');
	}
	/** 
	 * 血糖详情
	 * 
	 * 
	 */
	public function bloodsugarinfo(){
		$dataid = I('id',0,'intval');
		if(empty($dataid)){
			$this->error('参数错误');
		}
		$data = Form_nfc_bloodsugarModel::getinfo($dataid);
		$info = array();
		if($data){
			$info = Health_data_analyseModel::getinfo($data['bloodsugar']);			
		}
		$data['conclusion'] = $info ? $info['conclusion'] :'';
		$this->assign('detail',$data);
		$this->display('data-detail');
	}
	/**
	 * 发布咨询消息 或回复
	 *   
	 */
	public function  publish(){
		  if(IS_POST){ 
			$content['content'] = I('content','','htmlspecialchars');
			if(empty($content['content'])){
				$msgdata['status'] = 0;
				$msgdata['msg'] = '请输入咨询内容';
				echo $this->Ajaxreturn($msgdata);
			} 
			//$content['content'] = 'abcdef';
			$cookie = $this->getcookie();
			$content['userid'] = $cookie['userid'];
			$content['pid'] = I('pid',0,'intval');
			$content['bid'] = I('bid',0,'intval');
			$content['datetime'] = time();			
			if(Form_consultModel::add($content,Form_consultModel::tablename())){
				$msgdata['status'] = 1;
				$msgdata['msg'] = '数据添加成功';
				echo $this->Ajaxreturn($msgdata);
			}else{
				$msgdata['status'] = 2;
				$msgdata['msg'] = '数据添加失败';
				echo $this->Ajaxreturn($msgdata);
			}
		  }else{
		  	$id = I('id',0,'intval');
		  	$this->assign('id',$id);
		  	$this->display('ask-form');
		  }  
		
	}
	/**
	 * 问诊记录列表
	 *   
	 */
	public function inquirylist(){
		$page = I('page','1','intval');
		$list = array();
		$cookie = $this->getcookie();
		$userid = $cookie['userid'];		
		$list = Form_consultModel::getconsultlist($userid,0,$page);
		$this->assign('list',$list);
		$this->display('inquiry_record');	
	}
	/**
	 * 咨询列表
	 *   
	 */
	public function publishlist(){
		$page = I('page','1','intval');
		$bid = I('bid',0,'intval');
		$cookie = $this->getcookie();
		$userid = $cookie['userid'];
		$data = Form_consultModel::getlistinfo($userid,$bid,$page);
		//获取最后一条信息dataid
		if($data){
			for($i=0;$i<count($data);$i++){
				if($i== count($data)-1){
					$pid = $data[$i]['dataid'];
					$pid = 0 ?1:0;
				}
			}
		}else{
			$pid = 0;
		}
		$this->assign('list',$data);
		$this->assign('bid',$bid);
		$this->assign('pid',$pid);
		$this->display('inquiry-detail');
	}
	
	/**
	 *
	 *用户信息
	 *
	 */
	public function userinfo(){
		//获取用户id
		$userinfo = D('Member');
		$cookie = $this->getcookie();		
		$data = $userinfo->userinfo($cookie['userid']);
		$this->assign('user',$data);
		$this->display('my_index');
	}
	
	/**  
	 * 
	 *用户信息修改
	 * 
	 */
	public function edituserinfo(){
		//获取用户id
		$member = new \Mobileastronautic\Model\MemberModel();
		$cookie = $this->getcookie();
		if(IS_POST){
			$userinfo = I();
			$data = array(
				'username'=>$userinfo['username'],
				'sex'=>$userinfo['sex'],
				'height'=>$userinfo['height'],
				'weight'=>$userinfo['weight'],
				'birthday'=>$userinfo['birthday'],
				'edu'=>$userinfo['edu'],
				'disease'=>$userinfo['disease']
			);
			if($member->edit($data,$cookie['userid'])){
				$msgdata['status'] =1;
				$msgdata['msg'] = "用户信息修改成功";
				echo $this->Ajaxreturn($msgdata);
			}else{
				$msgdata['status'] =0;
				$msgdata['msg'] = "用户信息修改失败";
				echo $this->Ajaxreturn($msgdata);
			}			
		}
		$data = $member->userinfo($cookie['userid']);
		
		$this->assign('user',$data);
		$this->display('edit');
	}
	/**
	 *用户退出,清除cookie,及缓存
	 *   
	 */
	public function logout(){
		//清除cookie;
		cookie('yicheng_api',null);
		//清除缓存
		//S('nfc_user_token',null);
		$data['status'] = 1;
		$data['msg'] = '退出成功';
	}
	
	
}