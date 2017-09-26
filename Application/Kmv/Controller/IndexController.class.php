<?php

namespace Kmv\Controller;

use Think\Controller;
use Common\MyClass\Hicsdk;

/**
 * 康宝app接口
 *
 * @author tangchengqi
 *        
 */
class IndexController extends BsugarController {
	private $datas;
	// 所属接口用户组,接口vip组
	// private $groupid ="IN (5,6)";
	// 血糖仪用户组id
	private $groupid = 7;
	public function __construct() {
		parent::__construct ();
		$this->memberdb = M ( 'member' );
	}
	
	/**
	 * http://local.api.yicheng120.com/Mobileselfdoctor/index/index/authid/14535555/sn/23456789dfghj/attrs/4/bloodsugar/12/userecode/15010905653
	 *
	 * @param
	 *        	userecode:被检测的用户信息
	 * @param
	 *        	authid:合作方用户在登录APP时候的ID 可以通过给定的账号在app登录的时候获取到
	 * @param
	 *        	sn:设备码
	 * @param
	 *        	attrs:测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
	 * @param
	 *        	bloodsugar:血糖值
	 * @param
	 *        	cardid:卡号ID
	 * @author ripen_wang@163.com
	 *         @data 2015/8/31
	 */
	public function index() {
		$Infoarr = array ();
		// 会员卡号
		$Infoarr ['cardid'] = I ( 'cardnum', '', 'htmlspecialchars' );
		// 设备号
		$Infoarr ['sn'] = I ( 'deviceid', '', 'htmlspecialchars' );
		// 血糖值
		$Infoarr ['bloodsugar'] = I ( 'examdata', '', 'htmlspecialchars' );
		// 检测血糖状态
		$Infoarr ['attrs'] = I ( 'examtime', '1', 'intval' );
		//
		$Infoarr ['access_token'] = I ( 'access_token', '', 'htmlspecialchars' );
		if ($Infoarr ['cardid'] == '' || $Infoarr ['bloodsugar'] == '') {
			$data ['received'] = 0;
			$data ['status'] = 0;
			$data ['msg'] = "上传失败";
			echo $this->ajaxReturn ( $data, 'JSON' );
		}
		$token = $this->get_token($Infoarr['access_token']);
		$userid = $token['userid'];
		//卡状态
		if(!is_numeric($Infoarr ['cardid']) || strlen($Infoarr ['cardid'])<5){
			$data ['received'] = 0;
			$data ['status'] = 3;
			$data ['msg'] = "卡号长度不能小于5或格式不为数字";
			echo $this->ajaxReturn ( $data, 'JSON' );
		}
		$cardstatus = '';
		$cardstatus = \Kmv\Model\Drug_card_userModel::getcheckcard ($userid,$Infoarr ['cardid']);
		
		if(empty($cardstatus)){
			$data ['received'] = 0;
			$data ['status'] = 4;
			$data ['msg'] = "卡号不存在或不是您的卡号";
			echo $this->ajaxReturn ( $data, 'JSON' );
		}elseif ($cardstatus['status'] !=1) {
			$data ['received'] = 0;
			$data ['status'] = 2;
			$data ['msg'] = "卡号未激活或异常错误";
			echo $this->ajaxReturn ( $data, 'JSON' );
		}
		$Insertdatas = Array (
				'userid' => $userid,
				'createtime' => Time (),
				'bloodsugar' => $Infoarr ['bloodsugar'],
				'cardid' => $Infoarr ['userecode'],
				'attr' => $Infoarr ['attrs'],
				'sn' => $Infoarr ['sn'],
		);
		
		$insertid = M ( "kangbao_bbsugar" )->add ( $Insertdatas );
		if ($insertid) {
			// 检测结果状态
			//$analyseDatas = $this->getDatas ( $Infoarr ['bloodsugar'], $Infoarr ['attrs'], $Collecterinfos );
			// 转成数组
			//$analyseDatas = json_decode ( $analyseDatas, true );
			$data ['received'] = 1;
			$data ['status'] = 1;
			$data ['msg'] = "上传成功";
			echo $this->ajaxReturn ( $data, 'JSON' );
		} else {
			$data ['received'] = 0;
			$data ['status'] = 0;
			$data ['msg'] = "上传失败";
			echo $this->ajaxReturn ( $data, 'JSON' );
		}
		/*
		 * echo "<PRE>"; print_r($analyseDatas); exit();
		 */
		// 接下来，会让对方给定一个接口，然后把获取到的信息直接插入到对方的数据里
	}
	
	
	/**
	 * 用户登陆
	 *
	 * @param $username 用户名        	
	 * @param $password 密码        	
	 *
	 */
	public function login() {
		if (IS_POST) {
			$condition ['username'] = $username = I ( 'username', '', 'htmlspecialchars' );
			// 测试账户
			$condition ['username'] ;
			$password = I ( 'password', '', 'htmlspecialchars' );
			// 获取用户encrypt
			$encrypt = $this->memberdb->where ( 'username="' . $username . '" AND groupid="' . $this->groupid . '" ' )->getField ( 'encrypt' );
			
			if (! $encrypt) {
				$data ['status'] = 2;
				$data ['msg'] = '用户名不存在';
				echo $this->ajaxReturn ( $data );
			} else {
				$condition ['password'] = $this->userpassword ( $password, $encrypt );
			}
			if ($user = $this->memberdb->where ( $condition )->find ()) {
				$token = $this->create_token ();
				$this->set_token ( $token, $user ['userid'] );
				$data ['access_token'] = $token;
				$data ['userid'] = $user ['userid'];
				$data ['username'] = $user ['username'];
				$data ['nickname'] = $user ['nickname'];
				$data ['email'] = $user ['email'];
				$data ['status'] = 1;
				$data ['msg'] = '用户登陆成功';
				echo $this->ajaxReturn ( $data );
			} else {
				$data ['status'] = 0;
				$data ['msg'] = '用户名或密码错误,请重新登陆';
				echo $this->ajaxReturn ( $data );
			}
		}else{
			$data ['status'] = 0;
			$data ['msg'] = '用户名或密码错误,请重新登陆';
			echo $this->ajaxReturn ( $data );
		}
	}
	
	/**
	 * 注册
	 *   
	 *   */
	
	public function register(){
		if(IS_POST){
			$userinfo = I();
				
			//$userinfo['mobile'] ='13800000005';
			//$userinfo['password'] = '123456';
			if (!isset($userinfo['mobile']) || !trim($userinfo['mobile']) ||
					!checkphone( trim($userinfo['mobile'])) ) {
		
						$data['status'] = 0;
						$data['msg']	= '手机号格式错误';
						echo $this->ajaxReturn($data);
						exit;
					}
					if (!isset($userinfo['password']) || !trim($userinfo['password']) ) {
						$data['status'] = 2;
						$data['msg']	= '请输入密码';
						echo $this->ajaxReturn($data);
						exit;
					}
					if($userinfo['password']!=$userinfo['password1']){
						$data['status'] = 3;
						$data['msg']	= '两次密码不一致';
						echo $this->ajaxReturn($data);
						exit;
					}
					if (!isset($userinfo['code']) || !trim($userinfo['code']) ) {
						$data['status'] = 4;
						$data['msg']	= '手机激活码不为空';
						echo $this->ajaxReturn($data);
						exit;
					}
					/* if (!isset($userinfo['regcode']) || !trim($userinfo['regcode']) ) {
						$data['status'] = 5;
						$data['msg']	= '验证码不为空';
						echo $this->ajaxReturn($data);
						exit;
					} */
					//	判断输入的手机号与获取验证码时候手机号是否一致
					/* $sessmobile	=	session('registermobile');
					
					if (!$sessmobile || $sessmobile != $userinfo['mobile'] ) {
						$data['status'] = 6;
						$data['msg']	= '请输入获取激活码的手机号';
						echo $this->ajaxReturn($data);
						exit;
					} */
					if(\Kmv\Model\MemberModel::checkmobile($userinfo['mobile'])){
						$data['status'] = 7;
						$data['msg']	= '手机号已存在';
						echo $this->ajaxReturn($data);
						exit;
					}
					//	判断获手机取到的验证码是否可用
					//$registercode	=	session('registercode');
					$time = time()-600;
					$registercode = M('form_sms_code')
									->where('mobile='.$userinfo['mobile'].' AND datetime>='.$time.'')
									->field('code')
									->order('datetime desc')
									->limit(1)
									->find();
					if (!$registercode || $registercode['code'] != $userinfo['code'] ) {
						$data['status'] = 0;
						$data['msg']	= '激活码有误或已过期';
						echo $this->ajaxReturn($data);
						exit;
					}
						
					$encrypt	=	random(6);
					$password	=	password($userinfo['password'],$encrypt);
					//	注册流程，先往sso表添加数据
					$sso_members     = array(
							'username'=>$userinfo['mobile'],
							'password'=>$password,
							'random'=>$encrypt,
							'regdate'=>time(),
							'regip' =>get_client_ip()
					);
					$phpssouid = M('sso_members')->add($sso_members);
						
					$user     = array(
							'username'=>$userinfo['mobile'],
							'password'=>$password,
							'encrypt'=>$encrypt,
							'phpssouid'=>$phpssouid,
							'regdate'=>time(),
							'regip' =>get_client_ip(),
							'mobile'=>$userinfo['mobile'],
							'groupid'=>'7',
					);
					$uid = M('member')->add($user);
		
					if( $uid ){
						//用户细节插入member_detail表
						$userdetail = array(
								'userid'=>$uid,
						);
						M("member_detail")->add($userdetail);
						$token = $this->create_token ();
						$this->set_token ( $token, $user ['userid'] );
						$data ['access_token'] = $token;
						$data ['userid'] = $uid;
						$data ['username'] = $userinfo['mobile'];
						$data['status'] = 1;
						$data['msg']	= '注册成功';
						echo $this->ajaxReturn($data);
						exit;
						
					}else{
						$data['status'] = 0;
						$data['msg']	= '注册失败';
						echo $this->ajaxReturn($data);
						exit;
					}
		}else{
			$data['status'] = 0;
			$data['msg']	= '注册失败';
			echo $this->ajaxReturn($data);
			exit;
		}
		
		
		
	}
	
	
	/**
	 *检查手机号
	 *
	 */
	public function checkmobile(){
		if (IS_POST) {
			$userinfo = I ();
			if (! checkphone ( trim ( $userinfo ['mobile'] ) )) {
				// 不是正确手机号
				$data ['status'] = 0;
				$data ['msg'] = '请输入正确的手机号';
				echo $this->ajaxReturn ( $data );
			}
			$condition ['mobile'] = $userinfo ['mobile'];
			$info = M('member')->where ( $condition )->find ();
			if ($info) {
				if(!empty($userinfo['password']) && password(trim($userinfo['password']),$info['encrypt'])!=$info['password']){
					//密码判定
					$data ['status'] = 3;
					$data ['msg'] = '密码错误';
					echo $this->ajaxReturn ( $data );
				}
				// 手机号存在
				$data ['status'] = 2;
				$data ['msg'] = '手机号已存在';
				echo $this->ajaxReturn ( $data );
			} else {
				$data ['status'] = 1;
				$data ['msg'] = '手机号可以注册';
				echo $this->ajaxReturn ( $data );
			}
				
		}
	
	}
	
	
	
	/**
	 * 获取网站验证码
	 */
	public function regcode(){
		parent::code();
	}
	/**
	 * 判定网站验证码是否正确
	 *   */
	public function checkregcode(){
		if (IS_POST || IS_AJAX) {
			$regcode = I ( 'code', '', "trim" );
			$code = new \Common\Common\code ();
				
			if ($code->check_verify ( $regcode )!=true) {
				$msgdata ['status'] = 7;
				$msgdata ['msg'] = '验证码错误';
				echo $this->ajaxReturn ( $msgdata );
				exit ();
			}else{
				$msgdata ['status'] = 6;
				$msgdata ['msg'] = '验证码正确';
				echo $this->ajaxReturn ( $msgdata );
				exit ();
			}
		}
	
	}
	
	/**
	 * 发送验证码
	 *
	 */
	public function code(){
		$userinfo	=	I();
		if(!checkphone( trim($userinfo['mobile'])) ){
			//不是正确手机号
			$data['status'] = 0;
			$data['msg'] = '请输入正确的手机号';
			echo $this->ajaxReturn($data);
			exit;
		}
		if(\Kmv\Model\MemberModel::checkmobile($userinfo['mobile'])){
			$data['status'] = 7;
			$data['msg']	= '手机号已存在';
			echo $this->ajaxReturn($data);
			exit;
		}
		//	判断手机短信发送时间
		$sendtimes	=	session('registertime');
	
		//	一分钟只能发送一次
		if ($sendtimes && time() - $sendtimes < 60 ) {
			//不是正确手机号
			$data['status'] = 0;
			$data['msg']	= '一分钟只能获取一次';
			echo $this->ajaxReturn($data);
			exit;
		}
	
		//发送验证码
		$code	=	random(6,'0123456789');
		if($this->sendsms($userinfo['mobile'],$code)){
			$mobiles	= array(
					'code'  => $code,
					'mobile'=> $userinfo['mobile'],
					'ip'	=> get_client_ip(),
					'datetime'=>time()
			);
			//整理数据入库
			M('form_sms_code')->add($mobiles);
			$data['status'] = 1;
			$data['msg']	= '手机验证码已发送';
	
			/* session('registertime',time());
				
			session('registermobile',$userinfo['mobile']);
	
			session('registercode',$code); */
				
			echo $this->ajaxReturn($data);
			exit;
		}else{
			$data['status'] = 3;
			$data['msg']	= '重新发送手机验证码';
			echo $this->ajaxReturn($data);
			exit;
		}
	}
	
	
	
	/**
	 * 找回密码
	 *	根据手机号获取验证码
	 *	找回的密码以手机短信形式发送
	 *
	 * @param
	 * @return
	 */
	public function findpwd(){
	
		if( IS_POST ){
			$userinfo = I();
			if (!isset($userinfo['mobile']) || !trim($userinfo['mobile']) ||
					!checkphone( trim($userinfo['mobile'])) ) {
	
						$this->error('手机号为空或格式不正确');
						exit;
					}
				 if (!isset($userinfo['code']) || !trim($userinfo['code']) ) {
				 	$this->error('手机激活码不为空');
				 	exit;
				 }
	
				 if (!isset($userinfo['password']) || !trim($userinfo['password']) ) {
				 	$this->error('密码不为空');
				 	exit;
				 }
				 if($userinfo['password']!=$userinfo['password1']){
				 	$this->error('两次输入的密码不一致');
				 	exit;
				 }
				 //	判断输入的手机号与获取验证码时候手机号是否一致
				 $findpwdmobile	=	session('findpwdmobile');
				 if (!$findpwdmobile || trim($findpwdmobile) != trim($userinfo['mobile']) ) {
				 	$this->error('请输入正确的手机号');
				 	exit;
				 }
				 //	判断获取到的验证码是否可用
				 $findpwdcode	=	session('findpwdcode');
				 if (!$findpwdcode || trim($findpwdcode) != trim($userinfo['code']) ) {
				 	$this->error('请输入正确的手机激活码');
				 	exit;
				 }
	
				 //	获取手机号注册的基本信息
				 $info = M('member')->where('mobile="'.$userinfo['mobile'].'"')
					->field('userid,phpssouid')->find();
				 if ( !$info ) {
				 	redirect('用户信息有误');
				 	exit;
				 }
	
				 $encrypt	=	random(6);
				 $password	=	password($userinfo['password'],$encrypt);
	
				 //	更新，sso表与member表同时更新
				 $upinfo     = array(
				 		'password'=>$password,
				 		'random'=>$encrypt
				 );
				 $ssup	=	M('sso_members')->where('uid='.$info['phpssouid'])->save($upinfo);
				 $member = array(
				 		'password'=>$password,
				 		'encrypt'=>$encrypt
				 );
				 $uup	=	M('member')->where('userid='.$info['userid'])->save($member);
	
				 if( $ssup !== false && $uup !== false ) {
				 	session('findpwdtime',null);
				 	session('findpwdmobile',null);
				 	session('findpwdcode',null);
				 	$this->error('重置密码成功','Home');
				 	/* $data['status'] = 4;
				 	 $data['msg']	= '重置密码成功';
				 	echo $this->ajaxReturn($data); */
				 }else{
				 	/* $data['status'] = 4;
				 	 $data['msg'] = '找回密码失败';
				 	echo $this->ajaxReturn($data); */
				 	$this->error('密码找回失败');
				 }
	
		}
	}
	
	
	/**
	 * 发送验证码(找回密码)
	 * @param
	 * @return
	 */
	public function findcode(){
		if(IS_POST && IS_AJAX){
			$userinfo	=	I();
			if(!checkphone( trim($userinfo['mobile'])) ){
				//不是正确手机号
				$data['status'] = 0;
				$data['msg'] = '请输入正确的手机号';
				echo $this->ajaxReturn($data);
				exit;
			}
				
			//	确认手机号是否存在
			$info = M('member')->where('mobile="'.$userinfo['mobile'].'"')->getField('userid');
				
			if ( !$info ) {
				$data['status'] = 0;
				$data['msg']	= '手机号不存在';
				echo $this->ajaxReturn($data);
				exit;
			}
	
	
			//	判断手机短信发送时间
			$findpwdtime	=	session('findpwdtime');
				
			//	一分钟只能发送一次
			if ($findpwdtime && time() - $findpwdtime < 60 ) {
				//不是正确手机号
				$data['status'] = 0;
				$data['msg']	= '一分钟只能获取一次';
				echo $this->ajaxReturn($data);
				exit;
			}
			session('findpwdtime','');
				
			session('findpwdmobile','');
				
			session('findpwdcode','');
			//发送验证码
			$code	=	random();
			//短信延迟可能导致错误
			if( $this->sendsms($userinfo['mobile'],$code) ){
				$mobiles	= array(
						'code'  => $code,
						'mobile'=> $userinfo['mobile'],
						'ip'	=> get_client_ip(),
						'datetime'=>time()
				);
				//整理数据入库
				M('form_sms_code')->add($mobiles);
				$data['status'] = 1;
				$data['msg']	= '手机验证码已发送';
	
				session('findpwdtime',time());
	
				session('findpwdmobile',$userinfo['mobile']);
	
				session('findpwdcode',$code);
	
				echo $this->ajaxReturn($data);
				exit;
			}else{
				$data['status'] = 3;
				$data['msg']	= '重新发送手机验证码';
				echo $this->ajaxReturn($data);
				exit;
			}
		}
	}
	
	/**
	 * 当前值班医生,按星期几计算
	 *   */
	public function doctor(){
		 $access_token = I('access_token','','htmlspecialchars');
		 $token = $this->get_token($access_token);
		 $userid = $token['userid'];
		if(!$userid){
			$this->ajaxReturn(array('status'=>'-3','msg'=>'请登陆'));
		} 
		$week = date('w',time());
		$data =\Kmv\Model\MemberModel::getdoctorinfo($week);
		$msgdata = array();
		$msgdata['doctor'] = $data;
		$msgdata['status'] = 1;
		$msgdata['msg']	= '医生信息';
		echo $this->ajaxReturn($msgdata);
		exit;
	}
	
	/**
	 * 用户预约
	 *   
	 *   */
	
	public function  appointment(){
		if(IS_POST){
			$access_token = I('access_token','','htmlspecialchars');
			$token = $this->get_token($access_token);
			$userid = $token['userid'];
			if(!$userid){
				$this->ajaxReturn(array('status'=>'-3','msg'=>'请登陆'));
			}
			// 医生ID处理为默认的当前值班医生
			$doctorid =   I('doctorid','0','intval');
			$doctor = \Kmv\Model\MemberModel::getdocotr($doctorid);
			if (!$doctor) {
				$this->ajaxReturn(array('status'=>'-1','msg'=>'预约医生不存在'));
			}
			$doctorid   =   $doctor['userid'];		

			// 判断当前用户是否已经在当天有加入过队列中
			$where	=	array('uid'=>$userid,'did'=>$doctorid);
			$time 	=	strtotime(date('Y-m-d 00:00:00'));
			$etime	=	strtotime(date('Y-m-d 23:59:59'));
			$where['create_time']	=	array(array('EGT',$time),array('ELT',$etime));
			$where['resourse']      =   3;
			$where['status']        =   array('lt',4);
			
			
			$check 	=	M('reservation')->where($where)->find();
			if ( $check ) {
				$this->ajaxReturn(array('status'=>'-2','msg'=>'您已在等待队列中','roomid'=>$check['roomid']));
			}
			
			$ip 	=	get_client_ip();
			if ( $ip == '127.0.0.1' ) {
				$area 	=	'北京';
			}else{
				// 根据IP获取省份
				$classip	= new \Org\Net\IpLocation('UTFWry.dat');  	// 实例化类 参数表示IP地址库文件
				$area 		= $classip->getlocation( $ip );
			}
			$roomid		=	random(6,'0123456789');
			 
			
			$data 			=	array();
			$data['did']	=	$doctorid;
			$data['uid']	=	$userid;
			$data['roomid']	=	$roomid;
			$data['ip']		=	$ip;
			$data['area']	=	$area ? $area : '-';
			$data['create_time']	=	time();
			$data['resourse'] =  3;

			$resultid		=	M('reservation')->add($data);
			if ( $resultid ) {
				$memberinfo		=	M('member')->where('userid = '.$userid)->field('userid,username,nickname')->find();
			
				$name	=	$memberinfo['nickname'] ? $memberinfo['nickname'] : $memberinfo['username'];
				$this->ajaxReturn(array('status'=>'1','msg'=>'加入成功','roomid'=>$roomid));
			}			
			$this->ajaxReturn(array('status'=>'0','msg'=>'加入失败'));
		}
		
		
	}
	
	
	/**
	 * 返回当前用户之前有多少人在等待中……
	 *   
	 *
	*/
	public function  getnums(){
		if(IS_POST){
			$access_token = I('access_token','','htmlspecialchars');
			$token = $this->get_token($access_token);
			$userid = $token['userid'];
			if(!$userid){
				$this->ajaxReturn(array('status'=>'-3','msg'=>'请登陆'));
			}
			
			// 判断当前用户是否已经在当天有加入过队列中
			$where	=	array('uid'=>$userid);
			$time 	=	strtotime(date('Y-m-d 00:00:00'));
			$etime	=	strtotime(date('Y-m-d 23:59:59'));
			$where['create_time']	=	array(array('EGT',$time),array('ELT',$etime));
			$check 	=	M('reservation')->where($where)->find();

			if ( $check ) {
				$etime	=	$check['create_time'];
			}else{
				$etime	=	strtotime(date('Y-m-d 23:59:59'));
			}

			$time 	=	strtotime(date('Y-m-d 00:00:00'));
			
			$where	=	array();
			$where['create_time']	=	array(array('EGT',$time),array('ELT',$etime));
			$where['status']        =   array('lt',4);
			$where['uid']			=	array('NEQ',$userid);

			$total 	=	M('reservation')->where($where)->count();
			
			$result =	array();
			$result['total']	=	$total ? $total : 0;

			$this->ajaxReturn(array('status'=>'1','total'=>$total));
		}
		
		
	}
	
	
	/**
	 * 获取分析结果
	 *
	 * @param
	 *        	values 测试的血糖值[必填项]
	 * @param
	 *        	attrs	测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
	 * @param
	 *        	dateArray	其他信息 array()
	 * @author ripen_wang@163.com
	 *         @data
	 */
	private function getDatas($values, $attrs, $dateArray = array()) {
		// 接口访问帐号
		$username = "user1";
		// 接口访问口令
		$encrypt = "7MxCxF";
		
		$hissdk = new Hicsdk ();
		// 指定服务地址
		$hissdk->api_url = $_SERVER ['SERVER_NAME'];
		
		// $encrypt="12346";
		// 验证身份, Token值
		$infos = $hissdk->checkAuth ( $username, $encrypt );
		$infos = json_decode ( $infos, 1 ); // 将Json值转化为PHP 数组格式
		                                    // 获取验证成功，获取到Token值
		if ($infos ['result'] == 'resultOK') {
			$authid = array (
					'authid' => $infos ['authid'] 
			); // 接口访问者身份ID[必填项]
			$others = array_merge ( $authid, $dateArray );
			return $hissdk->getData ( $infos ['token'], $values, $attrs, $others );
			// echo $hissdk->getData($infos['token'],$values,$attrs,$others);
		}
	}
	
	/**
	 * 用户密码加密规则
	 */
	public function userpassword($password = '', $encrypt = '') {
		if (empty ( $password ) && empty ( $encrypt )) {
			return "";
		}
		return md5 ( md5 ( $password ) . $encrypt );
	}
}