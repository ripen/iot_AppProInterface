<?php
/**
 * 设备原始数据处理model
 *  
 * @author wangyangyang
 *
 */
namespace Kbox\Model;
use Think\Model;
class OriginalModel extends Model {
	
	/**
	 * 血压原始数据处理
	 * 	{"messageType":"exam","personID":"000001","devicename":"bp","RAWDATA":"FF010100000001000C80864D420D4100046D404B412003FE","examtime":"2016-01-04 16:31:07","deviceUUID":"5C-B6-CC-02-5A-17","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 *
	 * 
	 * @param string $RAWDATA 血压原始数据
	 * @return 
	 */
	public function bp( $RAWDATA = '' ){
		if ( !$RAWDATA ) {
			return false;
		}

		$result		=	array();
		$RAWDATA	=	trim(strtolower($RAWDATA));

		// 低压值
		$BPL 		=	substr($RAWDATA,20,2);
		$result['BPL']	=	$BPL ? hexdec($BPL) : '';

		// 高压值
		$BPH 			=	substr($RAWDATA,22,2);
		$result['BPH']	=	$BPH ? hexdec($BPH) : '';

		// 心率
		$HBR 			=	substr($RAWDATA,24,2);
		$result['HBR']	=	$HBR ? hexdec($HBR) : '';

		return $result;
	}
	
	/**
	 * 血脂原始数据处理
	 * 	{"messageType":"exam","personID":"000001","devicename":"BF","RAWDATA":"323031352D30332D323028592D4D2D44292031323A34300D0A49443A206231310D0A43484F4C3A203C322E35396D6D6F6C2F4C0D0A48444C3A20312E31346D6D6F6C2F4C0D0A545249473A20302E35356D6D6F6C2F4C0D0A43484F4C2F48444C3A204E410D0A4C444C3A203C312E32396D6D6F6C2F4C0D0A0D0A0D0A","examtime":"2016-01-04 16:31:07","deviceUUID":"5C-B6-CC-02-5A-17","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * {"messageType":"exam","personID":"000001","devicename":"BF","RAWDATA":"2016-01-06(Y-M-D) 16:13\r\nID: b07\r\nCHOL: <2.59mmol/L\r\nHDL:<0.39mmol/L\r\nTRIG: <0.51mmol/L\r\nCHOL/HDL: NA\r\nLDL: <1.29mmol/L\r\n\r\n\r\n","examtime":"2015-12-10 10:01:21","deviceUUID":"5C-B6-CC-00-33-FD","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * 
	 * @param string $RAWDATA 血脂原始数据
	 * @return [type] [description]
	 */
	public function bf( $RAWDATA = '' ){
		if ( !$RAWDATA ) {
			return false;
		}

		$RAWDATA	=	trim(strtolower($RAWDATA));

		/*
		
		//	原始数据16进制数据处理
		$array 		=	explode('0d0a',$RAWDATA);
		$array 		=	array_filter($array);
		$RAresult 	=	array();
		if ( $array ) {
			foreach ($array as $key => $value) {
				$templength	=	strlen($value);
				
				$tempdata	=	'';
				for($i = 0 ; $i < $templength ;$i = $i + 2){
					$tempdata	.=	self::hex2asc(substr($value,$i,2));
				}
				$RAresult[]	=	$tempdata;
			}
		}
		$RAresult[2]	=	$RAresult[2] ? preg_replace('/[^0-9.]/','',$RAresult[2]) : '';
		$RAresult[3]	=	$RAresult[3] ? preg_replace('/[^0-9.]/','',$RAresult[3]) : '';
		$RAresult[4]	=	$RAresult[4] ? preg_replace('/[^0-9.]/','',$RAresult[4]) : '';
		$RAresult[5]	=	$RAresult[5] ? preg_replace('/[^0-9.]/','',$RAresult[5]) : '';
		$RAresult[6]	=	$RAresult[6] ? preg_replace('/[^0-9.]/','',$RAresult[6]) : '';
		*/
	

		$array 		=	explode("\r\n",$RAWDATA);
		$array 		=	array_filter($array);
		$RAresult 	=	array();
		
		$RAresult[2]	=	$array[2] ? preg_replace('/[^0-9.]/','',$array[2]) : '';
		$RAresult[3]	=	$array[3] ? preg_replace('/[^0-9.]/','',$array[3]) : '';
		$RAresult[4]	=	$array[4] ? preg_replace('/[^0-9.]/','',$array[4]) : '';
		$RAresult[5]	=	$array[5] ? preg_replace('/[^0-9.]/','',$array[5]) : '';
		$RAresult[6]	=	$array[6] ? preg_replace('/[^0-9.]/','',$array[6]) : '';

		return $RAresult;
	}

	/**
	 * 血脂 16进制数据转 asc 码
	 * @param  [type] $str 
	 */
	private function hex2asc($str) {
		$data	=	'';
		$str 	= 	join('',explode('\x',$str));  
		$len 	= 	strlen($str);  
		for ( $i = 0 ; $i < $len ; $i += 2 ) {
			$data.= chr(hexdec(substr($str,$i,2)));  
		}
		return $data;  
	} 



	/**
	 * 血糖原始数据处理
	 * 	{"messageType":"exam","personID":"000001", "devicename":"GL", "RAWDATA": "060100df07041e12230035f011", "examtime":"2015-09-01 15:09:09", "deviceUUID": "C0:15:91:A1:90:8A", "gateUUID" : "B0-A1-11-31-A1-81", "gateChannel": "WIFI", "gateLocation": "23.12-45.12", "gateVersion": "2.1"}
	 *
	 * 
	 * @param string $RAWDATA 血糖原始数据
	 * @return [type] [description]
	 */
	public function gl( $RAWDATA = ''){
		if ( !$RAWDATA ) {
			return false;
		}
		$GLO	=	substr($RAWDATA,20,2);
		$GLO	=	$GLO ? hexdec($GLO) : '';
		
		$GLO 	=	$GLO ? $GLO/10.0 : '';
		return $GLO;
	}


	/**
	 * 尿11检测原始数据处理
	 * 	{"messageType":"exam","personID":"000001","devicename":"ur","RAWDATA":"938E100008040003FFFF1090C23100004006F6","examtime":"2016-01-04 16:31:07","deviceUUID":"5C-B6-CC-02-5A-17","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * 
	 * @param string $RAWDATA 尿11检测原始数据
	 * @return [type] [description]
	 */
	public function ur($RAWDATA = ''){
		if ( !$RAWDATA ) {
			return false;
		}

		// 原始数据处理
		$RAWDATA 		=	substr($RAWDATA,12,24);

		if ( $RAWDATA ) {
			$convertdata	=	'';
			for($i = 0 ; $i < 24 ;$i = $i + 2){
				$tempdata	=	base_convert(substr($RAWDATA,$i,2),16,2);
				$convertdata	.=	$tempdata ? str_pad($tempdata,8,"0",STR_PAD_LEFT) : '00000000';
			}
		}

		$result 	=	array();
		if ( $convertdata ) {
			$leu 	=	substr($convertdata,50,3);
			$bld 	=	substr($convertdata,65,3);
			$ph 	=	substr($convertdata,68,3);
			$pro 	=	substr($convertdata,71,3);
			$ubg 	=	substr($convertdata,74,3);
			$nit 	=	substr($convertdata,77,3);
			$vc 	=	substr($convertdata,81,3);
			$glu 	=	substr($convertdata,84,3);
			$bil 	=	substr($convertdata,87,3);
			$ket 	=	substr($convertdata,90,3);
			$sg 	=	substr($convertdata,93,3);

			$sg 	=	$sg ? bindec($sg) : '';
			$ket 	=	$ket ? bindec($ket) : '';
			$bil 	=	$bil ? bindec($bil) : '';
			$glu 	=	$glu ? bindec($glu) : '';
			$vc 	=	$vc ? bindec($vc) : '';
			$nit 	=	$nit ? bindec($nit) : '';
			$ubg 	=	$ubg ? bindec($ubg) : '';
			$pro 	=	$pro ? bindec($pro) : '';
			$ph 	=	$ph ? bindec($ph) : '';
			$bld 	=	$bld ? bindec($bld) : '';
			$leu 	=	$leu ? bindec($leu) : '';

			$leuarr	=	array('0'=>'-','1'=>'+-','2'=>'+1','3'=>'+2','4'=>'+3');
			$nitarr	=	array('0'=>'-','1'=>'+');
			$ubgarr	=	array('0'=>'-','1'=>'+1','2'=>'+2','3'=>'+3');
			$proarr	=	array('0'=>'-','1'=>'+-','2'=>'+1','3'=>'+2','4'=>'+3','5'=>'+4');
			$pharr	=	array('0'=>'5.0','1'=>'6.0','2'=>'6.5','3'=>'7.0','4'=>'7.5','5'=>'8.0','6'=>'8.5');
			$bldarr	=	array('0'=>'-','1'=>'+-','2'=>'+1','3'=>'+2','4'=>'+3');
			            
			$sgarr	=	array('0'=>'1.000 ','1'=>'1.005','2'=>'1.010','3'=>'1.015','4'=>'1.020','5'=>'1.025','6'=>'1.030');
			$ketarr	=	array('0'=>'-','1'=>'+-','2'=>'+1','3'=>'+2','4'=>'+3','5'=>'+4');
			$bilarr	=	array('0'=>'-','1'=>'+1','2'=>'+2','3'=>'+3');
			$gluarr	=	array('0'=>'-','1'=>'+-','2'=>'+1','3'=>'+2','4'=>'+3','5'=>'+4');
			$vcarr	=	array('0'=>'-','1'=>'+-','2'=>'+1','3'=>'+2','4'=>'+3');


			$result['sg'] 	=	isset($sgarr[$sg]) ? $sgarr[$sg] : '';
			$result['ket'] 	=	isset($ketarr[$ket]) ? $ketarr[$ket] : '';
			$result['bil'] 	=	isset($bilarr[$bil]) ? $bilarr[$bil] : '';
			$result['glu'] 	=	isset($gluarr[$glu]) ? $gluarr[$glu] : '';
			$result['vc'] 	=	isset($vcarr[$vc]) ? $vcarr[$vc] : '';
			$result['nit'] 	=	isset($nitarr[$nit]) ? $nitarr[$nit] : '';
			$result['ubg'] 	=	isset($ubgarr[$ubg]) ? $ubgarr[$ubg] : '';
			$result['pro'] 	=	isset($proarr[$pro]) ? $proarr[$pro] : '';
			$result['ph'] 	=	isset($pharr[$ph]) ? $pharr[$ph] : '';
			$result['bld'] 	=	isset($bldarr[$bld]) ? $bldarr[$bld] : '';
			$result['leu'] 	=	isset($leuarr[$leu]) ? $leuarr[$leu] : '';
		}

		return $result;
	}


	/**
	 * 血氧原始数据处理
	 * 	{"messageType":"exam","personID":"000001", "devicename":"OX", "RAWDATA": "55aa036158bc", "examtime":"2015-09-01 15:09:09", "deviceUUID": "C0:15:91:A1:90:8A", "gateUUID" : "B0-A1-11-31-A1-81", "gateChannel": "WIFI", "gateLocation": "23.12-45.12", "gateVersion": "2.1"}
	 *
	 * 
	 * @param string $RAWDATA 血氧原始数据
	 * @return [type] [description]
	 */
	public function ox( $RAWDATA = ''){
		if (!$RAWDATA ) {
			return false;
		}

		// 原始数据处理-----血氧
		$str 		=	substr($RAWDATA,6,2);
		$data['saturation']	=	$str ? hexdec($str) : '';
		// 原始数据处理-----脉率
		$OXV		=	substr($RAWDATA,8,2);
		$data['pr']		=	$OXV ? hexdec($OXV) : '';
		return $data;
	}


	/**
	 * 恒温原始数据处理
	 * 	{"messageType":"exam","personID":"000001", "devicename":"TM", "RAWDATA": "AA55740801023033363243C4", "examtime":"2015-09-01 15:09:09", "deviceUUID": "C0:15:91:A1:90:8A", "gateUUID" : "B0-A1-11-31-A1-81", "gateChannel": "WIFI", "gateLocation": "23.12-45.12", "gateVersion": "2.1"}
	 *
	 *  AA 55 74 08 01 02 30 33 36 32 43 C4  36.2
	 * @param string $RAWDATA 恒温原始数据
	 * @return [type] [description]
	 */
	public function tm( $RAWDATA = ''){
		if (!$RAWDATA ) {
			return false;
		}

		
		// 原始数据处理-----体温
		// 55aa0230333731430f
		// $str1	=	substr($RAWDATA,9,1);
		// $str2	=	substr($RAWDATA,11,1);
		// $str3	=	substr($RAWDATA,13,1);

		// AA55740801023033363243C4
		// $str1	=	substr($RAWDATA,15,1);
		// $str2	=	substr($RAWDATA,17,1);
		// $str3	=	substr($RAWDATA,19,1);

		// 55aa0230 		333731430f
		// AA557408010230   33363243C4

		$str1	=	substr($RAWDATA,-9,1);
		$str2	=	substr($RAWDATA,-7,1);
		$str3	=	substr($RAWDATA,-5,1);


		$tmv	=	$str1.$str2.'.'.$str3;
		return $tmv;
	}


	/**
	 * 体成分原始数据处理
	 * 	{"messageType":"exam","personID":"000001","devicename":"WE","RAWDATA":"64951b08049a9999420233b39243008095436666c2419a997743cd4c79433464951b08079a999942023373a54300007a43333391429a994e43cd4c5743e8","examtime":"2015-12-07 16:49:01","deviceUUID":"C0:15:91:A1:90:8A","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"wifi","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * 
	 * @param string $RAWDATA 体成分原始数据
	 * @return [type] [description]
	 */
	public function we($RAWDATA = ''){
		if (!$RAWDATA ) {
			return false;
		}

		// 截取原始数据头两位，如果为 01 表示为小体脂称
		$types 		=	substr($RAWDATA,0,2);
		if ( $types == '01' ) {
			$info 	=	self::yolanda($RAWDATA);
			return $info;
		}

		// 体成分按照两个进行拆分处理
		$weight		=	substr($RAWDATA,10,8);	//	体重
		$valr10		=	substr($RAWDATA,20,8);	//	valr10
		$valr11		=	substr($RAWDATA,28,8);	//	valr11
		$valr12		=	substr($RAWDATA,36,8);	//	valr12
		$valr13		=	substr($RAWDATA,44,8);	//	valr13
		$valr14		=	substr($RAWDATA,52,8);	//	valr14

		$valr20		=	substr($RAWDATA,82,8);	//	valr20
		$valr21		=	substr($RAWDATA,90,8);	//	valr21
		$valr22		=	substr($RAWDATA,98,8);	//	valr22
		$valr23		=	substr($RAWDATA,106,8);	//	valr23
		$valr24		=	substr($RAWDATA,114,8);	//	valr24

		$post 		=	array();
		$post['weight']		=	$weight ? self::unpackweight($weight) : 0;
		$post['valr10']		=	$valr10 ? self::unpackweight($valr10) : 0;
		$post['valr11']		=	$valr11 ? self::unpackweight($valr11) : 0;
		$post['valr12']		=	$valr12 ? self::unpackweight($valr12) : 0;
		$post['valr13']		=	$valr13 ? self::unpackweight($valr13) : 0;
		$post['valr14']		=	$valr14 ? self::unpackweight($valr14) : 0;

		$post['valr20']		=	$valr20 ? self::unpackweight($valr20) : 0;
		$post['valr21']		=	$valr21 ? self::unpackweight($valr21) : 0;
		$post['valr22']		=	$valr22 ? self::unpackweight($valr22) : 0;
		$post['valr23']		=	$valr23 ? self::unpackweight($valr23) : 0;
		$post['valr24']		=	$valr24 ? self::unpackweight($valr24) : 0;

		return $post;
	}

	/**
	 * 小的体脂称数据分析
	 * 	"RAWDATA":"01 00 02 03 04 05 0200111011150613010000000000000000000050 0200111011150000020000000000000000000038"
	 *  01 为设备编码
	 *  00 02 03 04 05 预留字段
	 * 
	 * @param  string $RAWDATA 原始数据
	 * @return array 返回解析过之后的数据          
	 */
	public function yolanda( $RAWDATA ){
		if ( ! $RAWDATA ) {
			return false;
		}

		// 判断设备编码是否真确
		$check 	=	substr($RAWDATA,0,2);
		if ( $check != '01' ) {
			return false;
		}

		// 返回数据
		$result =	array();

		$result['yolanda']			=	1;		//	状态区分

		// 真正的原始数据
		$data 	=	substr($RAWDATA,12);

		// 判断原始数据是否正确 0200 为设备的数据包头
		$check 	=	substr($data,0,4);
		if ( $check != '0200' ) {
			return false;
		}
		
		// 秤端重量值得分辨度(与命令 0x10 里重量值换算有关系),0x00 表示 0.1kg,0x01 表示0.01kg; 
		// SD 为 0x00, 则显示重量值 = Weight /10,若收到的 SD 为 0x01,则显示重量值 = Weight /100;
		// 该点默认先按照 100 进行处理
		$SD 		=	100.0;

		// 体重计算
		$weightH 	=	substr($data, 12,2);
		$weightL 	=	substr($data,14,2);
		$weight 	=	hexdec($weightH) * 256 + hexdec($weightL);
		$weight 	=	$weight / $SD;
		$weight 	=	$weight ? number_format($weight, 1, '.', '') : 0;
		$result['weight']	=	$weight;


		// 判断数据有效性 0 :只有体重有效 1 ：全部有效
		$effective	=	substr($RAWDATA,2,2);
		$effective	=	$effective ? hexdec($effective) : 0 ;
		if ( $effective == 0 ) {

			return $result;
		}

		// 脂肪率
		$fatH 	=	substr($data,26,2);
		$fatL 	=	substr($data,28,2);
		$fat 	=	hexdec($fatH) * 256 + hexdec($fatL);
		$fat 	=	$fat ? $fat / 10.0 : 0;
		$fat 	=	$fat ? number_format($fat, 1, '.', '') : 0;

		// 水分率值
		$tbwH	=	substr($data, 30,2);
		$tbwL 	=	substr($data, 32,2);
		$tbw 	=	hexdec($tbwH) * 256 + hexdec($tbwL);
		$tbw 	=	$tbw ? $tbw / 10.0 : 0;
		$tbw 	=	$tbw ? number_format($tbw, 1, '.', '') : 0;

		//	肌肉值
		$musH 	=	substr($data, 34,2);
		$musL 	=	substr($data, 36,2);
		$mus 	=	hexdec($musH) * 256 + hexdec($musL);
		$mus 	=	$mus ? $mus / 10.0 : 0;
		$mus 	=	$mus ? number_format($mus, 1, '.', '') : 0;

		// 骨量值
		$bone 	=	substr($data,70,2);
		$bone 	=	hexdec($bone);
		$bone 	=	$bone ? $bone / 10.0 : 0;
		$bone 	=	$bone ? number_format($bone, 1, '.', '') : 0;

		$result['weight']	=	$weight;

		$result['bf']				=	$fat;	// 	脂肪率	-----》	体脂率
		$result['watercontentrate']	=	$tbw;	//	水分率值	-----》 身体总水分率
		$result['muscle']			=	$mus;	//	肌肉值   	-----》 肌肉量
		$result['mineralsalts']		=	$bone;	//	骨量    	-----》 骨量

		
		return $result;
	}

	/**
	 * 体支撑 4字节转float
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function unpackweight($data){
		if ( !$data ) {
			return false;
		}
		$binarydata32 	= 	pack('H*',$data);
		$float32 		= 	unpack("f", $binarydata32);

		return $float32 ? number_format($float32[1], 1, '.', ' ') : 0;
	}


	/**
	 * 心电原始数据处理（拼接传递过来的原始数据）
	 * {"messageType":"exam","personID":"000067","devicename":"EL","RAWDATA":"194920088a0100388a8f757681848b837c7e7f80888c7a747b7a8581797f7b898b737b807d9386777e7a848d7e7b7e7c81888d84747c898581817f7d818b8587","examtime":"2016-01-21 08:59:07","deviceUUID":"5C-B6-CC-02-5A-17","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * {"messageType":"exam","personID":"000067","devicename":"EL","RAWDATA":"194920088a0200384e02ffff6affff02012081767d848789877f7c87877980898178809183707d817d89867c7f828d867481877b8187807e89857b8881768381","examtime":"2016-01-21 08:59:09","deviceUUID":"5C-B6-CC-02-5A-17","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 *
	 * {"messageType":"exam","personID":"000067","devicename":"EL","RAWDATA":"194920088a03013885897c788e9182827b7c89817e8b857c817f7f87837b7c888f746b868b858374768c8d7b797c868f7d7580888b7b7482848889787f8f8379","examtime":"2016-01-21 08:59:11","deviceUUID":"5C-B6-CC-02-5A-17","gateUUID ":"B0:A1:11:31:A1:81","gateChannel":"WIFI","gateLocation":"23.12-45.12","gateVersion":"2.1"}
	 * 
	 * @param string $RAWDATA 心电原始数据
	 * @return [type] [description]
	 */
	public function el( $RAWDATA = ''){
		if (!$RAWDATA || strlen( $RAWDATA ) != 128 ) {
			return false;
		}

		// 获取分包序列号 原始数据序列号为16进制格式数据
		$num 			=	substr($RAWDATA,10,2);
		$num 			=	$num ? intval(hexdec($num)) : 1;
		$RAWDATAENDING	=	substr($RAWDATA,12,2);
		$RAWDATAENDING	=	$RAWDATAENDING ? intval($RAWDATAENDING) : 0;

		/*
		$length	=	strlen( $RAWDATA );
		$hexarr =	array();
		for ( $i = 16; $i < $length ; $i = $i + 2 ) { 
			$tempdata	=	hexdec(substr($RAWDATA,$i,2));
			$hexarr[]	=	$tempdata;
		}
		$hr	=	implode(' ', $hexarr);
		*/
	
		$result 	=	array();
		$result['num']	=	$num;
		$result['RAWDATAENDING']	=	$RAWDATAENDING;
		$result['origindata']		=	$RAWDATA;
		return $result;
	}

	/**
	 * 解析心电原始数据
	 * @param  string $origindata 心电原始数据
	 * @return [type]          [description]
	 */
	public function eldata( $origindata = ''){
		if ( !$origindata ) {
			return false;
		}

		$data	=	array();

		$temp	= 	explode("|",$origindata);
		$temp	= 	array_filter($temp);

		$tempar	=	array();
		foreach($temp AS $key => $val){
			$tempar		=	explode(':',$val);
			$data[$tempar[0]]	=	$tempar[1];
		}
		ksort($data);
		$str 	=	'';
		foreach($data AS $v){
			$check 	=	substr($v,30,4);
			// 修改心电解析心率判别标识 之前为 020100 
			// 具体使用中发现有个别为 020120的情况
			// 现统一修改为使用 0201 进行判断
			// 2016/11/29
			// wangyangyang
			if( $check == '0201' ){
				$bpm 	=	substr($v,36,2);
				$str 	=	substr($v,38);
			}else{
				$str	=	substr($v,16);
				$str	=	substr($str,0,strlen($str)-2);
			}
			
			$keys	=	substr($v,10,2);
			
			$keys	=	hexdec($keys);
			$result[$keys]	=	$str;
		}
		

		//	计算结果
		$hexarr =	array();
		$temp	=	array();

		foreach($result AS $k => $v){
			$length	=	strlen( $v );
			for ( $i = 0; $i < $length ; $i = $i + 2 ) { 
				$temp[]			=	substr($v,$i,2);
				$tempdata		=	hexdec(substr($v,$i,2));
				$hexarrtest[]	=	$tempdata.' '.substr($v,$i,2);
				$hexarr[]		=	$tempdata;
			}
			
		}

		$return 	=	array();
		$return['hr']	=	implode(',',$hexarr);
		$return['bpm']	=	hexdec($bpm);

		return $return;
	}
}	