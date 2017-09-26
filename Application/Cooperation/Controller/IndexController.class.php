<?php
namespace Cooperation\Controller;
use Think\Controller;

/**
 * 合作商数据统一接口
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
	public function token( ){
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
		$info 	=	M('member')->where( $map )->field('userid')->limit(1)->find();
		if ( !$info ) {
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
	 * 血糖测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param float $glu 测量血糖状态值
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 */
	public function bbsugar(){
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 判断参数
		$gl 		=	I('post.glu','','htmlspecialchars,trim');
		$datetime	=	I('post.time','','htmlspecialchars,trim');

		$gl 		=	$gl ? sprintf("%.1f", $gl) : '';
	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';

	 	// 缺少参数或者参数有误
	 	if ( !$gl || !$datetime ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	$msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 			=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'gl';

	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['gl']		=	$gl;
	 	$datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;
	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 			=	M('cooperation')->add($data);
	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}


	/**
	 * 心电测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 */
	public function el(){
		ini_set('memory_limit', '128M');
		ini_set('post_max_size','20M');
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 判断参数
		$hr 		=	I('post.heartrate','','htmlspecialchars,trim');
		$eof		=	I('post.eof','','htmlspecialchars,trim');
		$datasize	=	I('post.datasize','','htmlspecialchars,trim');
		$datetime	=	I('post.time','','htmlspecialchars,trim');

		// 判断参数
		$hr 		=	$hr ? $hr : '';				//	心率值
		$datasize	=	$datasize ? $datasize : '';	//	数据流长度
		$eof		=	$eof ? $eof : '';			//	结束标志

	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';

	 	// 缺少参数或者参数有误
	 	if ( !$hr || !$datetime ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}
	 	F('coo_el',$_POST);

	 	$eldata 	=	I('ecgdata');
	 	$eldata 	=	$eldata ? urldecode($eldata) : '';
	 	// $eldata		=	'';
	 	// // 心电数据特殊获取
	 	// if ( $_FILES && isset($_FILES['filename']) && isset($_FILES['filename']['error']) && $_FILES['filename']['error'] == UPLOAD_ERR_OK) {
	 	// 	$image 	= 	$_FILES['filename']["tmp_name"];
   //          $fp 	=	fopen($image, "r");
   //          $eldata = 	fread($fp, $_FILES['filename']["size"]); //二进制数据流
	 	// }


	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	// $msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 			=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'el';

	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['hr']		=	$hr;
	 	$datas['datasize']	=	$datasize;
	 	$datas['eof']		=	$eof;
	 	// 心电数据 ========================
	 	$datas['eldata']	=	$eldata;
	 	// 心电数据 ========================

	 	// $datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;
	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 	=	M('cooperation')->add($data);
	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}



	/**
	 * 血压测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 * @param integer $sbp 收缩压
	 * @param integer $dbp 舒张压
	 * @param integer $abp 平均圧
	 * @param integer $pr 脉率
	 */
	public function bloodp(){
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 判断参数
		$sbp 		=	I('post.sbp','','intval');	//	收缩压（高压）
		$dbp 		=	I('post.dbp','','intval');	//	舒张压（低压）
		$abp 		=	I('post.abp','','intval');	//	平均圧
		$pr 		=	I('post.pr','','intval');	//	脉率

		$datetime	=	I('post.time','','htmlspecialchars,trim');


	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';


	 	// 缺少参数或者参数有误
	 	if ( !$sbp || !$datetime || !$dbp || !$pr ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	$msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 			=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'bp';

	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['sbp']		=	$sbp;
	 	$datas['dbp']		=	$dbp;
	 	$datas['abp']		=	$abp;
	 	$datas['pr']		=	$pr;
	 	$datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;
	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 			=	M('cooperation')->add($data);
	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}


	/**
	 * 血氧测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 * @param string $spo2 血氧饱和度
	 * @param string $pr 脉率
	 * 
	 * 
	 */
	public function oxygen(){
		$token 	=	I('post.token','','htmlspecialchars,trim');

		F('ox',I());

		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 判断参数
		$spo2 		=	I('post.spo2','','intval');	//	血氧饱和度
		$pr 		=	I('post.pr','','intval');	//	脉率
		$datetime	=	I('post.time','','htmlspecialchars,trim');

	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';

	 	$oxdata		=	I('post.data');

	 	// 缺少参数或者参数有误
	 	if ( !$spo2 || !$pr || !$datetime ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	$msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 			=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'ox';


	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['spo2']		=	$spo2;
	 	$datas['pr']		=	$pr;
	 	// 血氧波形 ========================
	 	$datas['oxdata']	=	$oxdata;
	 	// 血氧波形 ========================

	 	$datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;
	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 			=	M('cooperation')->add($data);
	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}


	/**
	 * 体成分测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 * @param string $fat 脂肪
	 * @param string $muscle 肌肉
	 * @param string $water 水含量
	 * @param string $skeleton 骨骼
	 * @param string $vf 内脏脂肪等级
	 * @param string $bmi 基础代谢率
	 * @param string $bmr 
	 * @param string $height 身高
	 * @param string $weight 体重
	 * @param string $age 年龄
	 * @param string $crowd 族群
	 * @param string $gender 性别
	 */
	public function humanbody(){
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 判断参数
		$fat 		=	I('post.fat','','htmlspecialchars,trim');		//	脂肪
		$muscle 	=	I('post.muscle','','htmlspecialchars,trim');	//	肌肉
		$water 		=	I('post.water','','htmlspecialchars,trim');		//	水含量
		$skeleton 	=	I('post.skeleton','','htmlspecialchars,trim');	//	骨骼
		$vf 		=	I('post.vf','','htmlspecialchars,trim');		//	内脏脂肪等级
		$bmi 		=	I('post.bmi','','htmlspecialchars,trim');		//	基础代谢率
		$bmr 		=	I('post.bmr','','htmlspecialchars,trim');	//	
		$height 	=	I('post.height','','intval');					//	身高
		$weight 	=	I('post.weight','','htmlspecialchars,trim');	//	体重
		$age 		=	I('post.age','','intval');						//	年龄
		$crowd 		=	I('post.crowd','','intval');					//	族群
		$gender 	=	I('post.gender','','intval');					//	性别

		$datetime	=	I('post.time','','htmlspecialchars,trim');

	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';

	 	// 缺少参数或者参数有误
	 	if ( !$datetime ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	$msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 			=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'we';


	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['fat']		=	$fat;
	 	$datas['muscle']	=	$muscle;
	 	$datas['water']		=	$water;
	 	$datas['skeleton']	=	$skeleton;
	 	$datas['vf']		=	$vf;
	 	$datas['bmi']		=	$bmi;
	 	$datas['bmr']		=	$bmr;
	 	$datas['height']	=	$height;
	 	$datas['weight']	=	$weight;
	 	$datas['age']		=	$age;
	 	$datas['crowd']		=	$crowd;
	 	$datas['gender']	=	$gender;

	 	// 缺少参数或者参数有误
	 	$tempcheck 	=	array_filter($datas);
	 	if ( !$tempcheck ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;
	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 			=	M('cooperation')->add($data);
	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}


	/**
	 * 尿常规测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 */
	public function urine(){
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}

		// 判断参数
		$sg 		=	I('post.sg','','htmlspecialchars,trim');		//	尿比重
		$ph 		=	I('post.ph','','htmlspecialchars,trim');		//	PH值

		$pro		=	I('post.pro','','htmlspecialchars,trim');		//	尿蛋白“-”或“+”
		$prolevel 	=	I('post.prolevel','','htmlspecialchars,trim');	//	

		$glu 		=	I('post.glu','','htmlspecialchars,trim');		//	尿糖“-”或“+”
		$glulevel 	=	I('post.glulevel','','htmlspecialchars,trim');		//	

		$ket 		=	I('post.ket','','htmlspecialchars,trim');						//	酮体“-”或“+”
		$ketlevel 	=	I('post.ketlevel','','htmlspecialchars,trim');					//	

		$ubil 		=	I('post.ubil','','htmlspecialchars,trim');		//	胆红素“-”或“+”
		$ubillevel 	=	I('post.ubillevel','','htmlspecialchars,trim');					//	

		$ubc 		=	I('post.ubc','','htmlspecialchars,trim');						//	尿胆原“-”或“+”
		$ubclevel 	=	I('post.ubclevel','','htmlspecialchars,trim');					//	

		$nit 		=	I('post.nit','','htmlspecialchars,trim');						//	亚硝酸亚“-”或“+”
		$nitlevel 	=	I('post.nitlevel','','htmlspecialchars,trim');					//	

		$leu 		=	I('post.leu','','htmlspecialchars,trim');						//	白细胞“-”或“+”
		$leulevel 	=	I('post.leulevel','','htmlspecialchars,trim');					//	

		$ery 		=	I('post.ery','','htmlspecialchars,trim');						//	潜血 “-”或“+”
		$erylevel 	=	I('post.erylevel','','htmlspecialchars,trim');					//	

		$vc 		=	I('post.vc','','htmlspecialchars,trim');						//	维生素 “-”或“+”
		$vclevel 	=	I('post.vclevel','','htmlspecialchars,trim');					//	

		$datetime	=	I('post.time','','htmlspecialchars,trim');

	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';

	 	// 缺少参数或者参数有误
	 	if ( !$datetime ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	$msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 					=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'ur';


	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['sg']		=	$sg;
	 	$datas['ph']		=	$ph;
	 	$datas['pro']		=	$pro;
	 	$datas['prolevel']	=	$prolevel;
	 	$datas['glu']		=	$glu;
	 	$datas['glulevel']	=	$glulevel;
	 	$datas['ket']		=	$ket;
	 	$datas['ketlevel']	=	$ketlevel;
	 	
	 	$datas['ubil']		=	$ubil;
	 	$datas['ubillevel']	=	$ubillevel;

	 	$datas['ubc']		=	$ubc;
	 	$datas['ubclevel']	=	$ubclevel;

	 	$datas['nit']		=	$nit;
	 	$datas['nitlevel']	=	$nitlevel;

	 	$datas['leu']		=	$leu;
	 	$datas['leulevel']	=	$leulevel;

	 	$datas['ery']		=	$ery;
	 	$datas['erylevel']	=	$erylevel;

	 	$datas['vc']		=	$vc;
	 	$datas['vclevel']	=	$vclevel;

	 	// 缺少参数或者参数有误
	 	$tempcheck 	=	array_filter($datas);
	 	if ( !$tempcheck ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}


	 	$datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;
	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 			=	M('cooperation')->add($data);

	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
		$this->ajaxReturn($result);
		exit;
	}



	/**
	 * 血脂测量结果数据上传
	 * 
	 * @param string $token 权限
	 * @param datetime $time 测量时间，时间格式（2016-09-09 10:00:00 ）
	 * @param string $gatewayid 网关ID
	 * @param string $iccardnumber 用户IC卡号
	 * @param string $deviceid 测量设备ID
	 * @param string $msrtype 测量设备类型
	 * @param string $tc 总胆固醇
	 * @param string $hdl_c 高密度脂蛋白胆固醇
	 * @param string $tg 甘油三酯
	 * @param string $ldl_c 低密度脂蛋白胆固醇
	 * @param string $ratio 胆固醇高密度比
	 */
	public function bloodfat(){
		$token 	=	I('post.token','','htmlspecialchars,trim');
		
		$result =	array();
		// 判断token
		if ( !$token ) {
			$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
		}

		// 权限判断
		$gettoken 	=	$this->get_token();
		if ( !$gettoken || !isset($gettoken['token']) || $gettoken['token'] != $token ) {
			$result['status']	=	$this->noprev;
			$this->ajaxReturn($result);
			exit;
		}



		// 判断参数
		$tc 		=	I('post.tc','','htmlspecialchars,trim');	//	总胆固醇
		$hdlc 		=	I('post.hdlc','','htmlspecialchars,trim');	//	高密度脂蛋白胆固醇
		$tg			=	I('post.tg','','htmlspecialchars,trim');	//	甘油三酯
		$ldlc 		=	I('post.ldlc','','htmlspecialchars,trim');	//	低密度脂蛋白胆固醇
		$ratio		=	I('post.ratio','','htmlspecialchars,trim');	//	胆固醇高密度比

		$datetime	=	I('post.time','','htmlspecialchars,trim');

	 	// 判断日期格式是否正确
	 	$datetime	=	 checkDateIsValid($datetime , array('Y-m-d H:i:s')) ? $datetime : '';

	 	// 缺少参数或者参数有误
	 	if ( !$datetime ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	// 网关ID
	 	$gatewayid 		=	I('post.gatewayid','','htmlspecialchars,trim');

	 	// 用户IC卡号
	 	$iccardnumber	=	I('post.iccardnumber','','htmlspecialchars,trim');

	 	// 测量设备ID
	 	$deviceid		=	I('post.deviceid','','htmlspecialchars,trim');

	 	// 测量设备类型
	 	$msrtype		=	I('post.msrtype','','htmlspecialchars,trim');


	 	$data 			=	array();
	 	$data['gatewayid']		=	$gatewayid;
	 	$data['iccardnumber']	=	$gettoken['userid'].'_'.$iccardnumber;
	 	$data['datetime']		=	$datetime;
	 	$data['addtime']		=	date('Y-m-d H:i:s');
	 	$data['apiuserid']		=	$gettoken['userid'];
	 	$data['types']			=	'bf';

	 	// 检测数据
	 	$datas 				=	array();
	 	$datas['tc']		=	$tc;
	 	$datas['hdlc']		=	$hdlc;
	 	$datas['tg']		=	$tg;
	 	$datas['ldlc']		=	$ldlc;
	 	$datas['ratio']		=	$ratio;
	 	$datas['msrtype']	=	$msrtype;
	 	$datas['deviceid']	=	$deviceid;

	 	// 缺少参数或者参数有误
	 	$tempcheck 	=	array_filter($datas);
	 	if ( !$tempcheck ) {
	 		$result['status']	=	$this->noparam;
			$this->ajaxReturn($result);
			exit;
	 	}


	 	$data['datas']		=	json_encode($datas);

	 	$data['origindatas']=	json_encode(I());

	 	$id 				=	M('cooperation')->add($data);
	 	if ( !$id ) {
	 		$result['status']	=	$this->returnError;
			$this->ajaxReturn($result);
			exit;
	 	}

	 	$result['status']	=	$this->returnOK;
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
		S('CooperationToken',$info,$this->tokenlifetime);
		return $info;
	}


	/**
	 * 获取token
	 * @return array
	 */
	private function get_token() {
		return S('CooperationToken');
	}

	


}