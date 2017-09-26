<?php
namespace Common\Analysisclass;


/**
 * 数据库获取分析结果数据
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class datadb implements ifactory{
	
	/**
	 * 血糖
	 * 
	 * @param  string $gl       血糖测量值
	 * @param  string  $times   进食状态
	 * @param  string  $history 病史
	 * @return array   返回分析结果原始数据
	 */
	public function bsugar( $gl = '0' , $times = '3' , $history = '2' ){
		//生成文件缓存
		if(F('Analysis_bsugar')){
			$info	=	F('Analysis_bsugar');	
		}else{
	   	 	$info	=	M('analysis_bbsugar')->select();
	   	 	F('Analysis_bsugar',$info);
		}
	   	if ( !$info ) {
			return false;
		}

		$result 	=	array();
		foreach ($info as $key => $value) {
			if($value['min'] <= $gl && $value['max'] >=  $gl && $value['times'] == $times && $value['history'] == $history){
				$result 	=	$value;
				break;
			}
		}

		return $result ? $result : array();
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
		//生成缓存文件
		if(F('Analysis_bloodfat')){
			$info = F('Analysis_bloodfat');
		}else{
			$info 		= 	M('analysis_bloodfat')->select();
			F('Analysis_bloodfat',$info);
		}
		$result 	=	array();
		if ( $info ) {
			foreach ($info as $key => $value) {
				$title	=	'';
				if ($tch && $tch>= $value['min'] && $tch <= $value['max'] && $value['types'] == 1 ) {
					$title	=	'TCH 总胆固醇';
				}

				if ( $tg && $tg>= $value['min'] && $tg <= $value['max'] && $value['types'] == 2 ) {
					$title	=	'TG甘油三酯（三酰甘油）';
				}

				if ( $ldl && $ldl>= $value['min'] && $ldl <= $value['max'] && $value['types'] == 3 ) {
					$title	=	'LDL-C低密度脂蛋白胆固醇';
				}

				if ( $hdl && $hdl>= $value['min'] && $hdl <= $value['max'] && $value['types'] == 4 ) {
					$title	=	'HDL-C高密度脂蛋白胆固醇';
				}

				if ( $title ) {
					$result[$value['types']]	=	$value;
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

		//生成文件缓存
		if(F('Analysis_bloodp')){
			$info	=	F('Analysis_bloodp');	
		}else{
	   	 	$info	=	M('analysis_bloodp')->select();
	   	 	F('Analysis_bloodp',$info);
		}
	   	if ( !$info ) {
			return false;
		}

		$result 	=	array();
		foreach ($info as $key => $value) {
			if($value['hmin'] <= $hdata && $value['hmax'] >=  $hdata && $value['lmin'] <= $ldata && $value['lmax'] >=  $ldata ){
				$result 	=	$value;
				break;
			}
		}

		return $result ? $result : array();
	}
	
	
	/**
	 * 血氧
	 *
	 * @param  string $ox  血氧饱和度
	 * @param  string $bpm 脉率
	 * @return [type] [description]
	 */
	public function oxygen( $ox = '' , $bpm = '' ){
		//生成文件缓存
		if(F('Analysis_oxygen')){
			$info	=	F('Analysis_oxygen');	
		}else{
	   	 	$info	=	M('analysis_oxygen')->select();
	   	 	F('Analysis_oxygen',$info);
		}
	   	if ( !$info ) {
			return false;
		}
		$result 	=	array();
		foreach ($info as $key => $value) {
			foreach ($info as $key => $value) {
				$title	=	'';
				if ($ox && $ox>= $value['min'] && $ox <= $value['max'] && $value['types'] == 1 ) {
					$title	=	'血氧饱和度';
				}

				if ( $bpm && $bpm >= $value['min'] && $tg <= $value['max'] && $value['types'] == 2 ) {
					$title	=	'脉率';
				}

				if ( $title ) {
					$result[$value['types']]	=	$value;
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
		//生成文件缓存
		if(F('Analysis_tm')){
			$info	=	F('Analysis_tm');	
		}else{
	   	 	$info	=	M('analysis_tm')->select();
	   	 	F('Analysis_tm',$info);
		}
	   	if ( !$info ) {
			return false;
		}
		$result 	=	array();
		foreach ($info as $key => $value) {
			if($value['min'] <= $tm && $value['max'] >=  $tm ){
				$result 	=	$value;
				break;
			}
		}

		return $result ? $result : array();
	}
	

	/**
	 * 心电（心率）
	 *
	 * @param  string $bpm  心率值
	 * @return [type] [description]
	 */
	public function el( $bmp = '' ){
		//生成文件缓存
		if(F('Analysis_el')){
			$info	=	F('Analysis_el');	
		}else{
	   	 	$info	=	M('analysis_el')->select();
	   	 	F('Analysis_el',$info);
		}
	   	if ( !$info ) {
			return false;
		}
		$result 	=	array();
		foreach ($info as $key => $value) {
			if($value['min'] <= $bmp && $value['max'] >=  $bmp ){
				$result 	=	$value;
				break;
			}
		}

		return $result ? $result : array();
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

		//生成缓存文件
		if(F('Analysis_urine')){
			$info = F('Analysis_urine');
		}else{
			$info 		= 	M('analysis_urine')->select();
			F('Analysis_urine',$info);
		}

		// 数据处理 ----- 亚硝酸盐
		if ( $nitrite && $nitrite == '-') {
			$nitrite 	=	'1';
		}elseif( $nitrite ){
			$nitrite 	=	'2';
		}
		//	数据处理 ---- 尿胆原
		if ($urobilinogen && $urobilinogen == '-') {
			$urobilinogen 	=	'1';
		}elseif( $urobilinogen ){
			$urobilinogen 	=	'2';
		}	
		// 数据处理 ----- 白细胞
		if ($whitecells && $whitecells == '-') {
			$whitecells 	=	'1';
		}elseif( $whitecells ){
			$whitecells 	=	'2';
		}
		// 数据处理 ---- 潜血（红细胞）
		if ($redcells && $redcells == '-') {
			$redcells 	=	'1';
		}elseif( $redcells ){
			$redcells 	=	'2';
		}
		// 数据处理 ---- 尿蛋白
		if ($urineprotein && $urineprotein == '-') {
			$urineprotein 	=	'1';
		}elseif( $urineprotein ){
			$urineprotein 	=	'2';
		}
		// 数据处理 ==== 尿酮
		if ($urineketone && $urineketone == '-') {
			$urineketone 	=	'1';
		}elseif( $urineketone ){
			$urineketone 	=	'2';
		}
		// 数据处理 ---- 胆红素
		if ($bili && $bili == '-') {
			$bili 	=	'1';
		}elseif( $bili ){
			$bili 	=	'2';
		}
		// 数据处理 ---- 尿糖（葡萄糖）
		if ($sugar && $sugar == '-') {
			$sugar 	=	'1';
		}elseif( $sugar ){
			$sugar 	=	'2';
		}
		// 数据处理 ---- 维生素c
		if ($vc && $vc == '-') {
			$vc 	=	'1';
		}elseif( $vc ){
			$vc 	=	'2';
		}

		
		$result 	=	array();
		if ( $info ) {
			foreach ($info as $key => $value) {
				$title	=	'';
				if ( $nitrite && $nitrite == $value['division'] && 1 == $value['types']) {
					$title	=	'亚硝酸盐';
				}

				if ( $urobilinogen && $urobilinogen == $value['division'] && 2 == $value['types']) {
					$title	=	'尿胆原';
				}

				if ( $whitecells && $whitecells == $value['division'] && 3 == $value['types']) {
					$title	=	'白细胞';
				}

				if ( $redcells && $redcells == $value['division'] && 4 == $value['types']) {
					$title	=	'潜血';
				}
				if ( $urineprotein && $urineprotein == $value['division'] && 5 == $value['types']) {
					$title	=	'尿蛋白';
				}
				if ( $ph && $ph >= $value['min'] && $ph <= $value['max'] && 6 == $value['types']) {
					$title	=	'酸碱度';
				}
				if ( $urine && $urine>= $value['min'] && $urine <= $value['max'] && 7 == $value['types']) {
					$title	=	'尿比重';
				}
				if ( $urineketone && $urineketone == $value['division'] && 8 == $value['types']) {
					$title	=	'尿酮';
				}
				if ( $bili && $bili == $value['division'] && 9 == $value['types']) {
					$title	=	'胆红素';
				}
				if ( $sugar && $sugar == $value['division'] && 10 == $value['types']) {
					$title	=	'尿糖';
				}
				if ( $vc && $vc == $value['division'] && 11 == $value['types']) {
					$title	=	'维生素C';
				}

				if ( $title ) {
					$result[$value['types']]	=	$value;
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
	public function humanbody( $weight = '', $bmi = '' , $bf = '', $fatweight = '', $protein = '', $watercontentrate = '', $muscle = '', $mineralsalts = '', $fat= '',$sex = ''){

		$sex 	=	$sex ? intval($sex) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;

		//生成缓存文件
		if(F('Analysis_humanbody')){
			$info = F('Analysis_humanbody');
		}else{
			$info 		= 	M('analysis_humanbody')->select();
			F('Analysis_humanbody',$info);
		}
		

		$result 	=	array();
		if ( $info ) {
			foreach ($info as $key => $value) {
				$title	=	'';
				if ( $weight && $weight>= $value['min'] && $weight <= $value['max'] && $value['types'] == 1) {
					$title	=	'体重';
				}

				if ( $bmi && $bmi>= $value['min'] && $bmi <= $value['max'] && $value['types'] == 2) {
					$title	=	'BMI';
				}

				if ( $bf && $bf >= $value['min'] && $bf <= $value['max'] && 3 == $value['types'] && $value['sex'] == $sex) {
					$title	=	'体脂率';
				}

				if ( $fatweight && $fatweight>= $value['min'] && $fatweight <= $value['max'] && 4 == $value['types']) {
					$title	=	'去脂体重';
				}
				if ( $protein && $protein>= $value['min'] && $protein <= $value['max'] && 5 == $value['types']) {
					$title	=	'内脏脂肪指数';
				}
				if ( $watercontentrate && $watercontentrate>= $value['min'] && $watercontentrate <= $value['max'] && 6 == $value['types']) {
					$title	=	'身体总水分率';
				}
				if ( $muscle && $muscle>= $value['min'] && $muscle <= $value['max'] && 7 == $value['types']) {
					$title	=	'肌肉量';
				}
				if ( $mineralsalts && $mineralsalts>= $value['min'] && $mineralsalts <= $value['max'] && 8 == $value['types']) {
					$title	=	'骨量';
				}
				if ( $fat && $fat>= $value['min'] && $fat <= $value['max'] && 9 == $value['types']) {
					$title	=	'基础代谢率';
				}

				if ( $title ) {
					$result[$value['types']]	=	$value;
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
		//生成文件缓存
		if(F('Analysis_bloodketone')){
			$info	=	F('Analysis_bloodketone');	
		}else{
	   	 	$info	=	M('analysis_bloodketone')->select();
	   	 	F('Analysis_bloodketone',$info);
		}
	   	if ( !$info ) {
			return false;
		}

		$result 	=	array();
		foreach ($info as $key => $value) {
			if($value['min'] <= $bk && $value['max'] >=  $bk ){
				$result 	=	$value;
				break;
			}
		}

		return $result ? $result : array();
	}


	/**
	 * 血尿酸
	 * 
	 * @param  string $suricacid  血尿酸
	 * @param  string $sex  性别
	 * @return array   返回分析结果原始数据
	 */
	public function renal( $suricacid = '' , $sex = '' ){
		//生成缓存文件
		if(F('Analysis_renal')){
			$info = F('Analysis_renal');
		}else{
			$info 		= 	M('analysis_renal')->select();
			F('Analysis_renal',$info);
		}
		$result 	=	array();
		if ( $info ) {
			foreach ($info as $key => $value) {
				if ($suricacid && $suricacid>= $value['min'] && $suricacid <= $value['max'] && $value['types'] == 1 && $value['sex'] == $sex) {
					$result	=	$value;
				}
			}
		}

		return $result ? $result : array();

	}


	/**
	 * 血尿酸
	 * 
	 * @param  string $suricacid  血尿酸
	 * @param  string $sex  性别
	 * @return array   返回分析结果原始数据
	 */
	public function renalnew( $suricacid = '' , $sex = '' ){
		//生成缓存文件
		if(F('Analysis_renalnew')){
			$info = F('Analysis_renalnew');
		}else{
			$info 		= 	M('analysis_renalnew')->select();
			F('Analysis_renalnew',$info);
		}
		$result 	=	array();
		if ( $info ) {
			foreach ($info as $key => $value) {
				if ($suricacid && $suricacid>= $value['min'] && $suricacid <= $value['max'] && $value['types'] == 1 && $value['sex'] == $sex) {
					$result	=	$value;
				}
			}
		}

		return $result ? $result : array();

	}


	/**
	 * 尿微量白蛋白
	 * 
	 * @param  string  $umalbumin      尿微量白蛋白
	 * @return array   返回分析结果原始数据
	 */
	public function umprotein( $umprotein = '' ){
		//生成文件缓存
		if(F('Analysis_umprotein')){
			$info	=	F('Analysis_umprotein');	
		}else{
	   	 	$info	=	M('analysis_umprotein')->select();
	   	 	F('Analysis_umprotein',$info);
		}
	   	if ( !$info ) {
			return false;
		}

		$result 	=	array();
		foreach ($info as $key => $value) {
			if($value['min'] <= $umprotein && $value['max'] >=  $umprotein ){
				$result 	=	$value;
				break;
			}
		}

		return $result ? $result : array();
	}


	/**
	 * 整体大项正常返回结果
	 * @param  string $datas 设备类型 (一次获取多个，中间用英文逗号拆分)
	 *          1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温	
	 * @return [type]        [description]
	 */
	public function resources( $datas = '' ){
		//生成缓存文件
		if(F('Analysis_resources')){
			$result = F('Analysis_resources');
		}else{
			$result 		= 	M('analysis_resources')->select();
			F('Analysis_resources',$result);
		}

		$types 		=	explode(',',$datas);

		$in 		=	array();
		foreach ($types as $key => $value) {
			$in[]	=	intval($value);
		}

		$in 	=	array_filter($in);

		if ( !$in ) {
			return false;
		}

		$info 	=	array();
		
		foreach ($result as $key => $value) {
			if ( in_array( $value['types'],$in ) ) {
				$info[$value['types']]['types']		=	$value['types'];
				$info[$value['types']]['result']	=	isset($value['result']) ? $value['result'] : '';
			}
		}

		return $info;
	}
	
}
