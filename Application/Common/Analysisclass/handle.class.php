<?php
namespace Common\Analysisclass;

/**
 * 处理数据
 *
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class handle {
	
	/**
	 * 处理血糖数据
	 * 
	 * @param  array $data 血糖测量分析结果数据
	 * @param  array $datas 血糖测量相关数据
	 * @return array
	 */
	public function bsugar( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$gltypes 	=	'gl';
		switch ($datas['history']) {
			case '2':
				$gltypes	=	'gl';
				break;
			case '1':
				$gltypes	=	'gl1';
				break;
			case '3':
				$gltypes	=	'gl3';
				break;
			case '4':
				$gltypes	=	'gl4';
				break;
			default:
				$gltypes	=	'gl';
				break;
		}

		// 不同进食状态下的参考范围
		$arr 	=	self::rangedata($gltypes);
		
		$attrnameArr	=	array(
			'1'=>'空腹血糖','2'=>'早餐后2小时血糖','3'=>'随机血糖',
			'5'=>'午餐前血糖','6'=>'午餐后2小时血糖','7'=>'晚餐前血糖',
			'8'=>'晚餐后2小时血糖','9'=>'睡前血糖'
		);

		//	进食状态
		if ($datas['attr'] == 0 || !$datas['attr'] ) {
			$datas['attr'] = 3 ;
		}

		if (!$datas['bloodsugar']) {
			return false;
		}

		$msg 	= 	array();

		// 测量值状态（正常、异常）
		$type	=	0;	
		// 测量值是升高还是下降计算
		$status	=	'';
		// 不同状态下的区间值
		$range	=	'';
		// 测量名称
		$attrname	=	'';

		$attr 		=	$datas['attr'];
		if ($datas ['bloodsugar'] >= $arr[$attr]['0'] && $datas ['bloodsugar'] <= $arr [$attr] ['1']) {
			$type = 0;
		} else {
			$type = 1;
		}
		$status		=	$arr[$attr];
		$range		= 	$arr[$attr]['0'].'-'.$arr[$attr]['1'].'(mmol/L)' ;
		$attrname	=	$attrnameArr[$attr];
		
		$msg['title']		= $attrname;
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';

		// 基本信息处理
		$msg['data']['title']		= $attrname;
		$msg['data']['0'] 			= $range ;
		$msg['data']['bloodsugar']	= $datas ['bloodsugar'] ;//不带单位的原测量数值
		$msg['data']['tests'] 		= $datas ['bloodsugar'].'(mmol/L)' ;
		$msg['data']['status']		= self::getstatus($datas['bloodsugar'],$status);
		$msg['data']['type']		= $type;
		$msg['data']['attr']		= $attr;

		return $msg;
	}

	
	/**
	 * 处理数据 --- 血脂
	 * 
	 * @param  array $data 血脂测量分析结果数据（包含测量信息）
	 * @param  array $datas 血脂测量相关数据
	 * @return array
	 */
	public function bloodfat( $data = array() ,$datas = array() ) {
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}
		$arr 	=	self::rangedata('bf');

		$msg = array();
		// 总胆固醇
		if ($datas['tc']) {
			$msg[1]['status'] 	= 	self::getstatus($datas['tc'],$arr['tc'],'<=');
			$msg[1]['0']		= 	'≤'.$arr['tc']['0'].'(mmol/L)';
			$msg[1]['tests'] 	= 	$datas['tc'].'(mmol/L)';
			$msg[1]['tc'] 		= 	$datas['tc'];//不带单位的原测量数值
			$msg[1]['msg'] 		= 	'总胆固醇';
			if( $datas['tc'] < $arr['tc']['0'] ){
				$msg[1]['type']	= 	0;
			}else{
				$msg[1]['type']	=	1;
			}
		}
		
		// 甘油三酯
		if ($datas['tg']) {
			$msg[2]['status'] 	= 	self::getstatus($datas['tg'],$arr['tg'],'<=');
			$msg[2]['0']		= 	'≤'.$arr['tg']['0'].'(mmol/L)';
			$msg[2]['tests'] 	= 	$datas['tg'].'(mmol/L)';
			$msg[2]['tg'] 		= 	$datas['tg'];//不带单位的原测量数值
			$msg[2]['msg'] 		= 	'甘油三酯';
			if($datas['tg'] < $arr['tg']['0'] ){
				$msg[2]['type']	= 	0;
			}else{
				$msg[2]['type']	=	1;
			}
		}
		
		// 高密度
		if ($datas['htc']) {
			$msg[4]['status'] 	= 	self::getstatus($datas ['htc'],$arr ['htc'],'>=');
			$msg[4]['0']		= 	'≥'.$arr['htc']['0'].'(mmol/L)';
			$msg[4]['tests'] 	= 	$datas['htc'].'(mmol/L)';
			$msg[4]['htc'] 		= 	$datas['htc'];//不带单位的原测量数值
			$msg[4]['msg'] 		= 	'高密度脂蛋白胆固醇';
			if( $datas['htc'] >= $arr['htc']['0'] ){
				$msg[4]['type'] = 0;
			}else{
				$msg[4]['type'] = 1;
			}
		}
		
		// 低密度
		if ($datas['ltc']) {
			$msg[3]['status'] 	= 	self::getstatus($datas ['ltc'],$arr ['ltc'],'<');
			$msg[3]['0']		= 	'＜'.$arr['ltc']['0'].'(mmol/L)';
			$msg[3]['tests'] 	= 	$datas['ltc'].'(mmol/L)';
			$msg[3]['ltc'] 		= 	$datas['ltc'];//不带单位的原测量数值
			$msg[3]['msg'] 		= 	'低密度脂蛋白胆固醇';

			if($datas['ltc'] < $arr['ltc']['0'] ){
				$msg[3]['type']	= 	0;
			}else{
				$msg[3]['type']	=	1;
			}
		}

		$result 	=	array();
		foreach ($data as $key => $value) {
			$result[$key]['title']		=	$msg[$key]['msg'];
			$result[$key]['state']		=	$value['state'];
			$result[$key]['clinical']	=	$value['clinical'] ? $value['clinical'] : '';
			$result[$key]['result']		=	$value['result'] ? $value['result'] : '';
			$result[$key]['danger']		=	$value['danger'] ? $value['danger'] : '';
			$result[$key]['nutrition']	= 	$value['nutrition'] ? $value['nutrition'] : '';
			$result[$key]['recovery']	= 	$value['recovery'] ? $value['recovery'] : '';
			$result[$key]['data']		=	$msg[$key];
		}

	  	return $result;
	}


	/**
	 * 处理数据 --- 血压
	 * 
	 * @param  array $data 血压测量分析结果数据
	 * @param  array $datas 血压测量相关数据
	 * @return array
	 */
	public function bloodp( $data = array(),$datas = array() ) {
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		} 

		$arr 	=	self::rangedata('bp');
		
		$msg = array();
		// 低压
		if ($datas['lboodp']) {
			$msg['lboodp']['status'] 	= self::getstatus($datas ['lboodp'],$arr['lboodp'],'>=<');
			$msg['lboodp']['0']			= $arr['lboodp']['0'].'-'.$arr['lboodp']['1'].'(不含)(mmHg)';
			$msg['lboodp']['tests'] 	= $datas['lboodp'].'(mmHg)';
			$msg['lboodp']['lboodp'] 	= $datas['lboodp'];//不带单位的原测量数值
			$msg['lboodp']['msg'] 		= '舒张压(低压)';

			if( $datas['lboodp'] >= $arr['lboodp']['0'] && $datas['lboodp'] < $arr['lboodp']['1'] ){
				$msg['lboodp']['type'] = 0;
			}else{
				$msg['lboodp']['type'] = 1;
			}	
		}
		

		//高压
		if ($datas['hboodp']) {
			$msg['hboodp']['status'] 	= self::getstatus($datas ['hboodp'],$arr['hboodp'],'>=<');
			$msg['hboodp']['0']			= $arr['hboodp']['0'].'-'.$arr['hboodp']['1'].'(不含)(mmHg)';
			$msg['hboodp']['tests'] 	= $datas['hboodp'].'(mmHg)';
			$msg['hboodp']['hboodp'] 	= $datas['hboodp'];//不带单位的原测量数值
			$msg['hboodp']['msg'] 		= '收缩压(高压)';

			if($datas['hboodp'] >= $arr['hboodp']['0'] && $datas['hboodp']<$arr['hboodp']['1'] ){
				$msg['hboodp']['type']= 0;
			}else{
				$msg['hboodp']['type']=1;
			}
		}
		
		
		$result 	=	array();
		$result['title']		=	'血压分析结果';
		$result['state']		=	$data['state'];
		$result['clinical']		=	$data['clinical'] ? $data['clinical'] : '';
		$result['result']		=	$data['result'] ? $data['result'] : '';
		$result['danger']		=	$data['danger'] ? $data['danger'] : '';
		$result['nutrition']	= 	$data['nutrition'] ? $data['nutrition'] : '';
		$result['recovery']		= 	$data['recovery'] ? $data['recovery'] : '';
		$result['hboodp']		=	$msg['hboodp'] ? $msg['hboodp'] : array();
		$result['lboodp']		=	$msg['lboodp'] ? $msg['lboodp'] : array();

		return $result;
	}



	/**
	 * 处理数据 --- 体成分
	 * 
	 * @param  array $data 体成分测量分析结果数据
	 * @param  array $datas 体成分测量相关数据
	 * @return array
	 */
	public function humanbody($data = array() , $datas = array() ) {
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		} 

		$arr 	=	self::rangedata('we');

		//BMI
		if ( $datas['bmi'] ) {
			$msg[2]['status'] 	= self::getstatus($datas ['bmi'],$arr['bmi']);
			$msg[2]['0']		= $arr['bmi']['0'].'-'.$arr['bmi']['1'].'(kg/㎡)';
			$msg[2]['tests'] 	= $datas['bmi'].'(kg/㎡)';
			$msg[2]['bmi'] 		= $datas['bmi'];//不带单位的原测量数值
			$msg[2]['msg'] 		= 'BMI ';
			if($datas['bmi']>=$arr['bmi']['0'] && $datas['bmi']<=$arr['bmi']['1'] ){
				$msg[2]['type']	= 0;
			}else{
				$msg[2]['type']	= 1;
			}
		}
		
		//体脂率
		//  体脂率(区分性别)
		$sex 	=	$datas['sex'] ? intval($datas['sex']) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;
		if ( $datas['bf'] ) {
			$msg[3]['tests']= $datas['bf'].'(%)';
			$msg[3]['bf']	= $datas['bf'];//不带单位的原测量数值
			$msg[3]['msg']	= '体脂率 ';
			if( $sex == 2 ){
				//女
				if($datas['bf']>=$arr['bf']['1']['0'] && $datas['bf']<=$arr['bf']['1']['1'] ){
					$msg['3']['type']= 0;
				}else{
					$msg['3']['type']=1;
				}
				$msg['3']['0']= $arr['bf']['1']['0'].'-'.$arr['bf']['1']['1'].'(%)';
				$msg['3']['status'] 	= self::getstatus($datas ['bf'],$arr['bf']['1']);
			}else{
				//男
				if($datas['bf']>=$arr['bf']['0']['0'] && $datas['bf']<=$arr['bf']['0']['1'] ){
					$msg['3']['type']		= 0;
				}else{
					$msg['3']['type'] 		= 1;
				}
				$msg['3']['0']				= $arr['bf']['0']['0'].'-'.$arr['bf']['0']['1'].'(%)';
				$msg['3']['status'] 		= self::getstatus($datas ['bf'],$arr['bf']['0']);
			}
		}
		


		// 内脏脂肪指数
		if ($datas['protein']) {
			$msg['5']['status'] 	= self::getstatus($datas ['protein'],$arr['protein']);
			$msg['5']['0']			= $arr['protein']['0'].'-'.$arr['protein']['1'];
			$msg['5']['tests'] 		= $datas['protein'];
			$msg['5']['protein'] 	= $datas['protein'];//不带单位的原测量数值
			$msg['5']['msg'] 		= '内脏脂肪指数 ';
			if($datas['protein']>=$arr['protein']['0'] && $datas['protein']<=$arr['protein']['1'] ){
				$msg['5']['type']	= 0;
			}else{
				$msg['5']['type']	= 1;
			}
		}
		

		//去脂体重
		if ($datas['fatweight']) {
			$msg['4']['status'] 	= self::getstatus($datas ['fatweight'],$arr['fatweight']);
			$msg['4']['0']			= $arr['fatweight']['0'].'-'.$arr['fatweight']['1'].'(kg)';
			$msg['4']['tests'] 		= $datas['fatweight'].'(kg)';
			$msg['4']['fatweight'] 	= $datas['fatweight'];//不带单位的原测量数值
			$msg['4']['msg'] 		= '去脂体重 ';
			if($datas['fatweight']>=$arr['fatweight']['0'] && $datas['fatweight']<=$arr['fatweight']['1'] ){
				$msg['4']['type']	= 0;
			}else{
				$msg['4']['type'] 	= 1;
			}
		}
		
		//体重
		if( $datas['weight'] ){
			$msg['1']['status'] 	= '';
			$msg['1']['type']		= '';
			$msg['1']['0']		    = '-';
			$msg['1']['tests'] 	= $datas['weight'].'(kg)';
			$msg['1']['weight'] 	= $datas['weight'];//不带单位的原测量数值
			$msg['1']['msg'] 		= '体重';
		}


		//基础代谢率
		if ($datas['fat']) {
			$msg['9']['status'] 	= self::getstatus($datas ['fat'],$arr['fat']);
			$msg['9']['0']		= $arr['fat']['0'].'-'.$arr['fat']['1'].'(kcal/day)';
			$msg['9']['tests'] 	= $datas['fat'].'(kcal/day)';
			$msg['9']['fat'] 		= $datas['fat'];//不带单位的原测量数值
			$msg['9']['msg'] 		= '基础代谢率 ';
			if($datas['fat']>=$arr['fat']['0'] && $datas['fat']<=$arr['fat']['1'] ){
				$msg['9']['type']	= 0;
			}else{
				$msg['9']['type']	= 1;
			}
		}

		

		//骨量
		if( $datas['mineralsalts'] ){
			$msg['8']['status'] 	= self::getstatus($datas ['mineralsalts'],$arr['mineralsalts']);
			
			$msg['8']['0']				= $arr['mineralsalts']['0'].'-'.$arr['mineralsalts']['1'].'(kg)';
			$msg['8']['tests'] 			= $datas['mineralsalts'].'(kg)';
			$msg['8']['mineralsalts']	= $datas['mineralsalts'];//不带单位的原测量数值
			$msg['8']['msg']			= '骨量';

			if($datas['mineralsalts']>=$arr['mineralsalts']['0'] && $datas['mineralsalts']<=$arr['mineralsalts']['1'] ){
				$msg['8']['type']	= 0;
			}else{
				$msg['8']['type']	= 1;
			}


		}

		//肌肉量
		if ($datas['muscle']) {
			$msg['7']['status'] 	= self::getstatus($datas ['muscle'],$arr['muscle']);
			$msg['7']['0']			= $arr['muscle']['0'].'-'.$arr['muscle']['1'].'(kg)';
			$msg['7']['tests'] 		= $datas['muscle'].'(kg)';
			$msg['7']['muscle'] 	= $datas['muscle'];//不带单位的原测量数值
			$msg['7']['msg'] 		= '肌肉量 ';
			if($datas['muscle']>=$arr['muscle']['0'] && $datas['muscle']<=$arr['muscle']['1'] ){
				$msg['7']['type']	= 0 ;
			}else{
				$msg['7']['type']	= 1 ;
			}
		}
		

		//身体总水分率
		if ($datas['watercontentrate']) {
			$msg['6']['status'] 	= self::getstatus($datas ['watercontentrate'],$arr['watercontentrate']);
			$msg['6']['0']			= $arr['watercontentrate']['0'].'-'.$arr['watercontentrate']['1'].'(%)';
			$msg['6']['tests'] 		= $datas['watercontentrate'].'(%)';
			$msg['6']['water'] 		= $datas['watercontentrate'];//不带单位的原测量数值
			$msg['6']['msg'] 		= '身体总水分率';
			if($datas['watercontentrate']>=$arr['watercontentrate']['0'] && $datas['watercontentrate']<=$arr['watercontentrate']['1'] ){
				$msg['6']['type']	= 0;
			}else{
				$msg['6']['type']	= 1;
			}
		}
		
		$result 	=	array();
		foreach ($data as $key => $value) {
			$result[$key]['title']		=	$msg[$key]['msg'];
			$result[$key]['state']		=	$value['state'];
			$result[$key]['clinical']	=	$value['clinical'] ? $value['clinical'] : '';
			$result[$key]['result']		=	$value['result'] ? $value['result'] : '';
			$result[$key]['danger']		=	$value['danger'] ? $value['danger'] : '';
			$result[$key]['nutrition']	= 	$value['nutrition'] ? $value['nutrition'] : '';
			$result[$key]['recovery']	= 	$value['recovery'] ? $value['recovery'] : '';

			$result[$key]['data']		=	$msg[$key];
		}

		if (!isset($result['1'])) {
			$result[1]['title']		=	$msg[1]['msg'];
			$result[1]['state']		=	'';
			$result[1]['clinical']	=	'';
			$result[1]['result']	=	'';
			$result[1]['danger']	=	'';
			$result[1]['nutrition']	= 	'';
			$result[1]['recovery']	= 	'';
			$result[1]['data']		=	$msg[1];
		}
		
	   	return $result;	
	}


	/**
	 * 处理数据 --- 血氧
	 * 
	 * @param  array $data  血氧测量分析结果数据
	 * @param  array $datas 血氧测量相关数据
	 * @return array
	 */
	public function oxygen( $data = array() , $datas = array() ) {
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		} 

		$arr 	=	self::rangedata('ox');

		$msg = array();

		// 血氧饱和度 %
		if ( $datas['saturation'] ) {
			$msg['1']['status'] 	= self::getstatus($datas ['saturation'],$arr['saturation']);
			$msg['1']['0']			= $arr['saturation']['0'].'-'.$arr['saturation']['1'].'(%)';
			$msg['1']['tests'] 	= $datas['saturation'].'(%)';
			$msg['1']['saturation']= $datas['saturation'];//不带单位的原测量数值
			$msg['1']['msg'] = '血氧饱和度 ';

			if( $datas['saturation'] >= $arr['saturation']['0'] && $datas['saturation'] <= $arr['saturation']['1'] ){
				$msg['1']['type'] = 0;
			}else{
				$msg['1']['type'] = 1;
			}
		}

		// 脉率	
		if ($datas['pr']) {
			$msg['2']['status']	= self::getstatus($datas ['pr'],$arr['pr']);
			$msg['2']['0']		= $arr['pr']['0'].'-'.$arr['pr']['1'].'(bpm)';
			$msg['2']['tests'] 	= $datas['pr'].'(bpm)';
			$msg['2']['pr'] 	= $datas['pr'];//不带单位的原测量数值
			$msg['2']['msg'] 	= '脉率';
			if($datas['pr'] >= $arr['pr']['0'] && $datas['pr'] <= $arr['pr']['1'] ){
				$msg['2']['type'] = 0;
			}else{
				$msg['2']['type'] = 1;
			}
		}
		
		// 分析结果
		$result 	=	array();
		foreach ($data as $key => $value) {
			if ( isset($msg[$key]) ) {
				$result[$key]['title']		=	$msg[$key]['msg'];
				$result[$key]['state']		=	$value['state'];
				$result[$key]['clinical']	=	$value['clinical'] ? $value['clinical'] : '';
				$result[$key]['result']		=	$value['result'] ? $value['result'] : '';
				$result[$key]['danger']		=	$value['danger'] ? $value['danger'] : '';
				$result[$key]['nutrition']	= 	$value['nutrition'] ? $value['nutrition'] : '';
				$result[$key]['recovery']	= 	$value['recovery'] ? $value['recovery'] : '';

				$result[$key]['data']		=	$msg[$key];
			}
		}

		return $result;
	}


	/**
	 * 处理数据 --- 恒温
	 * 
	 * @param  array $data  恒温测量分析结果数据
	 * @param  array $datas 恒温测量相关数据
	 * @return array
	 */
	public function tm( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('tm');

		$msg = array();
		$msg['title']		= '体温';
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';

		$msg['data']['status'] 	= self::getstatus($datas ['tmv'],$arr['tmv']);
		$msg['data']['0']		= $arr['tmv']['0'].'-'.$arr['tmv']['1'].'(℃)';
		$msg['data']['tests'] 	= $datas['tmv'].'(℃)';
		$msg['data']['tmv']		= $datas['tmv'];//不带单位的原测量数值
		$msg['data']['msg']		= '体温';
		if($datas['tmv']>=$arr['tmv']['0'] && $datas['tmv']<=$arr['tmv']['1'] ){
			$msg['data']['type'] = 0;
		}else{
			$msg['data']['type'] = 1;
		}
		
		return $msg;
	}


	/**
	 * 处理数据 --- 心电
	 * 
	 * @param  array $data  心电测量分析结果数据
	 * @param  array $datas 心电测量相关数据
	 * @return array
	 */
	public function el( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('el');

		$msg = array();
		$msg['title']		= '心率';
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';

		$msg['data']['status'] 	= self::getstatus($datas ['bpm'],$arr['bpm']);
		$msg['data']['0']		= $arr['bpm']['0'].'—'.$arr['bpm']['1'].'(bpm)';
		$msg['data']['tests'] 	= $datas['bpm'].'(bpm)';
		$msg['data']['bpm']		= $datas['bpm'];//不带单位的原测量数值
		$msg['data']['msg']		= '心率';
		if($datas['bpm']>=$arr['bpm']['0'] && $datas['bpm']<=$arr['bpm']['1'] ){
			$msg['data']['type'] = 0;
		}else{
			$msg['data']['type'] = 1;
		}
		
		return $msg;
	}

	/**
	 * 处理数据 --- 尿常规
	 * 
	 * @param  array $data  尿常规测量分析结果数据
	 * @param  array $datas 尿常规测量相关数据
	 * @return array
	 */
	public function urine($data = array() , $datas = array() ) {
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('ur');

		$msg 	=	array();

		//亚硝酸盐
		if ( $datas['nitrite'] ) {
			$msg['1']['status']	= '-1';
			$msg['1']['0']		= $arr['nitrite']['0'];
			$msg['1']['tests']	= $datas['nitrite'];
			$msg['1']['nitrite'] 	= $datas['nitrite'];
			$msg['1']['msg'] 		= '亚硝酸盐';
			if ($datas['nitrite'] != $arr['nitrite']['0'] ) {
				$msg['1']['type']	=	1;
			}else{
				$msg['1']['type']	=	0;
			}
		}
		
		//尿胆原
		if ( $datas['urobilinogen'] ) {
			$msg['2']['status']	= '-1';
			$msg['2']['0']		= $arr['urobilinogen']['0'];
			$msg['2']['tests']	= $datas['urobilinogen'];
			$msg['2']['urobilinogen'] 	= $datas['urobilinogen'];
			$msg['2']['msg'] 			= '尿胆原';
			if ($datas['urobilinogen'] != $arr['urobilinogen']['0'] ) {
				$msg['2']['type']	=	1;
			}else{
				$msg['2']['type']	=	0;
			}
		}

		// 白细胞
		if ( $datas['whitecells'] ) {
			$msg['3']['status']	= '-1';
			$msg['3']['0']		= $arr['whitecells']['0'];
			$msg['3']['tests'] 	= $datas['whitecells'];
			$msg['3']['whitecells'] 	= $datas['whitecells'];
			$msg['3']['msg'] 			= '白细胞';
			if ($datas['whitecells'] != $arr['whitecells']['0'] ) {
				$msg['3']['type']	=	1;
			}else{
				$msg['3']['type']	=	0;
			}
		}
		// 潜血
		if ( $datas['redcells'] ) {
			$msg['4']['status']	= '-1';
			$msg['4']['0']		= $arr['redcells']['0'];
			$msg['4']['tests'] 	= $datas['redcells'];
			$msg['4']['redcells'] 	= $datas['redcells'];
			$msg['4']['msg'] 			= '潜血';
			if ($datas['redcells'] != $arr['redcells']['0'] ) {
				$msg['4']['type']	=	1;
			}else{
				$msg['4']['type']	=	0;
			}
		}
		// 尿蛋白
		if ( $datas['urineprotein'] ) {
			$msg['5']['status']	= '-1';
			$msg['5']['0']		= $arr['urineprotein']['0'];
			$msg['5']['tests'] 	= $datas['urineprotein'];
			$msg['5']['urineprotein'] 	= $datas['urineprotein'];
			$msg['5']['msg'] 			= '尿蛋白';
			if ($datas['urineprotein'] != $arr['urineprotein']['0'] ) {
				$msg['5']['type']	=	1;
			}else{
				$msg['5']['type']	=	0;
			}
		}
		
		// ph
		if ( $datas['ph'] ) {
			$msg['6']['status'] 	= self::getstatus($datas ['ph'],$arr['ph']);
			$msg['6']['0']			= $arr['ph']['0'].'-'.$arr['ph']['1'];
			$msg['6']['tests'] 		= $datas['ph'];
			$msg['6']['ph'] 		= $datas['ph'];
			$msg['6']['msg'] 		= '酸碱度';
			if( $datas['ph'] >= $arr['ph']['0'] && $datas['ph'] <= $arr['ph']['1'] ){
				$msg['6']['type']= 0;
			}else{
				$msg['6']['type']=1;
			}
		}
		// 尿比重
		if ( $datas['urine'] ) {
			$msg['7']['status'] = self::getstatus($datas ['urine'],$arr['urine']);
			$msg['7']['0']		= $arr['urine']['0'].'～'.$arr['urine']['1'];
			$msg['7']['tests'] 	= $datas['urine'];
			$msg['7']['urine'] 	= $datas['urine'];
			$msg['7']['msg'] 	= '尿比重';
			if( $datas['urine'] >= $arr['urine']['0'] && $datas['urine'] <= $arr['urine']['1'] ){
				$msg['7']['type']= 0;
			}else{
				$msg['7']['type']=1;
			}
		}
		

		// 尿酮
		if ( $datas['urineketone'] ) {
			$msg['8']['status']	= '-1';
			$msg['8']['0']		= $arr['urineketone']['0'];
			$msg['8']['tests'] 	= $datas['urineketone'];
			$msg['8']['urineketone'] 	= $datas['urineketone'];
			$msg['8']['msg'] 			= '尿酮';
			if ($datas['urineketone'] != $arr['urineketone']['0'] ) {
				$msg['8']['type']	=	1;
			}else{
				$msg['8']['type']	=	0;
			}
		}

		// 胆红素
		if ( $datas['bili'] ) {
			$msg['9']['status']	= '-1';
			$msg['9']['0']		= $arr['bili']['0'];
			$msg['9']['tests'] 	= $datas['bili'];
			$msg['9']['bili'] 	= $datas['bili'];
			$msg['9']['msg'] 			= '胆红素';
			if ($datas['bili'] != $arr['bili']['0'] ) {
				$msg['9']['type']	=	1;
			}else{
				$msg['9']['type']	=	0;
			}
		}
		
		// 尿糖
		if ( $datas['sugar'] ) {
			$msg['10']['status']	= '-1';
			$msg['10']['0']			= $arr['sugar']['0'];
			$msg['10']['tests'] 	= $datas['sugar'];
			$msg['10']['sugar'] 	= $datas['sugar'];
			$msg['10']['msg'] 			= '尿糖';
			if ($datas['sugar'] != $arr['sugar']['0'] ) {
				$msg['10']['type']	=	1;
			}else{
				$msg['10']['type']	=	0;
			}
		}

		// vc
		if ( $datas['vc'] ) {
			$msg['11']['status'] 	= '-1';
			$msg['11']['0']			= $arr['vc']['0'];
			$msg['11']['tests'] 	= $datas['vc'];
			$msg['11']['vc'] 		= $datas['vc'];
			$msg['11']['msg'] 			= '维生素c';
			if ($datas['vc'] != $arr['vc']['0'] ) {
				$msg['11']['type']	=	1;
			}else{
				$msg['11']['type']	=	0;
			}
		}
		
		$result 	=	array();
		foreach ($data as $key => $value) {
			$result[$key]['title']		=	$msg[$key]['msg'];
			$result[$key]['state']		=	$value['state'];
			$result[$key]['clinical']	=	$value['clinical'] ? $value['clinical'] : '';
			$result[$key]['result']		=	$value['result'] ? $value['result'] : '';
			$result[$key]['danger']		=	$value['danger'] ? $value['danger'] : '';
			$result[$key]['nutrition']	= 	$value['nutrition'] ? $value['nutrition'] : '';
			$result[$key]['recovery']	= 	$value['recovery'] ? $value['recovery'] : '';

			$result[$key]['data']		=	$msg[$key];
		}

		return $result;
	}



	/**
	 * 处理数据 --- 血酮
	 * 
	 * @param  array $data  血酮测量分析结果数据
	 * @param  array $datas 血酮测量相关数据
	 * @return array
	 */
	public function bloodketone( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('bk');

		$msg = array();
		$msg['title']		= '血酮';
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';

		$msg['data']['status'] 	= self::getstatus($datas ['bk'],$arr['bk']);
		$msg['data']['0']		= $arr['bk']['0'].'-'.$arr['bk']['1'].' mmol/L';
		$msg['data']['tests'] 	= $datas['bk'].'(mmol/L)';
		$msg['data']['bk']		= $datas['bk'];//不带单位的原测量数值
		$msg['data']['msg']		= '血酮';
		if($datas['bk']>=$arr['bk']['0'] && $datas['bk']<=$arr['bk']['1'] ){
			$msg['data']['type'] = 0;
		}else{
			$msg['data']['type'] = 1;
		}
		
		return $msg;
	}


	/**
	 * 处理数据 --- 血尿酸
	 * 
	 * @param  array $data  血尿酸测量分析结果数据
	 * @param  array $datas 血尿酸测量相关数据
	 * @return array
	 */
	public function renal( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('re');

		$msg = array();
		$msg['title']		= '血尿酸';
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';

		//  血尿酸(区分性别)
		$sex 	=	$datas['sex'] ? intval($datas['sex']) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;
		if( $sex == 2 ){
			//女
			if($datas['suricacid'] >= $arr['suricacid']['1']['0'] && $datas['suricacid'] <= $arr['suricacid']['1']['1'] ){
				$msg['data']['type']	= 	0;
			}else{
				$msg['data']['type']	=	1;
			}
			$msg['data']['0']			= 	$arr['suricacid']['1']['0'].'-'.$arr['suricacid']['1']['1'].' mg/dL';
			$msg['data']['status'] 		= 	self::getstatus($datas ['suricacid'],$arr['suricacid']['1']);
		}else{
			//男
			if($datas['suricacid'] >= $arr['suricacid']['0']['0'] && $datas['suricacid'] <= $arr['suricacid']['0']['1'] ){
				$msg['data']['type']		= 0;
			}else{
				$msg['data']['type'] 		= 1;
			}
			$msg['data']['0']				= $arr['suricacid']['0']['0'].'-'.$arr['suricacid']['0']['1'].' mg/dL';
			$msg['data']['status'] 			= self::getstatus($datas ['suricacid'],$arr['suricacid']['0']);
		}

		$msg['data']['tests'] 		= $datas['suricacid'].'(mg/dL)';
		$msg['data']['suricacid']	= $datas['suricacid'];//不带单位的原测量数值
		$msg['data']['msg']			= '血尿酸';
		
		
		return $msg;
	}



	/**
	 * 处理数据 --- 血尿酸 μmol/L
	 * 
	 * @param  array $data  血尿酸测量分析结果数据
	 * @param  array $datas 血尿酸测量相关数据
	 * @return array
	 */
	public function renalnew( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('renalnew');

		$msg = array();
		$msg['title']		= '血尿酸';
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';

		//  血尿酸(区分性别)
		$sex 	=	$datas['sex'] ? intval($datas['sex']) : 0;
		$sex 	=	$sex == 0 ? 1 : 2;
		if( $sex == 2 ){
			//女
			if($datas['suricacid'] >= $arr['suricacid']['1']['0'] && $datas['suricacid'] <= $arr['suricacid']['1']['1'] ){
				$msg['data']['type']	= 	0;
			}else{
				$msg['data']['type']	=	1;
			}
			$msg['data']['0']			= 	$arr['suricacid']['1']['0'].'-'.$arr['suricacid']['1']['1'].' μmol/L';
			$msg['data']['status'] 		= 	self::getstatus($datas ['suricacid'],$arr['suricacid']['1']);
		}else{
			//男
			if($datas['suricacid'] >= $arr['suricacid']['0']['0'] && $datas['suricacid'] <= $arr['suricacid']['0']['1'] ){
				$msg['data']['type']		= 0;
			}else{
				$msg['data']['type'] 		= 1;
			}
			$msg['data']['0']				= $arr['suricacid']['0']['0'].'-'.$arr['suricacid']['0']['1'].' μmol/L';
			$msg['data']['status'] 			= self::getstatus($datas ['suricacid'],$arr['suricacid']['0']);
		}

		$msg['data']['tests'] 		= $datas['suricacid'].'(μmol/L)';
		$msg['data']['suricacid']	= $datas['suricacid'];//不带单位的原测量数值
		$msg['data']['msg']			= '血尿酸';
		
		
		return $msg;
	}



	/**
	 * 处理数据 --- 尿微量白蛋白
	 * 
	 * @param  array $data  尿微量白蛋白测量分析结果数据
	 * @param  array $datas 尿微量白蛋白测量相关数据
	 * @return array
	 */
	public function umprotein( $data = array() , $datas = array() ){
		if( !$data || !is_array($data) || !$datas || !is_array($datas) ){
			return '';
		}

		$arr 	=	self::rangedata('um');

		$msg = array();
		$msg['title']		= '尿微量白蛋白';
		$msg['state']		= $data['state'];
		$msg['clinical']	= $data['clinical'] ? $data['clinical'] : '';
		$msg['result']		= $data['result'] ? $data['result'] : '';
		$msg['danger']		= $data['danger'] ? $data['danger'] : '';
		$msg['nutrition']	= $data['nutrition'] ? $data['nutrition'] : '';
		$msg['recovery']	= $data['recovery'] ? $data['recovery'] : '';
		
		$msg['data']['status'] 	= self::getstatus($datas ['um'],$arr['um']);
		$msg['data']['0']		= $arr['um']['0'].'-'.$arr['um']['1'].' mg/L';
		$msg['data']['tests'] 	= $datas['um'].'(mg/L)';
		$msg['data']['um']		= $datas['um'];//不带单位的原测量数值
		$msg['data']['msg']		= '尿微量白蛋白';
		if($datas['um']>=$arr['um']['0'] && $datas['um']<=$arr['um']['1'] ){
			$msg['data']['type'] = 0;
		}else{
			$msg['data']['type'] = 1;
		}
		
		return $msg;
	}

	/**
	 * 处理数据 --- 整体项目分析
	 * 
	 * @param  array $data 整体项目分析结果数据
	 * @return array
	 */
	public function resources( $data = array() ){
		if( !$data || !is_array($data) ){
			return '';
		}

		$result 	=	array();
		foreach ($data as $key => $value) {
			$result[$value['types']]	=	$value['result'];
		}
		return $result;
	}


	/**
	 * 判定康宝8项的检测状态 是上升,下降,还是正常 
	 * @param number $checkdata :检测的数值
	 * @param unknown $data :正常的比较数值
	 * @param unknown $strflag :运算符号字符串,数值比较(<,>,=,!=符号)
	 * @return $status 0:正常,1:下降,2:上升 
	 * 
	 */
	private function getstatus($checkdata=0,$data = array(),$strflag=''){
		
		if(!$data){
			return '';
		}
		if(count($data)==1){
			//单一值
			if( trim($strflag) == '>' ){
				if( $checkdata > $data['0'] ){
					$status = 0;
				}else{
					$status = 1;
				}
			}elseif( trim($strflag) == '<' ){
				if( $checkdata<$data['0'] ){
					$status = 0;
				}else{
					$status = 2;
				}
			}elseif( trim($strflag) == '>=' ){
				if( $checkdata >= $data['0'] ){
					$status = 0;
				}elseif( $checkdata < $data['0'] ){
					$status = 1;
				}else{
					$status = 2;
				}
			}elseif( trim($strflag) == '<=' ){
				if( $checkdata <= $data['0'] ){
					$status = 0;
				}elseif( $checkdata > $data['0'] ){
					$status = 2;
				}else{
					$status = 1;
				}
			}else{
				$status =0;
			}
		}else if( count($data) == 2 ){
			//2个值
			if( trim($strflag) == "!=" ){
				//不包含（60（不含）-90（不含））
				if( $checkdata <= $data[0] ){
					$status = 1;
				}elseif( $checkdata >= $data[1] ){
					$status = 2;
				}else{
					$status = 0;
				}
			}elseif( trim($strflag) == "<" ){
				//这样的范围＜0.25～0.3
				if( $checkdata < $data[0] ){
					$status = 1;
				}elseif( $checkdata > $data[1] ){
					$status = 2;
				}else{
					$status = 0;
				}
			}elseif( trim($strflag) == ">=<" ){
				// 血压新处理项 不包含（60 -90（不含））
				if( $checkdata < $data[0] ){
					$status = 1;
				}elseif( $checkdata >= $data[1] ){
					$status = 2;
				}else{
					$status = 0;
				}
			}else{
				//这样的范围39.2-48.0
				if( $checkdata < $data[0] ){
					$status = 1;
				}elseif( $checkdata > $data[1] ){
					$status = 2;
				}else{
					$status = 0;
				}
			}
				
		}
		return $status;
	}


	/**
	 * 各检测状态测量范围
	 *
	 * @param string $type 设备类型
	 * @return [type] [description]
	 */
	public function rangedata( $type = '' ){
		$result			=	array();

		// 2：无  
		// $result['gl']	=	array(
		// 	// 空腹血糖
		// 	'1' => array ( '0' => '2.8','1' => '6.0'),
		// 	// 早餐后2小时血糖
		// 	'2' => array ( '0' => '2.8','1' => '7.7'),  
		// 	// 随机血糖
		// 	'3' => array ( '0' => '2.8','1' => '7.7'),  
		// 	// 午餐前血糖
		// 	'5' => array ( '0' => '2.8','1' => '6.0'),  
		// 	// 午餐后2小时血糖
		// 	'6' => array ( '0' => '2.8','1' => '7.7'),  
		// 	// 晚餐前血糖
		// 	'7' => array ( '0' => '2.8','1' => '6.0'),  
		// 	// 晚餐后2小时血糖 
		// 	'8' => array ( '0' => '2.8','1' => '7.7'),  
		// 	// 睡前血糖
		// 	'9' => array ( '0' => '2.8','1' => '6.0'),  
		// );
		/*
			正常值范围变动
			变动时间：2017-05-24
			程序开发：王阳阳
			取值范围整理人：石华
		 */
		$result['gl']	=	array(
			// 空腹血糖
			'1' => array ( '0' => '2.8','1' => '6.0'),
			// 早餐后2小时血糖
			'2' => array ( '0' => '2.8','1' => '7.7'),  
			// 随机血糖
			'3' => array ( '0' => '2.8','1' => '11.0'),  
			// 午餐前血糖
			'5' => array ( '0' => '2.8','1' => '6.0'),  
			// 午餐后2小时血糖
			'6' => array ( '0' => '2.8','1' => '7.7'),  
			// 晚餐前血糖
			'7' => array ( '0' => '2.8','1' => '6.0'),  
			// 晚餐后2小时血糖 
			'8' => array ( '0' => '2.8','1' => '7.7'),  
			// 睡前血糖
			'9' => array ( '0' => '2.8','1' => '6.0'),  
		);



		// 1：二型糖尿病
		// $result['gl1']	=	array(
		// 	// 空腹血糖
		// 	'1' => array ( '0' => '4','1' => '6.9'),
		// 	// 早餐后2小时血糖
		// 	'2' => array ( '0' => '4','1' => '11'),  
		// 	// 随机血糖
		// 	'3' => array ( '0' => '4','1' => '10'),  
		// 	// 午餐前血糖
		// 	'5' => array ( '0' => '4','1' => '7'),  
		// 	// 午餐后2小时血糖
		// 	'6' => array ( '0' => '4','1' => '11'),  
		// 	// 晚餐前血糖
		// 	'7' => array ( '0' => '4','1' => '6.9'),  
		// 	// 晚餐后2小时血糖 
		// 	'8' => array ( '0' => '4','1' => '11'),  
		// 	// 睡前血糖
		// 	'9' => array ( '0' => '4','1' => '6.9'),
		// );
		/*
			正常值范围变动
			变动时间：2017-05-24
			程序开发：王阳阳
			取值范围整理人：石华
		 */
		$result['gl1']	=	array(
			// 空腹血糖
			'1' => array ( '0' => '4.0','1' => '6.9'),
			// 早餐后2小时血糖
			'2' => array ( '0' => '4.0','1' => '11.0'),  
			// 随机血糖
			'3' => array ( '0' => '4.0','1' => '11.0'),  
			// 午餐前血糖
			'5' => array ( '0' => '4','1' => '6.9'),  
			// 午餐后2小时血糖
			'6' => array ( '0' => '4.0','1' => '11.0'),  
			// 晚餐前血糖
			'7' => array ( '0' => '4.0','1' => '6.9'),  
			// 晚餐后2小时血糖 
			'8' => array ( '0' => '4.0','1' => '11.0'),  
			// 睡前血糖
			'9' => array ( '0' => '4.0','1' => '6.9'),
		);

		// 4：一型糖尿病
		// $result['gl4']	=	array(
		// 	// 空腹血糖
		// 	'1' => array ( '0' => '4','1' => '6.1'),
		// 	// 早餐后2小时血糖
		// 	'2' => array ( '0' => '4','1' => '7.7'),  
		// 	// 随机血糖
		// 	'3' => array ( '0' => '4','1' => '10'),  
		// 	// 午餐前血糖
		// 	'5' => array ( '0' => '4','1' => '6.1'),  
		// 	// 午餐后2小时血糖
		// 	'6' => array ( '0' => '4','1' => '7.7'),  
		// 	// 晚餐前血糖
		// 	'7' => array ( '0' => '4','1' => '6.1'),  
		// 	// 晚餐后2小时血糖 
		// 	'8' => array ( '0' => '4','1' => '7.7'),  
		// 	// 睡前血糖
		// 	'9' => array ( '0' => '4','1' => '6.1'),  
		// );
		/*
			正常值范围变动
			变动时间：2017-05-24
			程序开发：王阳阳
			取值范围整理人：石华
		 */
		$result['gl4']	=	array(
			// 空腹血糖
			'1' => array ( '0' => '4.0','1' => '6.9'),
			// 早餐后2小时血糖
			'2' => array ( '0' => '4.0','1' => '11.0'),  
			// 随机血糖
			'3' => array ( '0' => '4.0','1' => '11.0'),  
			// 午餐前血糖
			'5' => array ( '0' => '4','1' => '6.9'),  
			// 午餐后2小时血糖
			'6' => array ( '0' => '4.0','1' => '11.0'),  
			// 晚餐前血糖
			'7' => array ( '0' => '4.0','1' => '6.9'),  
			// 晚餐后2小时血糖 
			'8' => array ( '0' => '4.0','1' => '11.0'),  
			// 睡前血糖
			'9' => array ( '0' => '4.0','1' => '6.9'),
		);


		// 3：妊娠糖尿病
		// $result['gl3']	=	array(
		// 	// 空腹血糖
		// 	'1' => array ( '0' => '4','1' => '5.1'),
		// 	// 早餐后2小时血糖
		// 	'2' => array ( '0' => '4','1' => '8.5'),  
		// 	// 随机血糖
		// 	'3' => array ( '0' => '4','1' => '10'),  
		// 	// 午餐前血糖
		// 	'5' => array ( '0' => '4','1' => '5.1'),  
		// 	// 午餐后2小时血糖
		// 	'6' => array ( '0' => '4','1' => '8.5'),  
		// 	// 晚餐前血糖
		// 	'7' => array ( '0' => '4','1' => '5.1'),  
		// 	// 晚餐后2小时血糖 
		// 	'8' => array ( '0' => '4','1' => '8.5'),  
		// 	// 睡前血糖
		// 	'9' => array ( '0' => '4','1' => '5.1'),  
		// );
		/*
			正常值范围变动
			变动时间：2017-05-24
			程序开发：王阳阳
			取值范围整理人：石华
		 */
		$result['gl3']	=	array(
			// 空腹血糖
			'1' => array ( '0' => '4.0','1' => '5.0'),
			// 早餐后2小时血糖
			'2' => array ( '0' => '4.0','1' => '8.4'),  
			// 随机血糖
			'3' => array ( '0' => '4.0','1' => '11.0'),  
			// 午餐前血糖
			'5' => array ( '0' => '4.0','1' => '5.0'),  
			// 午餐后2小时血糖
			'6' => array ( '0' => '4.0','1' => '8.4'),  
			// 晚餐前血糖
			'7' => array ( '0' => '4.0','1' => '5.0'),  
			// 晚餐后2小时血糖 
			'8' => array ( '0' => '4.0','1' => '8.4'),  
			// 睡前血糖
			'9' => array ( '0' => '4.0','1' => '5.0'),  
		);
	


		$result['ox']	=	array(
			// 血氧饱和度
			'saturation' => array ( '0' => '90','1' => '100'), 
			// 脉率
			'pr' 		 => array ( '0' => '60','1' => '100')  
		);

		$result['we']	=	array(
			// BMI
			'bmi' => array ( '0' => '18.5', '1' => '23.9'),
			// 体脂率 %
			'bf' => array (		
					'0' => array('0'=>'10','1'=>'20'),//男
					'1' => array('0'=>'18','1'=>'28'),//女
			),
			// 内脏脂肪指数
			'protein' => array ( '0' => '0', '1' => '9'  ), 
			// 去脂体重
			'fatweight' => array ( '0' => '41.6', '1' => '50.8' ),
			// 体重 
			'weight' => array (),
			// 基础代谢率
			'fat' => array ( '0' => '1268', '1' => '1467'  ), 
			// 骨量
			'mineralsalts' => array ( '0' => '2.1' ,'1' => '2.4'  ), 
			// 肌肉量
			'muscle' => array ( '0' => '39.2', '1' => '48.0' ), 
			// 身体总水分率 %
			'watercontentrate' => array ( '0' => '48.1', '1' => '57' )
		);

		$result['bp']	=	array(
			// 低压
			'lboodp' => array ( '0' => '60', '1' => '90' ), 
			// 高压
			'hboodp' => array ( '0' => '90', '1' => '140')  

		);


		// $result['bf']	=	array(
		// 	// 总胆固醇
		// 	'tc'  => array ( '0' => '5.18' ), 
		// 	// 甘油三酯
		// 	'tg'  => array ( '0' => '1.70' ), 
		// 	// 高密度
		// 	'htc' => array ( '0' => '1.04' ), 
		// 	// 低密度
		// 	'ltc' => array ( '0' => '3.37' )
		// );
		/*
			正常值范围变动
			变动时间：2017-05-24
			程序开发：王阳阳
			取值范围整理人：石华
		 */
		$result['bf']	=	array(
			// 总胆固醇
			'tc'  => array ( '0' => '5.17' ), 
			// 甘油三酯
			'tg'  => array ( '0' => '1.69' ), 
			// 高密度
			'htc' => array ( '0' => '1.04' ), 
			// 低密度
			'ltc' => array ( '0' => '3.37' )
		);


		$result['ur']	=	array(
			// 亚硝酸盐
			'nitrite' 		=> array ( '0' => '-' ), 
			// 尿胆原
			'urobilinogen'  => array ( '0' => '-' ), 
			// 白细胞
			'whitecells' 	=> array ( '0' => '-' ), 
			// 潜血
			'redcells' 		=> array ( '0' => '-' ), 
			// 尿蛋白
			'urineprotein'  => array ( '0' => '-' ), 
			// 尿酮
			'urineketone' 	=> array ( '0' => '-' ), 
			// 胆红素
			'bili'		 	=> array ( '0' => '-' ), 
			// 尿糖
			'sugar' 		=> array ( '0' => '-' ), 
			// 维生素C
			'vc' 			=> array ( '0' => '-' ), 
			// 酸碱度
			'ph' 	=> array ( '0' => '5', '1' => '7' ), 
			// 尿比重%
			'urine' => array ( '0' => '1.000', '1' => '1.005'), 
		);

		$result['el']	=	array(
			// 心率
			'bpm' => array ( '0' => '60', '1' => '100'   )  
		);

		$result['tm']	=	array(
			// 额温
			'tmv' => array ( '0' => '36.0', '1' => '37.2')
		);

		$result['bk']	=	array(
			// 血酮体
			'bk' => array ( '0' => '0.0', '1' => '0.3')
		);


		// $result['re']	=	array(
		// 	// 血尿酸
		// 	'suricacid' => array (		
		// 			'0' => array('0'=>'3.5','1'=>'7.0'),//男
		// 			'1' => array('0'=>'2.5','1'=>'6.0'),//女
		// 	),
		// );
		/*
			正常值范围变动
			变动时间：2017-05-24
			程序开发：王阳阳
			取值范围整理人：石华
		 */
		$result['re']	=	array(
			// 血尿酸
			'suricacid' => array (		
					'0' => array('0'=>'3.52','1'=>'7.06'),//男
					'1' => array('0'=>'2.52','1'=>'6.05'),//女
			),
		);
		$result['renalnew']	=	array(
			// 血尿酸(μmol/L)
			'suricacid' => array (		
					'0' => array('0'=>'210','1'=>'420'),//男
					'1' => array('0'=>'150','1'=>'360'),//女
			),
		);



		$result['um']	=	array(
			// 尿微量白蛋白
			'um' => array ( '0' => '0', '1' => '19')
		);

		if ( isset($result[$type]) && $result[$type] ) {
			return $result[$type];
		}

		return $result;
	}

}
