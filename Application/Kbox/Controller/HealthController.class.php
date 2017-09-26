<?php
/**
 * @author tangchengqi
 * 康宝系统7项检测接口
 * 2015.10.10 v.1.1
 */
namespace Kbox\Controller;
use Think\Controller;
use Kbox\Model\OriginalModel;

class HealthController extends Controller{

	public $successcode	=	'0';	//	保存数据成功
	public $errorcode	=	'98';	//	保存数据失败
	public $morecode	=	'97';	//	上传太频繁

	public $drugid 		=	'';	//	药店id
	public $cardid		=	''; //	卡号id

	public $cardtype	=	''; //	卡类型

	private $debug		=	false; //	调试

	public function __construct(){
		parent::__construct();
	} 
		
	
	/*康宝系统7项的
	 * 要数据插入的例子
	 * { messageType:exam,
	 * personID:”1234567890”,
	 *  devicename:”BP”, 
	 *  BPH: ”90”, 
	 *  BPL: ”70”, 
	 *  HBR: ”60”, 
	 *  examtime: ”2015-09-01 15:09:09”,
	 *  deviceUUID: ”C0-15-91-A1-90-8A”,
	 *  gateUUID : ”B0-A1-11-31-A1-81”, 
	 *  gateChannel: ”WIFI”, 
	 *  gateLocation: ”23.12-45.12”,
	 *  gateVersion: ”2.1”}
	 * 
	 * 
	 * */
	public function index(){
		$kbjson = array();
		$kbjson = I('kbjson','','trim');
		$arr 	= $kbjson ? json_decode($kbjson,true) : '';

		if ( !$arr ) {
			$msgdata['resultcode'] 	=	'101';
			$msgdata['desc'] 		=	'Parameter error';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_kbjson');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr 	=	$this->trimarr($arr);

		if ( isset($arr['debug']) && $arr['debug'] == 1) {
			$this->debug 	=	true;
		}


		$arr['messageType']	=	trim(strtolower($arr['messageType']));

		if ( !isset($arr['messageType']) || $arr['messageType'] != 'exam' ) {
			$msgdata['resultcode'] 	=	'102';
			$msgdata['desc'] 		=	'Equipment not in the detection state';
			
			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_messageType');

			$this->errrajaxreturn($msgdata);
			exit;
		}
		
		if ( !isset($arr['personID']) || !$arr['personID']  ) {
			$msgdata['resultcode'] 	=	'103';
			$msgdata['desc'] 		=	'Card number data is empty';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_personID');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['personID']	=	trim($arr['personID']);
		$user = $this->cardstatus($arr['personID']);

		if ( !$user ) {
			$msgdata['resultcode'] 	=	'104';
			$msgdata['desc'] 		=	'Users do not exist';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_user');
			
			$this->debug 	=	true;
			$this->errrajaxreturn($msgdata);
			exit;
		}

		$this->drugid	=	$user['drugid'];
		$this->cardid 	=	$user['id'];
		$this->cardtype =	$user['cardtype'];

		// 查询配置信息
		$check	=	M('equipment')->where(array('gateUUID'=>$arr['gateUUID'],'status'=>99))->order('sign desc')->find();
		if ( !$check ) {
			$msgdata['resultcode'] 	= 	'106';
			$msgdata['desc'] 		=	'No query to device information';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_equipment');

			$this->errrajaxreturn($msgdata);
			exit;
		}


		$arr['devicename']	=	trim(strtolower($arr['devicename']));
		
		// 保存请求log信息
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	'index';
		$log['data']		=	$kbjson;
		$log['gateuuid']	=	$arr['gateUUID'];
		$log['devicename']	=	$arr['devicename'];
		$log['equstatus']	=	$check['sign'];
		M('equipment_log')->add($log);



		$data	=	array();
		$data['userid']		=	$user['userid'];
		$data['gate']		=	$arr['gateUUID'];		//	网关地址
		$data['channel']	=	$arr['gateChannel'];	//  传输路径
		$data['location']	=	$arr['gateLocation'];	//	网关经纬度
		$data['version']	=	$arr['gateVersion'];	//	网关版本
		$data['createtime']	=	time();					//	入库时间
		$data['sn']			=	$arr['deviceUUID'];		//	设备地址
		$data['examtime']	=	$arr['examtime'];		//	测试时间
		$data['cardid']		=	$this->cardid;
		$data['drugid']		=	$this->drugid;
		//获取最近个人的检测时间
		$this->updatetime($data['userid'],$data['examtime'],$user['id']);

		$this->gate	=	$data['gate'];
		$this->sn 	=	$data['sn'];


		$result 			=	array();
		if($arr['devicename'] == 'bp'){
			$RAWDATA 	=	$arr['RAWDATA'];
			$original 	=	\Kbox\Model\OriginalModel::bp($RAWDATA);

			// 血压
			$data['lboodp']	=	isset($original['BPH']) ? $original['BPH'] : '';	//	低压值
			$data['hboodp']	=	isset($original['BPL']) ? $original['BPL'] : '';	//	高压值
			$data['hbr']	=	isset($original['HBR']) ? $original['HBR'] : '';	//	心率
			
			// 单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'bp');

			$result 			=	$this->bloodp($data);
		}elseif($arr['devicename']=='bf'){
			$RAWDATA 	=	$arr['RAWDATA'];
			
			$RAresult 	=	\Kbox\Model\OriginalModel::bf($RAWDATA);
			//	血脂
			$data['tg']			=	$RAresult[4];	//	甘油三脂
			$data['ltc']		=	$RAresult[6];	//	低密度脂蛋白
			$data['htc']		=	$RAresult[3];	//	高密度脂蛋白
			$data['tc']			=	$RAresult[2];	//	总胆固醇

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'bf');

			$result 			=	$this->bloodfat($data);
		}elseif($arr['devicename']=='gl'){
			//	血糖
			$RAWDATA 	=	$arr['RAWDATA'];
			$GLO 	=	\Kbox\Model\OriginalModel::gl($RAWDATA);
			$data['bloodsugar']	=	$GLO;

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'gl');

			$result 			=	$this->bbsugar($data);

		}elseif($arr['devicename']=='ur'){
			//	尿11检测	
			$RAWDATA 	=	$arr['RAWDATA'];
			$original 	=	\Kbox\Model\OriginalModel::ur($RAWDATA);

			//	尿胆原
			$data['urobilinogen']	=	isset($original['ubg']) ? $original['ubg'] : '';
			//	亚硝酸盐	
			$data['nitrite']		=	isset($original['nit']) ? $original['nit'] : '';	
			//	白细胞
			$data['whitecells']		=	isset($original['leu']) ? $original['leu'] : '';	
			//	潜血（红细胞）
			$data['redcells']		=	isset($original['bld']) ? $original['bld'] : '';	
			//	尿蛋白
			$data['urineprotein']	=	isset($original['pro']) ? $original['pro'] : '';	
			//	酸碱度
			$data['ph']				=	isset($original['ph']) ? $original['ph'] : '';	
			//	尿比重
			$data['urine']			=	isset($original['sg']) ? $original['sg'] : '';	
			//	尿酮
			$data['urineketone']	=	isset($original['ket']) ? $original['ket'] : '';	
			//	胆红素
			$data['bili']			=	isset($original['bil']) ? $original['bil'] : '';	
			//	尿糖（葡萄糖）
			$data['sugar']			=	isset($original['glu']) ? $original['glu'] : '';	
			//	维生素c
			$data['vc']				=	isset($original['vc']) ? $original['vc'] : '';	

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'ur');

			$result 			=	$this->urine($data);
		}elseif($arr['devicename']=='ox'){
			//	血氧
			$RAWDATA 	=	$arr['RAWDATA'];
			
			$original 	=	\Kbox\Model\OriginalModel::ox($RAWDATA);

			// 脉率
			$data['pr']	=	isset($original['pr']) ? $original['pr'] : '';	
			// 血氧
			$data['saturation']	=	isset($original['saturation']) ? $original['saturation'] : '';	

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'ox');

			$result 			=	$this->oxygen($data);
		}elseif($arr['devicename']=='tm'){
			//	体温
			$RAWDATA 	=	$arr['RAWDATA'];
			$original 	=	\Kbox\Model\OriginalModel::tm($RAWDATA);

			$data['tmv']=	$original;

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'tm');

			$result 			=	$this->tm($data);

		}elseif($arr['devicename']=='we'){
			//	体成分
			// 原始数据
			$RAWDATA	=	$arr['RAWDATA'];
			
			// 体脂秤原始数据日志记录
			$RAWDATAlog 	=	array();
			$RAWDATAlog['data']		=	$RAWDATA;
			$this->errorkbjson(json_encode($RAWDATAlog),'sshc_RAWDATA');


			$post 		=	\Kbox\Model\OriginalModel::we($RAWDATA);

			$userinfo 	=	$this->getuserinfo($user['userid']);

			$sshcinfo	=	array();

			$sign 		=	'';
			if ( $post && isset($post['yolanda']) && $post['yolanda'] == 1 ) {
				$sshcinfo['weight']				=	$post['weight'];
				$sshcinfo['bf']					=	isset($post['bf']) ? $post['bf'] : '';
				$sshcinfo['watercontentrate']	=	isset($post['watercontentrate']) ? $post['watercontentrate'] : '';
				$sshcinfo['muscle']				=	isset($post['muscle']) ? $post['muscle'] : '';
				$sshcinfo['mineralsalts']		=	isset($post['mineralsalts']) ? $post['mineralsalts'] : '';

				$sign 	=	1;
			}


			for ($i=0; $i < 3; $i++) { 
				if ( $sshcinfo ) {
					break;
				}
				if ( $userinfo && $post ) {
					$post['age']	=	$userinfo['age'];
					$post['gender']	=	$userinfo['gender'];
					$post['height']	=	$userinfo['height'];
					$sshcinfo	=	$this->sshc($post);
				}
				usleep(10000);
			}

			if ( $sshcinfo && !$sign ) {
				$data['age']				=	$sshcinfo['age'];
				$data['gender']				=	$sshcinfo['gender'];
				$data['weight']				=	$sshcinfo['weight'];
				$data['height']				=	$sshcinfo['height'];
				$data['bmi']				=	$sshcinfo['bmi'];     //身体质量指数
				$data['bodyfat']			=	$sshcinfo['bodyfat']; // 体脂肪
				$data['bf']					=	$sshcinfo['bodyfatrate']; //体脂率
				$data['fatweight']			=	$sshcinfo['fatfreemass']; // 去脂体重
				$data['water']				=	$sshcinfo['watercontent']; // 身体水分
				$data['watercontentrate']	=	$sshcinfo['watercontentrate']; // 身体水分率(百分比)
				$data['protein']			=	$sshcinfo['visceralfatindex']; // 内脏脂肪指数
				$data['mineralsalts']		=	$sshcinfo['bonemass']; // 骨量
				$data['muscle']				=	$sshcinfo['musclemass']; // 肌肉量
				$data['fat']				=	$sshcinfo['basalmetabolicrate'];//基础代谢率

				$data['bfmt']				=	$sshcinfo['bfmt'];
				$data['bfmal']				=	$sshcinfo['bfmal'];
				$data['bfmar']				=	$sshcinfo['bfmar'];
				$data['bfmll']				=	$sshcinfo['bfmll'];
				$data['bfmlr']				=	$sshcinfo['bfmlr'];
				$data['lmt']				=	$sshcinfo['lmt'];
				$data['lmal']				=	$sshcinfo['lmal'];
				$data['lmar']				=	$sshcinfo['lmar'];
				$data['lmll']				=	$sshcinfo['lmll'];
				$data['lmlr']				=	$sshcinfo['lmlr'];

				$data['valr10']				=	$sshcinfo['valr10'];
				$data['valr11']				=	$sshcinfo['valr11'];
				$data['valr12']				=	$sshcinfo['valr12'];
				$data['valr13']				=	$sshcinfo['valr13'];
				$data['valr14']				=	$sshcinfo['valr14'];
				$data['valr20']				=	$sshcinfo['valr20'];
				$data['valr21']				=	$sshcinfo['valr21'];
				$data['valr22']				=	$sshcinfo['valr22'];
				$data['valr23']				=	$sshcinfo['valr23'];
				$data['valr24']				=	$sshcinfo['valr24'];
			}elseif( $sshcinfo && $sign == 1 ){
				$data['age']				=	$userinfo['age'];
				$data['gender']				=	$userinfo['gender'];
				$data['height']				=	$userinfo['height'];
				$data['weight']				=	$sshcinfo['weight'];
				$data['bf']					=	$sshcinfo['bf']; 				//	体脂率
				$data['watercontentrate']	=	$sshcinfo['watercontentrate']; 	// 	身体水分率(百分比)
				$data['mineralsalts']		=	$sshcinfo['mineralsalts']; 		// 	骨量
				$data['muscle']				=	$sshcinfo['muscle']; 		// 	肌肉量

				// BMI 通过计算获得	计算公式 BMI = 体重（kg）/ ( (身高 m）* (身高 m）)
				// 身高单位转换
				$h 							=	$userinfo['height'] ? $userinfo['height'] / 100.0 : 0;
				$h 							=	$h ? number_format($h, 1, '.', '') : 0;
				$bmi						=	$h ? $sshcinfo['weight'] / ( $h * $h ) : '';
				$data['bmi']				=	$bmi ? number_format($bmi, 1, '.', '') : '';
				$data['status']				=	2;
			}else{
				$data['weight']				=	$post['weight'];
				$data['valr10']				=	$post['valr10'];
				$data['valr11']				=	$post['valr11'];
				$data['valr12']				=	$post['valr12'];
				$data['valr13']				=	$post['valr13'];
				$data['valr14']				=	$post['valr14'];
				$data['valr20']				=	$post['valr20'];
				$data['valr21']				=	$post['valr21'];
				$data['valr22']				=	$post['valr22'];
				$data['valr23']				=	$post['valr23'];
				$data['valr24']				=	$post['valr24'];


				// 错误原始数据记录
				$sshclog 	=	array();
				$sshclog['post']		=	$post;
				$sshclog['userinfo']	=	$userinfo;
				$this->errorkbjson(json_encode($sshclog),'sshc_error');
			}

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'we');

			$result		=	$this->humanbody($data);
		}elseif($arr['devicename']=='el'){
			//	心电
			$RAWDATA	=	$arr['RAWDATA'];

			$original 	=	\Kbox\Model\OriginalModel::el($RAWDATA);
			$num 		=	$original['num'];
			$origindata	=	$original['origindata'];
			// $rawend 	=	$original['RAWDATAENDING'];
			// if ($rawend) {
			// 	$data['status']	=	1;
			// }
			
			$data['origindata']	=	$num.':'.$origindata.'|';

			//  单项编码
			$data['reportcode']	=	$this->createcode($this->drugid,$user['userid'],'el');

			$result		=	$this->electrocardio($data);
		}else{
			$result['resultcode'] 	=	'105';
			$result['desc'] 		=	'Test items do not exist';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_tests');

			$this->errrajaxreturn($msgdata);
			exit;
		}
		
		echo $this->ajaxReturn($result);
		exit;
	}

	

	


	/**
	 * 2.3	请求配置信息接口
	 * eg:{"messageType":"Config","gateUUID":"B0-A1-11-31-A1-81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 * 
	 * @return json 格式数据
	 */
	public function configs(){
		$kbjson =	I('kbjson','','trim');
		$arr 	= 	$kbjson ? json_decode($kbjson,true) : '';

		if ( !$arr ) {
			$msgdata['resultcode'] 	= 	'301';
			$msgdata['desc'] 		=	'Parameter error';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'configs_error_kbjson');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr 	=	$this->trimarr($arr);

		if ( isset($arr['debug']) && $arr['debug'] == 1 ) {
			$this->debug 	=	true;
		}

		if ( !isset($arr['gateUUID']) || !$arr['gateUUID'] ) {
			$msgdata['resultcode'] 	= 	'302';
			$msgdata['desc'] 		=	'Gateway address can not be empty';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'configs_error_gateUUID');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['messageType']	=	trim(strtolower($arr['messageType']));

		if ( !isset($arr['messageType']) || $arr['messageType'] != 'config') {
			$msgdata['resultcode'] 	= 	'303';
			$msgdata['desc'] 		=	'Interface type is incorrect';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'configs_error_messageType');


			$this->errrajaxreturn($msgdata);
			exit;
		}
		
		$arr['gateUUID']	=	trim($arr['gateUUID']);


		// 查询配置信息
		$info	=	M('equipment')->where(array('gateUUID'=>$arr['gateUUID'],'status'=>99))->order('sign desc')->find();

		if ( !$info ) {
			$msgdata['resultcode'] 	= 	'304';
			$msgdata['desc'] 		=	'No query to device information';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'configs_error_equipment');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 保存请求log信息
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	'configs';
		$log['data']		=	$kbjson;
		$log['gateuuid']	=	$arr['gateUUID'];
		$log['equstatus']	=	$info['sign'];
		M('equipment_log')->add($log);


		// 处理数据
		$result 	=	array();
		// 血压
		if ( $info['bp'] && $info['bpstatus'] == 1 ) {
			$result['BP']	=	$info['bp'];
		}
		// 血糖
		if ( $info['gl'] && $info['glstatus'] == 1 ) {
			$result['GL']	=	$info['gl'];
		}
		// 体重
		if ( $info['we'] && $info['westatus'] == 1 ) {
			$result['WE']	=	$info['we'];
		}
		// 血脂
		if ( $info['bf'] && $info['bfstatus'] == 1 ) {
			$result['BF']	=	$info['bf'];
		}
		// 尿液
		if ( $info['ur'] && $info['urstatus'] == 1 ) {
			$result['UR']	=	$info['ur'];
		}
		// 血氧
		if ( $info['ox'] && $info['oxstatus'] == 1 ) {
			$result['OX']	=	$info['ox'];
		}
		// 体温
		if ( $info['tm'] && $info['tmstatus'] == 1 ) {
			$result['TM']	=	$info['tm'];
		}
		// 心电
		if ( $info['el'] && $info['elstatus'] == 1 ) {
			$result['EL']	=	$info['el'];
		}

		// if ( !$result ) {
		// 	$msgdata['resultcode'] 	= 	'305';
		// 	$msgdata['desc'] 		=	'Non binding equipment';
		// 	echo $this->ajaxReturn($msgdata);
		// 	exit;
		// }

		$result['resultcode']	=	"0";
		$result['desc']			=	"success";

		// WIFI 信息
		$result['WIFIAP_SSID']	=	$info['wifi'] ? $info['wifi'] : '';
		$result['WIFIPWD']		=	$info['wifipw'] ? $info['wifipw'] : '';
		$result['SERVERURL']	=	'api.yicheng120.com';

		echo $this->ajaxReturn($result);
		exit;
	}

	/**
	 * 开机注册接口
	 * 	eg:{"messageType":"powerOn","gateUUID":"B0-A1-11-31-A1-81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * 
	 * @return json
	 */
	public function register(){
		$kbjson =	I('kbjson','','trim');
		$arr 	= 	$kbjson ? json_decode($kbjson,true) : '';

		if ( !$arr ) {
			$msgdata['resultcode'] 	= 	'201';
			$msgdata['desc'] 		=	'Parameter error';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_kbjson');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr 	=	$this->trimarr($arr);

		if ( isset($arr['debug']) && $arr['debug'] == 1) {
			$this->debug 	=	true;
		}

		if ( !isset($arr['gateUUID']) || !$arr['gateUUID'] ) {
			$msgdata['resultcode'] 	= 	'202';
			$msgdata['desc'] 		=	'Gateway address can not be empty';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_gateUUID');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['messageType']	=	trim(strtolower($arr['messageType']));
		if ( !isset($arr['messageType']) || $arr['messageType'] != 'poweron') {
			$msgdata['resultcode'] 	= 	'203';
			$msgdata['desc'] 		=	'Interface type is incorrect';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_messageType');


			$this->errrajaxreturn($msgdata);
			exit;
		}
		
		$arr['gateUUID']	=	trim($arr['gateUUID']);
		

		
		// 查询配置信息
		$info	=	M('equipment')->where(array('gateUUID'=>$arr['gateUUID'],'status'=>99))->order('sign desc')->find();

		if ( !$info ) {
			// $msgdata['resultcode'] 	= 	'204';
			// $msgdata['desc'] 		=	'No query to device information';
			// echo $this->ajaxReturn($msgdata);
			// exit;
			
			// 自动绑定设备到 factorytest 账户下边
			// 查询用户 factorytest 的 userid信息
			$finfo 	=	M('member')->where(array('username'=>'factorytest'))->field('userid')->find();
			$minfo	=	array();
			$minfo['userid']	=	$finfo['userid'];
			$minfo['status']	=	99;
			$minfo['adminid']	=	0;
			$minfo['deviceUUID']=	$arr['deviceUUID'];
			$minfo['gateUUID']	=	$arr['gateUUID'];
			$minfo['sign']		=	0;
			$minfo['wifi']		=	'yicheng123';
			$minfo['wifipw']	=	'yicheng123';

			$minfo['gl']		=	'08-7c-be-20-67-d0';
			$minfo['glstatus']	=	1;

			$info['wifi']		=	'yicheng123';
			$info['wifipw']		=	'yicheng123';
			$info['gl']			=	'08-7c-be-20-67-d0';
			$info['glstatus']	=	1;

			M('equipment')->add($minfo);
		}

		// 保存请求信息
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	'register';
		$log['data']		=	$kbjson;
		$log['gateuuid']	=	$arr['gateUUID'];
		$log['equstatus']	=	$info['sign'] ? $info['sign'] : 0;
		M('equipment_log')->add($log);

		// 处理数据
		$result 	=	'';
		$data 	=	array();
		// 血压
		if ( $info && $info['bp'] && $info['bpstatus'] == 1 ) {
			$result 	.=	'BP';
			$data['BP']	=	$info['bp'];
		}
		// 血糖
		if ( $info && $info['gl'] && $info['glstatus'] == 1 ) {
			$result 	.=	$result  ? '-GL' : 'GL';
			$data['GL']	=	$info['gl'];
		}
		// 体重
		if ( $info &&  $info['we'] && $info['westatus'] == 1 ) {
			$result 	.=	$result  ? '-WE' : 'WE';
			$data['WE']	=	$info['we'];
		}
		// 血脂
		if ( $info &&  $info['bf'] && $info['bfstatus'] == 1 ) {
			$result 	.=	$result  ?  '-BF' : 'BF';
			$data['BF']	=	$info['bf'];
		}
		// 尿液
		if ( $info &&  $info['ur'] && $info['urstatus'] == 1 ) {
			$result 	.=	'-UR';
			$data['UR']	=	$info['ur'];
		}
		// 血氧
		if ( $info &&  $info['ox'] && $info['oxstatus'] == 1 ) {
			$result 	.=	$result  ? '-OX' : 'OX';
			$data['OX']	=	$info['ox'];
		}
		// 体温
		if ( $info &&  $info['tm'] && $info['tmstatus'] == 1 ) {
			$result 	.=	$result  ? '-TM' : 'TM';
			$data['TM']	=	$info['tm'];
		}
		// 心电
		if ( $info &&  $info['el'] && $info['elstatus'] == 1 ) {
			$result 	.=	$result  ? '-EL' : 'EL';
			$data['EL']	=	$info['el'];
		}



		// if ( !$result ) {
		// 	$msgdata['resultcode'] 	= 	'205';
		// 	$msgdata['desc'] 		=	'Non binding equipment';
		// 	echo $this->ajaxReturn($msgdata);
		// 	exit;
		// }

		$data['resultcode']		=	"0";
		$data['desc']			=	'success';
		$data['acceptDevice']	=	$result;


		// WIFI 信息
		$data['WIFIUSED']		=	$info['wifi'] ? "1" : '';
		$data['WIFIAP_SSID']	=	$info['wifi'] ? $info['wifi'] : '';
		$data['WIFIPWD']		=	$info['wifipw'] ? $info['wifipw'] : '';
		$data['SERVERURL']		=	'api.yicheng120.com';

		$data['date']			=	date("Y-m-d");
		$data['time']			=	date('H:i:s');

		// 该参数暂时固定
		$data['hbrate']			=	$info['hbrate'] ? $info['hbrate'] : "100";

		// 判断是否允许升级
		$getupdate		=	array();
		if ( isset($info['isupdate']) && $info['isupdate'] ) {
			$getupdate	=	$this->getupdate($info['version']);
		}

		if ( $getupdate ) {
			$data['updateFWURL']	=	'http://platform.yicheng120.com/uploadfile/'.$getupdate['files'];
			$data['version']		=	(string)$getupdate['id'];
		}else{
			$data['updateFWURL']	=	'';
			$data['version']		=	(string)$info['version'];
		}

		// 如果传递参数的版本号与给定的不同，则进行更新版本号操作
		if ( $info['version'] != $arr['gateVersion'] ) {
			M('equipment')->where(array('gateUUID'=>$arr['gateUUID']))->save(array('version'=>$data['version']));
		}

		echo $this->ajaxReturn($data);
		exit;
	}


	/**
	 * 仪器心跳
	 * @return json
	 */
	public function heartbeat(){
		$kbjson =	I('kbjson','','trim');
		$arr 	= 	$kbjson ? json_decode($kbjson,true) : '';

		if ( !$arr ) {
			$msgdata['resultcode'] 	= 	'501';
			$msgdata['desc'] 		=	'Parameter error';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_kbjson');

			$this->errrajaxreturn($msgdata);
			exit;
		}
		$arr 	=	$this->trimarr($arr);
		
		if ( isset($arr['debug']) && $arr['debug'] == 1) {
			$this->debug 	=	true;
		}

		$arr['gateUUID']	=	trim($arr['gateUUID']);
		if ( !isset($arr['gateUUID']) || !$arr['gateUUID'] ) {
			$msgdata['resultcode'] 	= 	'502';
			$msgdata['desc'] 		=	'Gateway address can not be empty';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_gateUUID');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['messageType']	=	trim(strtolower($arr['messageType']));
		if ( !isset($arr['messageType']) || $arr['messageType'] != 'heartbeat' ) {
			$msgdata['resultcode'] 	= 	'503';
			$msgdata['desc'] 		=	'Interface type is incorrect';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_messageType');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 查询配置信息
		$info	=	M('equipment')->where(array('gateUUID'=>$arr['gateUUID'],'status'=>99))->order('sign desc')->find();

		if ( !$info ) {
			$msgdata['resultcode'] 	= 	'504';
			$msgdata['desc'] 		=	'No query to device information';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'register_error_equipment');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 保存请求信息
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	'heartbeat';
		$log['data']		=	$kbjson;
		$log['gateuuid']	=	$arr['gateUUID'];
		$log['equstatus']	=	$info['sign'] ;
		M('equipment_log')->add($log);

		$data 				=	array();
		$data['resultcode']	= 	'0';
		$data['desc']		=	'success';
		echo $this->ajaxReturn($data);
		exit;
	}

	/**
	 * 体检状态信息
	 * @return [type] [description]
	 */
	public function examstatus(){
		$kbjson =	I('kbjson','','trim');
		$arr 	= 	$kbjson ? json_decode($kbjson,true) : '';

		if ( !$arr ) {
			$msgdata['resultcode'] 	= 	'601';
			$msgdata['desc'] 		=	'Parameter error';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_kbjson');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr 	=	$this->trimarr($arr);
		
		if ( isset($arr['debug']) && $arr['debug'] == 1) {
			$this->debug 	=	true;
		}

		$arr['gateUUID']	=	trim($arr['gateUUID']);
		if ( !isset($arr['gateUUID']) || !$arr['gateUUID'] ) {
			$msgdata['resultcode'] 	= 	'602';
			$msgdata['desc'] 		=	'Gateway address can not be empty';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_gateUUID');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['messageType']	=	trim(strtolower($arr['messageType']));
		if ( !isset($arr['messageType']) || $arr['messageType'] != 'examstatus' ) {
			$msgdata['resultcode'] 	= 	'603';
			$msgdata['desc'] 		=	'Interface type is incorrect';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_messageType');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		if ( !isset($arr['personID']) || !$arr['personID']  ) {
			$msgdata['resultcode'] 	=	'604';
			$msgdata['desc'] 		=	'Card number data is empty';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_personID');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['personID']	=	trim($arr['personID']);
		$user = $this->cardstatus($arr['personID']);
		
		if ( !$user ) {
			$msgdata['resultcode'] 	=	'605';
			$msgdata['desc'] 		=	'Users do not exist';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_user');

			$this->debug 	=	true;
			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 查询配置信息
		$info	=	M('equipment')->where(array('gateUUID'=>$arr['gateUUID'],'status'=>99))->order('sign desc')->find();

		if ( !$info ) {
			$msgdata['resultcode'] 	= 	'607';
			$msgdata['desc'] 		=	'No query to device information';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_equipment');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 保存请求信息
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	'examination';
		$log['data']		=	$kbjson;
		$log['gateuuid']	=	$arr['gateUUID'];
		$log['equstatus']	=	$info['sign'] ;
		M('equipment_log')->add($log);

		$state	=	$arr['ExamState'];
		if ( !$state && $state != 0) {
			$msgdata['resultcode'] 	=	'606';
			$msgdata['desc'] 		=	'ExamState do not exist';

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'examstatus_error_ExamState');


			$this->errrajaxreturn($msgdata);
			exit;
		}

		$state		=	hexdec($state);

		// 流水记录表，简单记录当前检测状态
		$data 		=	array('bp'=>'1','gl'=>'1','ox'=>'1','ur'=>'1','bf'=>'1','el'=>'1','we'=>'1','tm'=>'1');
		
		// 历史记录信息，详细记录信息
		$history	=	array('bp'=>'1','gl'=>'1','ox'=>'1','ur'=>'1','bf'=>'1','el'=>'1','we'=>'1','tm'=>'1');

		if ( ( $state & 0X80) == 0 ) {
	       $data['bp']		=	0;	//	血压
	       $history['bp']	=	0;	//	血压
	    }
	    
	    if ( ( $state & 0X40) ==0 ) {
	       $data['gl']		=	0;	//	血糖

	       $history['gl']	=	0;	//	血糖
	    }
	    if ( ( $state & 0X20) ==0 ) {
	       $data['ox']		=	0;	//	血氧

	       $history['ox']	=	0;	//	血氧
	    }

	    if ( ( $state & 0X10) ==0 ) {
	       $data['ur']		=	0;	//	尿液

	       $history['ur']	=	0;	//	尿液
	    }

	    if ( ( $state & 0X08) ==0 ) {
	       $data['bf']		=	0;	//	血脂

	       $history['bf']	=	0;	//	血脂
	    }

	    if ( ( $state & 0X04) ==0 ) {
	       $data['el']		=	0;	//	心电

	       $history['el']	=	0;	//	心电
	    }

	    if ( ( $state & 0X02) ==0 ) {
	       $data['we']		=	0;	//	体重

	       $history['we']	=	0;	//	体重
	    }

	    if ( ( $state & 0X01) ==0 ) {
	       $data['tm']		=	0;	//	恒温

	       $history['tm']	=	0;	//	恒温
	    }

	   
		$data['userid']	=	$user['userid'];
		$data['time']	=	date('Y-m-d');

		$history['userid']	=	$user['userid'];
		$history['time']	=	date('Y-m-d');

		$ongo 		=	'';
		if (isset($arr['ExamOnGoing']) && $arr['ExamOnGoing'] ) {
			$ongo 	=	trim(strtolower($arr['ExamOnGoing']));

			if ( $ongo == 'done') {
				$data['status']	=	0;
			}elseif( $ongo == 'idle' ){
				$data['status']	=	1;
			}elseif( in_array($ongo,array('bp','gl','ox','ur','bf','el','we','tm')) ){
				$data[$ongo]	=	2;

				$history[$ongo]	=	2;
				$history[$ongo.'btime']	=	time();	

				$data['status']	=	2;
			}
		}

		// 设备地址
		$history['gateuuid']	=	trim($arr['gateUUID']);

		//	判断当天是否已经有检测项，如果检测项，进行更新处理
		$where 	=	array();
		$where['userid']	=	$user['userid'];
		$where['time']		=	date('Y-m-d');
		$check 	=	M('examstate')->where($where)->find();

		if ( !$check ) {
			$data['gateuuid']	=	$arr['gateUUID'];
			$data['personid']	=	$user['personID'];
			$data['drugid']		=	$user['drugid'];
			M('examstate')->add($data);

			$history['personid']	=	$user['personID'];
			$history['drugid']		=	$user['drugid'];
			$history['cardid']		=	$user['id'];
			$insertid 	=	M('examstate_history')->add($history);

			// 删除缓存的进程ID信息
			S("kangbao_examstatus".$user['id'],NULL);

			// 缓存有效期 2 个小时
			S("kangbao_examstatus".$user['id'],$insertid,7200);
		}

		if( $ongo && $ongo == 'done'){
			M('examstate')->where($where)->delete();

			// 判断当前用户是否绑定了微信信息
			if ($user['from'] && $user['from'] == 'weixin' && $user['connectid']) {
				$wxreport 	=	array();
				$wxreport['wxid']	=	$user['connectid'];
				$wxreport['userid']	=	$user['userid'];
				$wxreport['addtime']=	time();
				$wxreport['status']	=	0;
				M('wx_report')->add($wxreport);
			}

			// 判断当前用户是否绑定了api信息，如有绑定，进行数据推送
			$apiwhere	=	array();
			$apiwhere['userid']	=	$user['userid'];
			$apiwhere['drugid']	=	$user['drugid'];
			$apicheck	=	M('user_api_from')->where($apiwhere)->find();
			if ($apicheck) {
				$wxreport 	=	array();
				$wxreport['wxid']	=	0;
				$wxreport['userid']	=	$user['userid'];
				$wxreport['addtime']=	time();
				$wxreport['status']	=	0;
				$wxreport['resource']	=	2;
				$wxreport['apiid']		=	$apicheck['apiuserid'];
				M('wx_report')->add($wxreport);
			}
			
		}else{
			$data['updatetime']	=	time();
			M('examstate')->where($where)->save($data);
		}

		// 更新历史记录表开始检测时间
		$hinfo 	=	M('examstate_history')->where($where)->order('id desc')->find();

		if ( $hinfo && !$hinfo['bpetime'] && $hinfo['bpbtime'] &&  $data['bp'] ==	0 ) {
	    	$history['bpetime']	=	time();
	    }
	    
	    if ( $hinfo && !$hinfo['gletime'] && $hinfo['glbtime'] &&  $data['gl'] ==	0 ) {
	    	$history['gletime']	=	time();
	    }

	    if ( $hinfo && !$hinfo['oxetime'] && $hinfo['oxbtime'] &&  $data['ox'] ==	0 ) {
	    	$history['oxetime']	=	time();
	    }
	    
	    if ( $hinfo && !$hinfo['uretime'] && $hinfo['urbtime'] &&  $data['ur'] ==	0 ) {
	    	$history['uretime']	=	time();
	    }
	    
	    if ( $hinfo && !$hinfo['bfetime'] && $hinfo['bfbtime'] &&  $data['bf'] ==	0 ) {
	    	$history['bfetime']	=	time();
	    }

	    if ( $hinfo && !$hinfo['eletime'] && $hinfo['elbtime'] &&  $data['el'] ==	0 ) {
	    	$history['eletime']	=	time();
	    }

	    if ( $hinfo && !$hinfo['weetime'] && $hinfo['webtime'] &&  $data['we'] ==	0 ) {
	    	$history['weetime']	=	time();
	    }
	    
	    if ( $hinfo && !$hinfo['tmetime'] && $hinfo['tmbtime'] &&  $data['tm'] ==	0 ) {
	    	$history['tmetime']	=	time();
	    }

		$history['updatetime']	=	time();
		M('examstate_history')->where($where)->order('id desc')->limit(1)->save($history);
	

		$msgdata['desc'] 		=	'success';
		$msgdata['resultcode'] 	=	$this->successcode;
		echo $this->ajaxReturn($msgdata);
		exit;
	}
	
	/**
	 *获取血糖信息 
	 */
	private function bbsugar($data = array()){
		
		$msgdata =array();
		if($this->getsncache($data['sn'])){
			$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid	=	M('kangbao_bbsugar')->add($data);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;
				
				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$data['userid'];
				$queue['type']		=	'gl';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$data['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($data['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}
	
	/**
	 *获取体温
	 */
	private function tm($data = array()){
		$msgdata =array();
		if($this->getsncache($data['sn'])){
			$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid	=	M('kangbao_tm')->add($data);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;
				
				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$data['userid'];
				$queue['type']		=	'tm';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$data['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($data['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}

	/**
	 *获取血脂信息
	 */
	private function bloodfat($data = array()){
		
		$msgdata =array();
		if( $this->getsncache($data['sn']) ){
			$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid	=	M('kangbao_bloodfat')->add($data);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;
				
				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$data['userid'];
				$queue['type']		=	'bf';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$data['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($data['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}
	
	/**
	 *获取血压信息
	 */
	private function bloodp($data = array()){
		$msgdata =array();
		if($this->getsncache($data['sn'])){
			$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid 	=	M('kangbao_bloodp')->add($data);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;

				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$data['userid'];
				$queue['type']		=	'bp';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$data['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($data['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}

	/**
	 *获取血氧信息
	 */
	private function oxygen( $info = array() ){
		$msgdata =array();

		if( $this->getsncache($info['sn']) ){
			$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid	=	M('kangbao_oxygen')->add($info);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;

				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$info['userid'];
				$queue['type']		=	'ox';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$info['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($info['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}
	
	/**
	 *获取尿11项信息
	 */
	private function urine($data = array()){
		$msgdata =array();
		if($this->getsncache($data['sn'])){
			$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid	=	M('kangbao_urine')->add($data);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;
				
				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$data['userid'];
				$queue['type']		=	'ur';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$data['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($data['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}
	
	/**
	 *获取心电信息
	 * 需要特殊处理，心电数据为分段发送的
	 */
	private function electrocardio($info = array()){
		$msgdata	=	array();

		// 根据用户id和添加时间判断
		$check 	=	M('kangbao_electrocardio')->where(array('userid'=>$info['userid'],'status'=>0))->find();
		$result =	false;

		$nums 	=	0;

		if ( $check ) {
			$info['origindata']	=	$check['origindata'].$info['origindata'];
			
			$nums 			=	$info['nums'];

			$info['nums']	=	$check['nums'] + 1;

			$info['status']	=	$info['nums'] == 60 ? 1 : 0;

			$result 		=	M('kangbao_electrocardio')->where(array('userid'=>$info['userid'],'status'=>0))->save($info);
		}else{
			$nums			=	1;
			$info['nums']	=	$nums;
			$result 	=	M('kangbao_electrocardio')->add($info);
		}

		$tempdata		=	'';
		if ($info['nums'] == 60 && $info['status'] && $info['origindata']) {
			// 解析心电原始数据
			$data 	=	\Kbox\Model\OriginalModel::eldata($info['origindata']);

			$upinfo		=	array();
			$upinfo['hr']	=	$data['hr'];
			$upinfo['bpm']	=	$data['bpm'];
			$tempdata 	=	M('kangbao_electrocardio')->where(array('userid'=>$info['userid'],'id'=>$check['id']))->save($upinfo);
		}

		if ( $info['status'] == 1 && $tempdata ) {
			// 保存数据到队列库中
			$queue 	=	array();
			$queue['userid']	=	$info['userid'];
			$queue['type']		=	'el';
			$queue['insertid']	=	$check['id'];
			$queue['extime']	=	$info['examtime'];
			$this->addqueue($queue);

			// 删除保存不够60条的记录
			$where 	=	array();
			$where['userid']	=	$info['userid'];
			$where['nums']		=	array('neq',60);
			M('kangbao_electrocardio')->where($where)->delete();
		}

		if($result){
			$msgdata['desc'] 		=	'success';
			$msgdata['resultcode'] 	=	$this->successcode;
			//设备号插入缓存
			$this->setsncache($info['sn']);
		}else{
			$msgdata['desc'] 		=	'Data upload failed';
			$msgdata['resultcode'] 	=	$this->errorcode;
		}

			
		return $msgdata;
	}
	
	
	/**
	 *获取人体成分信息
	 */
	private function humanbody($info = array()){
		$msgdata =array();
		if($this->getsncache($info['sn'])){
		 	$msgdata['desc'] 		=	'Data preservation is too frequent';
			$msgdata['resultcode'] 	=	$this->morecode;
		}else{
			$insertid 	=	M('kangbao_humanbody')->add($info);
			if( $insertid ){
				$msgdata['desc'] 		=	'success';
				$msgdata['resultcode'] 	=	$this->successcode;
				
				// 保存数据到队列库中
				$queue 	=	array();
				$queue['userid']	=	$info['userid'];
				$queue['type']		=	'we';
				$queue['insertid']	=	$insertid;
				$queue['extime']	=	$info['examtime'];
				$this->addqueue($queue);

				//设备号插入缓存
				$this->setsncache($info['sn']);
			}else{
				$msgdata['desc'] 		=	'Data upload failed';
				$msgdata['resultcode'] 	=	$this->errorcode;
			}
		}
		return $msgdata;
	}
	
	
	
	/**
	 *简单的防止数据库被恶意插入 ,判定同一设备,5秒内不能插入
	 *@param $sn 获取的设备号 
	 *@return 返回设备缓存信息   
	 */
	public function setsncache($sn =''){
		if(empty($sn)){
			return '';
		}
		if(!S($sn."kangbao_device")){
			S($sn."kangbao_device",$sn,5);
		}
	}
	/**
	 * 获取缓存值
	 * @param string $sn 设备号
	 * @return string|mixed  */
	public function getsncache($sn=''){
		if(empty($sn)){
			return '';
		}
		return S($sn."kangbao_device");
	}
	
	/**
	 *设备传来的卡号是否激活 
	 * @param $cardnum
	 */
	public function cardstatus($cardnum){

		if ( !$cardnum ) {
			return false;
		}

		// 卡号判断处理
		// 模拟卡号规则：a8dfcd  13 556677 88  
		// 卡号说明 a8dfcd为校验位   13 卡号前缀 556677 实际卡号 88 干扰数字
		$len 	=	strlen($cardnum);
		if ( $len != 16) {
			return false;
		}
		// 卡号校验
		$check 	=	array();
		for ($i=6; $i <= 14 ; $i=$i+2 ) { 
			//	16进制转换10进制
			$check[]	=	intval(hexdec($cardnum{$i}.$cardnum{$i+1}));
		}

		// 开始检验
		$last		=	0;
		$center		=	0;
		foreach($check AS $key => $val){
			$last	+=	$val;
			$center	=	$center ? $center ^ $val : $val;
		}

		//	10进制转换16进制
		$last	=	dechex($last) ;
		$center	=	dechex($center) ;
		$last	=	substr($last,-2);

		// 补0操作
		$center =	str_pad($center,2,"0",STR_PAD_LEFT);
		$last 	=	str_pad($last,2,"0",STR_PAD_LEFT);
		
		$verification 	=	substr($cardnum,0,6);

		if (strtolower($verification) != strtolower('a8'.$center.$last) ) {
			return false;
		}

		// 卡号前缀
		$cardprev	=	substr($cardnum,6,2);
		// 卡号
		$cardcenter	=	substr($cardnum,8,6);
		// 卡号随机因子
		$cardencrypt=	substr($cardnum,14,2);


		$user = array();
		$user = $this->getuserid($cardprev,$cardcenter,$cardencrypt);
		return $user ? $user : '';
		
	}
	
	
	/**
	 * @param usercard:卡号
	 * @retrun userid:用户id
	 *    
	 */
	private function getuserid($cardprev,$cardnum,$cardencrypt){
		if( !$cardprev || !$cardnum || !$cardencrypt ){
			return '';
		}
		$card = array();
		$Model = M('drug_card_user');

		$prefix 	=	C('DB_PREFIX');

		$card = $Model
				->field(''.$prefix.'member.userid,'.$prefix.'member.connectid,'.$prefix.'member.from,'.$prefix.'drug_card_user.id,'.$prefix.'drug_card_user.drugid,'.$prefix.'drug_card_user.cardtype')
				->join(''.C('DB_PREFIX').'member ON '.$prefix.'drug_card_user.userid = '.$prefix.'member.userid')
				->where("".$prefix."drug_card_user.cardnum='$cardnum' AND ".$prefix."drug_card_user.status=1 AND ".$prefix."drug_card_user.cardprev = '$cardprev' AND ".$prefix."drug_card_user.cardencrypt = '$cardencrypt' ")
				->find();

		if ( $card ) {
			$card['personID']	=	$cardprev.$cardnum.$cardencrypt;
		}

		return $card ? $card:'';
	}
	

	/**
	 * 获取用户信息
	 * 	用途：使用与体脂称四电极使用（俗称 小称 ）
	 *  
	 * 
	 * @return json 返回 json 格式数据
	 */
	public function userinfo(){
		$kbjson = array();
		$kbjson = I('kbjson','','trim');
		$arr 	= $kbjson ? json_decode($kbjson,true) : '';

		if ( !$arr ) {
			$msgdata['resultcode'] 	=	'801';
			$msgdata['desc'] 		=	'Parameter error';
			$msgdata['PSinformation']	=	"wrong";
			// 错误原始数据记录
			$this->errorkbjson($kbjson,'index_error_kbjson');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr 	=	$this->trimarr($arr);

		if ( isset($arr['debug']) && $arr['debug'] == 1) {
			$this->debug 	=	true;
		}


		$arr['messageType']	=	trim(strtolower($arr['messageType']));

		if ( !isset($arr['messageType']) || $arr['messageType'] != 'userinfo' ) {
			$msgdata['resultcode'] 	=	'802';
			$msgdata['desc'] 		=	'Equipment not in the detection state';
			$msgdata['PSinformation']	=	"wrong";
			// 错误原始数据记录
			$this->errorkbjson($kbjson,'userinfo_error_messageType');

			$this->errrajaxreturn($msgdata);
			exit;
		}
		
		if ( !isset($arr['personID']) || !$arr['personID']  ) {
			$msgdata['resultcode'] 	=	'803';
			$msgdata['desc'] 		=	'Card number data is empty';
			$msgdata['PSinformation']	=	"wrong";
			// 错误原始数据记录
			$this->errorkbjson($kbjson,'userinfo_error_personID');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		$arr['personID']	=	trim($arr['personID']);
		$user = $this->cardstatus($arr['personID']);

		if ( !$user ) {
			$msgdata['resultcode'] 	=	'804';
			$msgdata['desc'] 		=	'Users do not exist';
			$msgdata['PSinformation']	=	"wrong";

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'userinfo_error_user');
			
			$this->debug 	=	true;
			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 查询配置信息
		$check	=	M('equipment')->where(array('gateUUID'=>$arr['gateUUID'],'status'=>99))->order('sign desc')->find();
		if ( !$check ) {
			$msgdata['resultcode'] 		= 	'806';
			$msgdata['desc'] 			=	'No query to device information';
			$msgdata['PSinformation']	=	"wrong";

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'userinfo_error_equipment');

			$this->errrajaxreturn($msgdata);
			exit;
		}

		// 保存请求log信息
		$log 	=	array();
		$log['addtime']		=	date('Y-m-d H:i:s',time());
		$log['sign']		=	'userinfo';
		$log['data']		=	$kbjson;
		$log['gateuuid']	=	$arr['gateUUID'];
		$log['equstatus']	=	$info['sign'];
		M('equipment_log')->add($log);


		// 获取用户基本信息
		$userinfo 	=	$this->getuserinfo($user['userid']);

		// 处理返回数据
		$result 	=	array();
		$result['resultcode']	=	"0";
		$result['desc']			=	"success";

		// 设置秤体端重量单位,0x01:kg;0x02:lb,国内版暂只支持 kg;
		$set1 					=	1;
		// 用来设置秤体端结果显示关屏时间,0.5S 为计数单位。最小值 10,最大值 20(即 5 - 10S);
		$set2 					=	10;
		// 用户的身高参数,单位 cm,分辨度 1cm,可设置范围 80-220cm;
		$set3 					=	intval($userinfo['height']) ;	
		// 用户的年龄参数,分辨度 1 岁,可设置范围 18-80 岁;
		$set4 					=	intval($userinfo['age']);		
		
		// 性别 用户的性别参数,男--0x00,女--0x01;
		if ( $userinfo['gender'] == 2 ) {
			$set5		=	1;
		}else{
			$set5		=	0;
		}

		// 身高条件范围为：80-220 cm ， 如果不在该范围内，返回 0 
		if ($set3 < 80 || $set3 > 220 ) {
			$msgdata['resultcode'] 		= 	'807';
			$msgdata['desc'] 			=	'height wrong';
			$msgdata['PSinformation']	=	"wrong";
			// 错误原始数据记录
			$this->errorkbjson($kbjson,'userinfo_error_height');

			$this->errrajaxreturn($msgdata);
			exit;
		}
		// 年龄条件范围：18-80 岁 ，如果不在该范围内，返回 0
		if ($set4 < 18 || $set4 > 80 ) {
			$msgdata['resultcode'] 		= 	'808';
			$msgdata['desc'] 			=	'age wrong';
			$msgdata['PSinformation']	=	"wrong";

			// 错误原始数据记录
			$this->errorkbjson($kbjson,'userinfo_error_age');

			$this->errrajaxreturn($msgdata);
			exit;
		}


		// 计算校验和
		$set6 					=	$set1 + $set2 + $set3 + $set4 + $set5 ;
		// 判断数据位数是否为一位的，如果为一位，左侧进行补0操作
		$set1 	=	sprintf("%02X",$set1);
		if ( strlen($set1) < 2 ) {
			$set1 	=	'0'.$set1;
		}
		
		$set2 	=	sprintf("%02X",$set2); 
		if ( strlen($set2) < 2 ) {
			$set2 	=	'0'.$set2;  
		}
		
		
		$set3 	=	sprintf("%02X",$set3); 
		if ( strlen($set3) < 2 ) {
			 $set3 	=	'0'.$set3; 
		}

		$set4 	=	sprintf("%02X",$set4);  
		if ( strlen($set4) < 2 ) {
			$set4 	=	'0'.$set4; 
		}

		$set5 	=	sprintf("%02X",$set5);
		if ( strlen($set5) < 2 ) {
			$set5 	=	'0'.$set5;
		}

		$set6 	=	sprintf("%02X",$set6);
		if ( strlen($set6) < 2 ) {
			$set6 	=	'0'.$set6;  
		}elseif( strlen($set6) > 2 ){
			$set6 	=	substr($set6,-2);
		}

		// 数据通过16进制进行传递
		$result['PSinformation']=	$set1.'-'.$set2.'-'.$set3.'-'.$set4.'-'.$set5.'-'.$set6;
		$this->ajaxReturn($result);
		exit;
	}


	/**
	 * 获取用户基本信息
	 *  提供体脂秤基本信息
	 * @param  integer $userid 用户ID
	 * @return 
	 */
	private function getuserinfo( $userid ){
		if( !$userid || !is_numeric($userid) ){
			return false;
		}

		$userid	=	intval($userid);
		$info 	= 	array();
		$Model 	= 	M('member_detail');
		$info 	= 	$Model->field('birthday,sex,height')->where('userid = '.$userid )->find();

		if ( !$info || !$info['birthday'] || !$info['height']) {
			return false;
		}

		// 计算年龄
		$result 	=	array();
		$result['age']	=	$this->age($info['birthday']);
		switch ($info['sex']) {
			case '0':
				$result['gender']	=	1;
				break;
			case '1':
				$result['gender']	=	2;
				break;
			default:
				$result['gender']	=	1;
				break;
		}
		$result['height']	=	$info['height'];

		return $result;
	}

	/**
	 * 计算年龄
	 * @param  [type] $birthday [description]
	 * @return [type]      [description]
	 */
	private function age($birthday){
		$age	=	0; 
		$year	=	$month	=	$day	=	0; 
		if ( is_array($birthday) ) { 
			extract($birthday); 
		} else { 
			if ( strpos($birthday, '-') !== false ) { 
				list($year, $month, $day) = explode('-', $birthday); 
				$day	=	substr($day, 0, 2);
			} 
		} 
		
		$age = date('Y') - $year; 
		
		if (date('m') < $month || (date('m') == $month && date('d') < $day)) {
			$age--; 
		}

		return $age; 
	}

	
	/**
	 * 获取最后一次检测时间,在一天内更新检测时间
	 * 大于一天重新插入
	 * 
	 * @param number $userid 用户id
	 * @param string $time 检测时间 */
	private function updatetime($userid=0,$time='',$cardid = ''){
		$tmp_arr = explode(' ',$time);
		if($tmp_arr['0']){
			$time1 = strtotime($tmp_arr['0'])-86400;
			$arr = $data =array();
			$tmptime = strtotime($tmp_arr['0']);
			$arr = M('kangbao_jiance')
					->where('userid='.$userid.' AND checktime>='.$tmptime.'')
					->order('checktime desc')
					->find();
			if(!$arr){
				$data['checktime'] = strtotime($tmp_arr['0']);
				$data['userid'] = $userid;
				$data['cardid'] = $cardid;
				M('kangbao_jiance')->add($data);
			}
		}
	}
	
	/**
	 * 去除数组中键值和数据空格
	 * @return [type] [description]
	 */
	private function trimarr($data){
		if ( !$data || !is_array($data) ) {
			return false;
		}

		$result 	=	array();
		foreach ($data as $key => $value) {
			$result[trim($key)]	=	trim($value);

			if (  trim(strtolower($key)) == 'gateuuid' ) {
				$result[trim($key)]	=	trim(str_replace(':', '-', $value));				
			}
		}

		return $result;
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
	 * 检测数据添加到队列库中
	 * 
	 * @param  array  $data 
	 * @return bool true：成功 false：失败
	 */
	private function addqueue( $data = array() ){
		if ( !$data ) {
			return false;
		}

		$result 	=	array();
		$result['userid']	=	$data['userid'];
		$result['type']		=	$data['type'];
		$result['insertid']	=	$data['insertid'];
		$result['extime']	=	$data['extime'] ? strtotime($data['extime']) : '0';
		$result['cardid']	=	$this->cardid ? $this->cardid : '';
		$result['drugid']	=	$this->drugid ? $this->drugid : '';
		$result['sn']		=	$this->sn ? $this->sn : '';
		$result['gate']		=	$this->gate ? $this->gate : '';
		$result['cardtype'] =	$this->cardtype ? $this->cardtype : '';

		// 进程ID记录（ 记录的为pf_examstate_history 表 ID 用来区分数据为哪次进行的体检）
		$examstatusid 		=	S("kangbao_examstatus".$this->cardid);
		$examstatusid 		=	$examstatusid ? $examstatusid : 0;
		$result['examstatusid']	=	$examstatusid;

		$insertid			=	M('kangbao_queue')->add($result);

		return $insertid ? true : false;
	}


	/**
	 * 生成单项检测报告编码
	 * @param  integer $drugid 药店id
	 * @param  integer $userid 患者id
	 * @param  string  $exam     编码前缀
	 * @return string  返回编码
	 */
	private function createcode( $drugid,$userid,$exam = 'gl' ){
		$report		=	new \Common\Common\reportredis();
		$precode	=	$report->singlecode($exam);

		$code 		=	$report->createonecode($drugid,$userid,$precode);

		return $code;
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

	/**
	 * 有问题的数据返回
	 *  如果传递的参数中含有 debug 字段值，
	 *  并且debug字段值等于 1 时返回出错代码，否则全部返回成功状态
	 *  
	 * @param array $data 错误信息
	 * @author wangyangyang
	 * @version V1.0
	 */
	private function errrajaxreturn($data){
		if ( $this->debug  ) {
			echo $this->ajaxReturn($data);
			exit;
		}else{
			$msgdata				=	array();
			$msgdata['desc'] 		=	'success';
			$msgdata['resultcode'] 	=	$this->successcode; 
			echo $this->ajaxReturn($msgdata);
			exit;
		}
	}


	/**
	 * 查看最新版本
	 *
	 * @param integer $version 当前版本
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 
	 */
	private function getupdate( $version = 0 ){
		// 查询该网关是否允许升级
		$version 	=	$version ? intval($version) : 0;

		$where 	=	array();
		$where['id']	=	array('gt',$version);
		$info 	=	M('equpdate')->where($where)->limit(1)->find();

		return $info ? $info : false;

	}

}