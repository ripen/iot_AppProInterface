<?php
namespace Common\Analysisclass;


/**
 * redis获取分析结果数据
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class dataredis implements ifactory{
	
	public $redis;

	public function __construct(){
		$this->redis 	=	new \Common\Common\phpredis();
	}

	/**
	 * 血糖
	 * 
	 * @param  string $gl       血糖测量值
	 * @param  string  $times   进食状态
	 * @param  string  $history 病史
	 * @return array   返回分析结果原始数据
	 */
	public function bsugar( $gl = '0' , $times = '3' , $history = '2' ){
		// 获取区间
		$key 	=	'anslysis:range:gl';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}
		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ($gl >= $value['min'] && $gl <= $value['max'] && $times == $value['times'] && $history == $value['history'] ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}
		// 获取结果
		$rkey	=	'anslysis:data:gl:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();
	}
	
	/**
	 * 血脂
	 * 
	 * @param  string $tch 总胆固醇
	 * @param  string $tg  甘油三酯（三酰甘油）
	 * @param  string $ldl 低密度脂蛋白胆固醇
	 * @param  string $hdl 高密度脂蛋白胆固醇
	 * @return array   返回分析结果原始数据
	 */
	public function bloodfat( $tch = '' , $tg = '', $ldl = '', $hdl = '' ){
		// 获取区间
		$key 	=	'anslysis:range:bf';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}

		$result	=	array();
		$rkey 	=	'';
		// 判断范围，获取分析数据 总胆固醇
		if ( $tch && isset( $range['1']) ) {
			foreach ($range['1'] as $key => $value) {
				if ( $tch >= $value['min'] && $tch <= $value['max'] ) {
					$rkey	=	'anslysis:data:bf:'.$key;
					$result[1]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 甘油三酯（三酰甘油）
		if ( $tg && isset( $range['2']) ) {
			foreach ($range['2'] as $key => $value) {
				if ( $tg >= $value['min'] && $tg <= $value['max'] ) {
					$rkey	=	'anslysis:data:bf:'.$key;
					$result[2]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 低密度脂蛋白胆固醇
		if ( $ldl && isset( $range['3']) ) {
			foreach ($range['3'] as $key => $value) {
				if ( $ldl >= $value['min'] && $ldl <= $value['max'] ) {
					$rkey	=	'anslysis:data:bf:'.$key;
					$result[3]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 高密度脂蛋白胆固醇
		if ( $hdl && isset( $range['4']) ) {
			foreach ($range['4'] as $key => $value) {
				if ( $hdl >= $value['min'] && $hdl <= $value['max'] ) {
					$rkey	=	'anslysis:data:bf:'.$key;
					$result[4]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}

		return $result ? $result : array();
	}

	/**
	 * 血压
	 * 
	 * @param  string $hdata  高压
	 * @param  string $ldata  低压
	 * @return array
	 */
	public function bloodp( $hdata = '' , $ldata = '' ){
		// 获取区间
		$key 	=	'anslysis:range:bp';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}
		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ( $hdata >= $value['hmin'] && $hdata <= $value['hmax'] &&  
				$ldata >= $value['lmin'] && $ldata <= $value['lmax'] ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}
		// 获取结果
		$rkey	=	'anslysis:data:bp:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();
	}
	
	/**
	 * 血氧
	 *
	 * @param string $ox 血氧饱和度
	 * @param string $bpm 脉率
	 * @return [type] [description]
	 */
	public function oxygen( $ox = '' , $bpm = ''){
		// 获取区间
		$key 	=	'anslysis:range:ox';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}

		$result	=	array();
		$rkey 	=	'';
		// 判断范围，获取分析数据 血氧饱和度
		if ( $ox && isset( $range['1']) ) {
			foreach ($range['1'] as $key => $value) {
				if ( $ox >= $value['min'] && $ox <= $value['max'] ) {
					$rkey	=	'anslysis:data:ox:'.$key;
					$result[1]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 脉率
		if ( $bpm && isset( $range['2']) ) {
			foreach ($range['2'] as $key => $value) {
				if ( $bpm >= $value['min'] && $bpm <= $value['max'] ) {
					$rkey	=	'anslysis:data:ox:'.$key;
					$result[2]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}

		return $result ? $result : array();
	}
	

	/**
	 * 恒温
	 *
	 * @param  string $tm  恒温值
	 * @return [type] [description]
	 */
	public function tm( $tm = '' ){
		// 获取区间
		$key 	=	'anslysis:range:tm';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}
		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ( $tm >= $value['min'] && $tm <= $value['max'] ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}
		// 获取结果
		$rkey	=	'anslysis:data:tm:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();
	}
	
	/**
	 * 心电
	 *
	 * @param  string $bpm  心率值
	 * @return [type] [description]
	 */
	public function el( $bpm = '' ){
		// 获取区间
		$key 	=	'anslysis:range:el';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}
		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ( $bpm >= $value['min'] && $bpm <= $value['max'] ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}
		// 获取结果
		$rkey	=	'anslysis:data:el:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();
	}

	/**
	 * 尿常规
	 * 
	 * @param  string $urobilinogen 尿胆原
	 * @param  string $nitrite      亚硝酸盐
	 * @param  string $whitecells   白细胞
	 * @param  string $redcells     潜血、红细胞
	 * @param  string $urineprotein 尿蛋白
	 * @param  string $ph           酸碱度
	 * @param  string $urine        尿比重
	 * @param  string $urineketone  尿酮
	 * @param  string $bili         胆红素
	 * @param  string $sugar        尿糖
	 * @param  string $vc           维生素C
	 * 
	 */
	public function urine( $urobilinogen = '' , $nitrite = '' , $whitecells = '' , $redcells = '' , $urineprotein = '' , $ph = '' , $urine = '' , 
		$urineketone = '' , $bili = '' , $sugar = '' , $vc = '' ){

		// 获取区间
		$key 	=	'anslysis:range:ur';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}

		$result	=	array();
		$rkey 	=	'';
		// 判断范围，获取分析数据 尿胆原
		if ( $urobilinogen && isset( $range['2']) ) {
			$urobilinogen	=	$urobilinogen == '-' ? 1 : 2;

			foreach ($range['2'] as $key => $value) {
				if ( $value['division'] == $urobilinogen ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[2]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 亚硝酸盐
		if ( $nitrite && isset( $range['1']) ) {
			$nitrite	=	$nitrite == '-' ? 1 : 2;
			foreach ($range['1'] as $key => $value) {
				if ( $value['division'] == $nitrite ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[1]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 白细胞
		if ( $whitecells && isset( $range['3']) ) {
			$whitecells	=	$whitecells == '-' ? 1 : 2;
			foreach ($range['3'] as $key => $value) {
				if ( $value['division'] == $whitecells ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[3]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 潜血
		if ( $redcells && isset( $range['4']) ) {
			$redcells	=	$redcells == '-' ? 1 : 2;
			foreach ($range['4'] as $key => $value) {
				if ( $value['division'] == $redcells ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[4]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 尿蛋白
		if ( $urineprotein && isset( $range['5']) ) {
			$urineprotein	=	$urineprotein == '-' ? 1 : 2;
			foreach ($range['5'] as $key => $value) {
				if ( $value['division'] == $urineprotein ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[5]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 酸碱度
		if ( $ph && isset( $range['6']) ) {
			foreach ($range['6'] as $key => $value) {
				if ( $ph >= $value['min'] && $ph <= $value['max'] ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[6]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 尿比重
		if ( $urine && isset( $range['7']) ) {
			foreach ($range['7'] as $key => $value) {
				if ( $urine >= $value['min'] && $urine <= $value['max'] ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[7]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 尿酮
		if ( $urineketone && isset( $range['8']) ) {
			$urineketone	=	$urineketone == '-' ? 1 : 2;
			foreach ($range['8'] as $key => $value) {
				if ( $value['division'] == $urineketone ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[8]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 胆红素
		if ( $bili && isset( $range['9']) ) {
			$bili	=	$bili == '-' ? 1 : 2;
			foreach ($range['9'] as $key => $value) {
				if ( $value['division'] == $bili ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[9]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 尿糖
		if ( $sugar && isset( $range['10']) ) {
			$sugar	=	$sugar == '-' ? 1 : 2;
			foreach ($range['10'] as $key => $value) {
				if ( $value['division'] == $sugar ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[10]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 维生素C
		if ( $vc && isset( $range['11']) ) {
			$vc	=	$vc == '-' ? 1 : 2;
			foreach ($range['11'] as $key => $value) {
				if ( $value['division'] == $vc ) {
					$rkey	=	'anslysis:data:ur:'.$key;
					$result[11]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}

		return $result ? $result : array();

	}

	/**
	 * 体成分
	 * 
	 * @param  string $weight           体重
	 * @param  string $bmi              BMI 
	 * @param  string $bf               体脂率（％）
	 * @param  string $fatweight        去脂体重（kg）
	 * @param  string $protein          内脏脂肪指数（kg）
	 * @param  string $watercontentrate 身体总水分率（％）
	 * @param  string $muscle           肌肉量（kg）
	 * @param  string $mineralsalts     骨量
	 * @param  string $fat              基础代谢率
	 * @param  string $sex 				性别( 0 ：男 1：女)
	 * 
	 */
	public function humanbody( $weight = '', $bmi = '' , $bf = '', $fatweight = '', $protein = '', $watercontentrate = '', $muscle = '', $mineralsalts = '', $fat= '' , $sex = '' ){
		// 获取区间
		$key 	=	'anslysis:range:we';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}

		$result	=	array();
		$rkey 	=	'';
		// 判断范围，获取分析数据 BMI
		if ( $bmi && isset( $range['2']) ) {
			foreach ($range['2'] as $key => $value) {
				if ( $bmi >= $value['min'] && $bmi <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[2]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}

		// 判断范围，获取分析数据 体脂率(区分性别)
		$sex 	=	$sex ? intval($sex) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;
		if ( $bf && isset( $range['3']) ) {
			foreach ($range['3'] as $key => $value) {
				if ( $bf >= $value['min'] && $bf <= $value['max'] && $value['sex'] == $sex ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[3]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 去脂体重
		if ( $fatweight && isset( $range['4']) ) {
			foreach ($range['4'] as $key => $value) {
				if ( $fatweight >= $value['min'] && $fatweight <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[4]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 内脏脂肪指数（kg）
		if ( $protein && isset( $range['5']) ) {
			foreach ($range['5'] as $key => $value) {
				if ( $protein >= $value['min'] && $protein <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[5]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 身体总水分率
		if ( $watercontentrate && isset( $range['6']) ) {
			foreach ($range['6'] as $key => $value) {
				if ( $watercontentrate >= $value['min'] && $watercontentrate <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[6]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 肌肉量
		if ( $muscle && isset( $range['7']) ) {
			foreach ($range['7'] as $key => $value) {
				if ( $muscle >= $value['min'] && $muscle <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[7]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 骨量
		if ( $mineralsalts && isset( $range['8']) ) {
			foreach ($range['8'] as $key => $value) {
				if ( $mineralsalts >= $value['min'] && $mineralsalts <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[8]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		// 判断范围，获取分析数据 基础代谢率
		if ( $fat && isset( $range['9']) ) {
			foreach ($range['9'] as $key => $value) {
				if ( $fat >= $value['min'] && $fat <= $value['max'] ) {
					$rkey	=	'anslysis:data:we:'.$key;
					$result[9]	=	$this->redis->formatdataget($rkey);
					break;
				}
			}
		}
		return $result ? $result : array();
	}


	/**
	 * 血酮
	 * 
	 * @param  string  $bk       血酮测量值
	 * @return array   返回分析结果原始数据
	 */
	public function bloodketone( $bk = '' ){
		// 获取区间
		$key 	=	'anslysis:range:bk';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}
		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ($bk >= $value['min'] && $bk <= $value['max'] ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}
		// 获取结果
		$rkey	=	'anslysis:data:bk:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();
	}



	/**
	 * 血尿酸
	 * 
	 * @param  string $suricacid  血尿酸
	 * @param  string $sex  性别
	 * @return array   返回分析结果原始数据
	 */
	public function renal( $suricacid = '' , $sex = '' ){
		// 获取区间
		$key 	=	'anslysis:range:re';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}

		$result	=	array();
		$rkey 	=	'';
		// 判断范围，获取分析数据 血尿酸
		$sex 	=	$sex ? intval($sex) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;

		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ($suricacid >= $value['min'] && $suricacid <= $value['max'] && $value['sex'] == $sex ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}

		// 获取结果
		$rkey	=	'anslysis:data:re:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();

	}


	/**
	 * 血尿酸μmol/L
	 * 
	 * @param  string $suricacid  血尿酸
	 * @param  string $sex  性别
	 * @return array   返回分析结果原始数据
	 */
	public function renalnew( $suricacid = '' , $sex = '' ){
		// 获取区间
		$key 	=	'anslysis:range:renew';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}

		$result	=	array();
		$rkey 	=	'';
		// 判断范围，获取分析数据 血尿酸
		$sex 	=	$sex ? intval($sex) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;

		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ($suricacid >= $value['min'] && $suricacid <= $value['max'] && $value['sex'] == $sex ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}

		// 获取结果
		$rkey	=	'anslysis:data:renew:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();

	}


	/**
	 * 尿微量白蛋白
	 * 
	 * @param  string  $umprotein      尿微量白蛋白测量值
	 * @return array   返回分析结果原始数据
	 */
	public function umprotein( $umprotein = '' ){
		// 获取区间
		$key 	=	'anslysis:range:um';
		$range 	=	$this->redis->formatdataget($key);

		if ( !$range ) {
			return false;
		}
		// 判断范围，获取分析数据
		$datakey 	=	'';
		foreach ($range as $key => $value) {
			if ($umprotein >= $value['min'] && $umprotein <= $value['max'] ) {
				$datakey	=	$key;
				break;
			}
		}
		if ( !$datakey ) {
			return false;
		}
		// 获取结果
		$rkey	=	'anslysis:data:um:'.$datakey;
		$info 	=	$this->redis->formatdataget($rkey);

		return $info ? $info : array();
	}

	
	/**
	 * 整体大项正常返回结果
	 * @param  string $datas 设备类型 (一次获取多个，中间用英文逗号拆分)
	 *          1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温	
	 * @return [type]        [description]
	 */
	public function resources($datas = ''){
		if ( !$datas ) {
			return false;
		}
		$types	=	explode(',',$datas);
		$types 	=	array_filter($types);

		if ( !$types ) {
			return false;
		}

		foreach ($types as $key => $value) {
			$rkey	=	'anslysis:resources:'.$value;
			$result[$value]	=	$this->redis->formatdataget($rkey);
		}
		$result	=	array_filter($result);

		return $result;
	}
	
}
