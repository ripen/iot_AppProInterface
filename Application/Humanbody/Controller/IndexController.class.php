<?php
namespace Humanbody\Controller;
use Think\Controller;

/**
 * 体成分API提供方接口
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
	
	// param 缺少参数
	private $noparam 		=	'-98';

	// 用户基本信息
	private $userinfo;

 	public function __construct(){
		parent::__construct();

		$this->userinfo 	=	$this->checktoken();
	}


	/**
	 * 验证token是否有效
	 * @return [type] [description]
	 */
	private function checktoken(){
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	S('APITOKEN');
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 查询附表信息
		$apiinfo 	=	M('member_api')->where( array('userid'=>$gettoken['userid']) )->find();
		return $apiinfo ? $apiinfo : array('userid'=>$gettoken['userid']);
	}


	/**
	 * 体成分数据分析
	 * @return 
	 */
	public function index(){
		// 原始数据
		$RAWDATA	=	I('post.RAWDATA','','htmlspecialchars,trim');
		// 年龄
		$age		=	I('post.age','','intval');
		
		// 性别 1：男 2：女
		$gender		=	I('post.gender','','intval');
		// 身高(CM)
		$height		=	I('post.height','','htmlspecialchars,trim');

		// MAC地址
		$mac 		=	I('post.mac','','htmlspecialchars,trim');

		// UID
		$uid 		=	I('post.uid','','htmlspecialchars,trim');

		if ( !$RAWDATA || !$age || !$gender || !$height || !$uid || !$mac ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		$post 		=	\Kbox\Model\OriginalModel::we($RAWDATA);

		$sshcinfo	=	array();

		for ($i=0; $i < 3; $i++) { 
			if ( $sshcinfo ) {
				break;
			}
			if ( $post ) {
				$post['age']	=	$age;
				$post['gender']	=	$gender;
				$post['height']	=	$height;
				$sshcinfo	=	$this->sshc($post);
			}
			usleep(10000);
		}

		if (!$sshcinfo ) {
			$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
		}

		$data['age']				=	$sshcinfo['age'];
		$data['gender']				=	$sshcinfo['gender'];
		$data['weight']				=	$sshcinfo['weight'];
		$data['height']				=	$sshcinfo['height'];
		$data['bmi']				=	$sshcinfo['bmi'];     //身体质量指数
		$data['watercontentrate']	=	$sshcinfo['watercontentrate']; // 身体水分率(百分比)
		$data['bf']					=	$sshcinfo['bodyfatrate']; //体脂率
		$data['protein']			=	$sshcinfo['visceralfatindex']; // 内脏脂肪指数
		$data['fat']				=	$sshcinfo['basalmetabolicrate'];//基础代谢率
		$data['muscle']				=	$sshcinfo['musclemass']; // 肌肉量
		$data['mineralsalts']		=	$sshcinfo['bonemass']; // 骨量
		$data['mac']				=	$mac;
		$data['uid']				=	$uid;
		
		$apiinfo 	=	$this->userinfo;
		if ( isset($apiinfo['weurl']) && $apiinfo['weurl'] ) {
			Spost($apiinfo['weurl'],$data);
		}


		// 日志记录
		$data['RAWDATA']=	$RAWDATA;
		$log 			=	array();
		$log['addtime']	=	date('Y-m-d H:i:s');
		$log['data']	=	json_encode($data);
		$log['sign']	=	'apiwe';
		$log['userid']	=	$apiinfo && $apiinfo['userid'] ? $apiinfo['userid'] : 0;
		M('equipment_log')->add($log);


		$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}



	/**
	 * 四海华城体支称分析
	 * @param  array $info 检测信息
	 * @return [type] [description]
	 */
	private function sshc( $info = array() ){
		if ( !$info ) {
			return false;
		}

		// 获取code
		$url 		=	'http://lshapi.scintakes.com/partner/author';
		$post 		=	array('appid'=>C('SHHC_APPID'),'appkey'=>C('SHHC_APPKEY'),'reptype'=>'token');

		$data 		=	$this->https_request($url,$post);
		$data 		=	$data ? json_decode($data,true) : '';
		
		
		// 体脂秤API接口返回日志记录
		$apilog 	=	array();
		$apilog['data']		=	$data;
		$this->errorkbjson(json_encode($apilog),'sshc_api_code');

		if (!$data || !$data['data']) {
			return false;
		}

		// 获取
		$url 	=	'http://lshapi.scintakes.com/partner/accesstoken';
		$post 	=	array('appid'=>C('SHHC_APPID'),'name'=>C('SHHC_NAME'),'pwd'=>C('SHHC_PWD'),'check'=>strtoupper(md5(strtoupper($data['data'].C('SHHC_APPID').C('SHHC_APPKEY')))) );

		$data 	=	$this->https_request($url,$post);

		$data 		=	$data ? json_decode($data,true) : '';
		
		
		$result =	array();

		// 体脂秤API接口返回日志记录
		$apilog 	=	array();
		$apilog['data']		=	$data;
		$this->errorkbjson(json_encode($apilog),'sshc_api_token');

		if (!$data || !$data['data']['token']) {
			return $result;
		}


		$url 	=	'http://lshapi.scintakes.com/partner/receive';
		$post 	=	array(
				'appid'		=>	C('SHHC_APPID'),
				'token'		=>	$data['data']['token'],
				'age'  		=>	$info['age'],
				'gender'  	=> 	$info['gender'],
				'height'  	=> 	$info['height'],
				'weight'  	=> 	$info['weight'],
				'valr10'  	=> 	$info['valr10'],
				'valr11'  	=> 	$info['valr11'],
				'valr12'  	=> 	$info['valr12'],
				'valr13'  	=> 	$info['valr13'],
				'valr14'  	=> 	$info['valr14'],
				'valr20'  	=> 	$info['valr20'],
				'valr21'  	=> 	$info['valr21'],
				'valr22'  	=> 	$info['valr22'],
				'valr23'  	=> 	$info['valr23'],
				'valr24'  	=> 	$info['valr24']
			);

		$result 	=	$this->https_request($url,$post);

		$result 	=	$result ? json_decode($result,true) : array();

		// 体脂秤API接口返回日志记录
		$apilog 	=	array();
		$apilog['data']		=	$result;
		$this->errorkbjson(json_encode($apilog),'sshc_api_data');

		$result 	=	$result && isset($result['data']) ? $result['data'] : array();

		return $result;
	}


	/**
	 * 有问题的设备数据记录
	 * @return bool true 
	 */
	private function errorkbjson($kbjson,$sign = 'index'){
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	$sign;
		$log['data']		=	$kbjson;
		M('equipment_logindex')->add($log);

		return true;
	}


	function https_request($url,$data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

	    curl_setopt($ch, CURLOPT_TIMEOUT,10);

	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
}