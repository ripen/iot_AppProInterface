<?php
/** 
 * 康宝8项正常检测值,及检测分析结果
 * 
 *  
 *  */
namespace Common\Common;

class checkkangbao {
	private $res;
	public function __construct($classname = __CLASS__) {
		
		return self::$classname;
	}	
	
	/**
	 * 血脂
	 */
	public function bloodfat($data = array()) {
		if( !$data ){
			return '';
		}
		
		$result		=	\Common\Analysisclass\result::factory()->bloodfat($data['tc'],$data['tg'],$data['ltc'],$data['htc']);

		if ( !$result ) {
			return false;
		}
		$datas			=	array();
		$datas['tc']	=	$data['tc'];
		$datas['tg']	=	$data['tg'];
		$datas['ltc']	=	$data['ltc'];
		$datas['htc']	=	$data['htc'];

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bloodfat($result,$datas);

		$keyArr		=	array( 
			'1'=>'tc', '2'=>'tg', 
			'3'=>'ltc', '4'=>'htc'
		);

		$msg 	=	array();
		foreach ($keyArr as $key => $value) {
			if(isset($result[$key])){
				//胆固醇
				$msg[$value]['status']	= $result[$key]['data']['status'];
				$msg[$value]['type']	= $result[$key]['data']['type']; ;
				$msg[$value]['0']		= $result[$key]['data']['0']; ;
				$msg[$value]['tests']	= $result[$key]['data']['tests'];
				$msg[$value][$value]	= $result[$key]['data'][$value];
				$msg[$value]['msg']		= $result[$key]['data']['msg'];
				$msg[$value]['extime']	= $data['examtime'] ? $data['examtime'] : '';
				//分析结果
				$msg[$value]['suggest'] = array(
					'title'    	=>	$result[$key]['title'],
					'state'		=>	$result[$key]['state'],
					'clinical'	=>	$result[$key]['clinical'],
					'result'	=>	$result[$key]['result'],
					'danger'	=>	$result[$key]['danger'],
					'nutrition'	=>	$result[$key]['nutrition'],
					'recovery'	=>	$result[$key]['recovery'],
				);
			}
		}

	  	return $msg;
	}
	/**
	 * 血压
	 */
	public function bloodp($data = array()) {
		if( !$data ){
			return '';
		}
		$result		=	\Common\Analysisclass\result::factory()->bloodp(
			$data['hboodp'],$data['lboodp']);

		if (!$result ) {
			return false;
		}

		$datas				=	array();
		$datas['hboodp']	=	$data['hboodp'];
		$datas['lboodp']	=	$data['lboodp'];

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bloodp($result,$datas);

		$msg['lboodp']['status'] 	= $result['lboodp']['status'];
		$msg['lboodp']['type']		= $result['lboodp']['type'];
		$msg['lboodp']['0']			= $result['lboodp']['0'];
		$msg['lboodp']['tests'] 	= $result['lboodp']['tests'];
		$msg['lboodp']['lboodp'] 	= $result['lboodp']['lboodp'];
		$msg['lboodp']['msg'] 		= $result['lboodp']['msg'];
		$msg['lboodp']['extime']	= $data['examtime'] ? $data['examtime'] : '';
		$msg['lboodp']['suggest'] = array(
				'title'    	=>	$result['title'],
				'state'		=>	$result['state'],
				'clinical'	=>	$result['clinical'],
				'result'	=>	$result['result'],
				'danger'	=>	$result['danger'],
				'nutrition'	=>	$result['nutrition'],
				'recovery'	=>	$result['recovery'],
		);

		$msg['hboodp']['status'] 	= $result['hboodp']['status'];
		$msg['hboodp']['type']		= $result['hboodp']['type'];
		$msg['hboodp']['0']			= $result['hboodp']['0'];
		$msg['hboodp']['tests'] 	= $result['hboodp']['tests'];
		$msg['hboodp']['hboodp'] 	= $result['hboodp']['hboodp'];
		$msg['hboodp']['msg'] 		= $result['hboodp']['msg'];
		$msg['hboodp']['extime']	= $data['examtime'] ? $data['examtime'] : '';
		$msg['hboodp']['suggest'] = array(
				'title'    	=>	$result['title'],
				'state'		=>	$result['state'],
				'clinical'	=>	$result['clinical'],
				'result'	=>	$result['result'],
				'danger'	=>	$result['danger'],
				'nutrition'	=>	$result['nutrition'],
				'recovery'	=>	$result['recovery'],
		);

		return $msg;
	}
	/**
	 * 心电
	 */
	public function electrocardio($data = array()) {
		if( !$data ){
			return '';
		}

		$arr = array (
				'hr' => array (
						'0' => '60',
						'1' => '100' 
				), // 心率
				'1' => array ()  // 心电图
				);

		$msg['el']	=	array(  'tests'  => $data['hr'],
										'msg' => '心电'
									 );
		$msg['bpm']	=	array(  'tests'  => $data['bpm'],
										'msg' => '心率'
									 );
		if (isset($data['image']) && $data['image'] && isset($data['type']) && $data['type'] == 1) {
			$msg['el']['image']	=	$data['image'];
		}else{
			$msg['el']['image']	=	'';
		}

		$msg['extime']=	$data['examtime'];

		$result		=	\Common\Analysisclass\result::factory()->el(
			$data['bpm'] );


		$datas			=	array();
		$datas['bpm']	=	$data['bpm'];

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::el($result,$datas);

		if ( $result ) {
			unset($msg['bpm']);
			$msg['bpm']['status']	= $result['data']['status'];
			$msg['bpm']['type']		= $result['data']['type']; ;
			$msg['bpm']['0']		= $result['data']['0']; ;
			$msg['bpm']['tests']	= $result['data']['tests'];
			$msg['bpm']['bpm']		= $result['data']['bpm'];
			$msg['bpm']['msg']		= $result['data']['msg'];
			$msg['bpm']['extime']	= $data['examtime'] ? $data['examtime'] : '';
			//分析结果
			$msg['bpm']['suggest'] = array(
				'title'    	=>	$result['title'],
				'state'		=>	$result['state'],
				'clinical'	=>	$result['clinical'],
				'result'	=>	$result['result'],
				'danger'	=>	$result['danger'],
				'nutrition'	=>	$result['nutrition'],
				'recovery'	=>	$result['recovery'],
			);
		}
		
		return $msg;
	}
	
	
	/**
	 * 体成分
	 */
	public function humanbody($data = array()) {

		if( !$data ){
			return '';
		}

		$member		=	\Kbox\Model\Member_detailModel::getsex($data['userid']);
		$data['sex']=	$member ? $member['sex'] : 0;

		$result		=	\Common\Analysisclass\result::factory()->humanbody($data['weight'],$data['bmi'],$data['bf'],
			$data['fatweight'],$data['protein'],
			$data['watercontentrate'],$data['muscle'],
			$data['mineralsalts'],$data['fat'],$data['sex']
		);

		
		if ( !$result ) {
			return false;
		}

		$keyArr		=	array('1'=>'weight','2'=>'bmi','3'=>'bf','4'=>'fatweight','5'=>'protein','6'=>'water','7'=>'muscle','8'=>'mineralsalts','9'=>'fat');

		$result		=	\Common\Analysisclass\handle::humanbody($result,$data);

		foreach ($keyArr as $key => $value) {
			if(isset($result[$key])){
				$msg[$value]['status']	= $result[$key]['data']['status'];
				$msg[$value]['type']	= $result[$key]['data']['type']; ;
				$msg[$value]['0']		= $result[$key]['data']['0']; ;
				$msg[$value]['tests']	= $result[$key]['data']['tests'];
				$msg[$value][$value]	= $result[$key]['data'][$value];
				$msg[$value]['msg']		= $result[$key]['data']['msg'];
				$msg[$value]['extime']	= $data['examtime'] ? $data['examtime'] : '';
				//分析结果
				$msg[$value]['suggest'] = array(
					'title'    	=>	$result[$key]['title'],
					'state'		=>	$result[$key]['state'],
					'clinical'	=>	$result[$key]['clinical'],
					'result'	=>	$result[$key]['result'],
					'danger'	=>	$result[$key]['danger'],
					'nutrition'	=>	$result[$key]['nutrition'],
					'recovery'	=>	$result[$key]['recovery'],
				);
			}
		}
	   	return $msg;	
	}


	/**
	 * 血氧
	 */
	public function oxygen($data = array()) {
		if( !$data ){
			return '';
		}
		
		$result		=	\Common\Analysisclass\result::factory()->oxygen($data['saturation'],$data['pr']);

		if ( !$result ) {
			return false;
		}

		$datas	=	array();
		$datas['saturation']	=	$data['saturation'];
		$datas['pr']			=	$data['pr'];
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::oxygen($result,$datas);

		$keyArr		=	array('1'=>'saturation','2'=>'pr');
		foreach ($keyArr as $key => $value) {
			if ($result['1']) {
				$msg[$value]['status']	= $result[$key]['data']['status'];
				$msg[$value]['type']	= $result[$key]['data']['type']; ;
				$msg[$value]['0']		= $result[$key]['data']['0']; ;
				$msg[$value]['tests']	= $result[$key]['data']['tests'];
				$msg[$value][$value]	= $result[$key]['data'][$value];
				$msg[$value]['msg']		= $result[$key]['data']['msg'];
				$msg[$value]['extime']	= $data['examtime'] ? $data['examtime'] : '';
				//分析结果
				$msg[$value]['suggest'] = array(
					'title'    	=>	$result[$key]['title'],
					'state'		=>	$result[$key]['state'],
					'clinical'	=>	$result[$key]['clinical'],
					'result'	=>	$result[$key]['result'],
					'danger'	=>	$result[$key]['danger'],
					'nutrition'	=>	$result[$key]['nutrition'],
					'recovery'	=>	$result[$key]['recovery'],
				);
			}
		}

		return $msg;
	}
	/**
	 * 体温
	 * type:0 正常,1:异常
	 */
	public function tm($data = array()) {
		if(!$data){
			return '';
		}
		$result		=	\Common\Analysisclass\result::factory()->tm($data['tmv']); 
		if ( !$result ) {
			return false;
		}
		$datas	=	array();
		$datas['tmv']	=	$data['tmv'];
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::tm($result,$datas);

		$msg['tm']['status']	= $result['data']['status'];
		$msg['tm']['type']		= $result['data']['type']; ;
		$msg['tm']['0']			= $result['data']['0']; ;
		$msg['tm']['tests']		= $result['data']['tests'];
		$msg['tm']['tmv']		= $result['data']['tmv'];
		$msg['tm']['msg']		= $result['data']['msg'];
		$msg['tm']['extime']	= $data['examtime'] ? $data['examtime'] : '';
		//分析结果
		$msg['tm']['suggest'] = array(
			'title'    	=>	$result['title'],
			'state'		=>	$result['state'],
			'clinical'	=>	$result['clinical'],
			'result'	=>	$result['result'],
			'danger'	=>	$result['danger'],
			'nutrition'	=>	$result['nutrition'],
			'recovery'	=>	$result['recovery'],
		);
		
		return $msg;
	}

	/**
	 * 尿11项
	 * type:0 正常,1:异常
	 * status 为 -1时:参数没有上升下降
	 */
	public function urine($data = array()) {
		if( !$data ){
			return '';
		}
		$result		=	\Common\Analysisclass\result::factory()->urine($data['nitrite'],$data['urobilinogen'],$data['whitecells'],$data['redcells'],$data['urineprotein'],$data['ph'],$data['urine'],$data['urineketone'],$data['bili'],$data['sugar'],$data['vc'] );

		if (!$result ) {
			return false;
		}

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::urine($result,$data);

		$msg 		=	array();

		$keyArr		=	array( 
			'1'=>'nitrite', '2'=>'urobilinogen', 
			'3'=>'whitecells', '4'=>'redcells', 
			'5'=>'urineprotein', '6'=>'ph', '7'=>'urine', 
			'8'=>'urineketone', '9'=>'bili', 
			'10'=>'sugar', '11'=>'vc'
		);

		foreach ($keyArr as $key => $value) {
			if (isset($result[$key])) {
				$msg[$value]['status']	= $result[$key]['data']['status'];
				$msg[$value]['type']	= $result[$key]['data']['type']; ;
				$msg[$value]['0']		= $result[$key]['data']['0']; ;
				$msg[$value]['tests']	= $result[$key]['data']['tests'];
				$msg[$value][$value]	= $result[$key]['data'][$value];
				$msg[$value]['msg']		= $result[$key]['data']['msg'];
				$msg[$value]['extime']	= $data['examtime'] ? $data['examtime'] : '';
				//分析结果
				$msg[$value]['suggest'] = array(
					'title'    	=>	$result[$key]['title'],
					'state'		=>	$result[$key]['state'],
					'clinical'	=>	$result[$key]['clinical'],
					'result'	=>	$result[$key]['result'],
					'danger'	=>	$result[$key]['danger'],
					'nutrition'	=>	$result[$key]['nutrition'],
					'recovery'	=>	$result[$key]['recovery'],
				);
			}
		}

		return $msg;	
	}

	/**
	 * 血糖
	 * $type:0 正常,1:异常
	 */
	public function bbsugar($data = array()) {
		if( !$data ){
			return '';
		}

		if ($data['attr'] == 0 || !$data['attr'] ) {
			$data['attr'] = 3 ;
		}

		if ( !$data['history'] ) {
			$data['history']	=	2;
		}

		if (!$data['bloodsugar']) {
			return false;
		}

		$result		=	\Common\Analysisclass\result::factory()->bsugar($data['bloodsugar'],$data['attr'],$data['history']);

		if ( !$result ) {
			return false;
		}

		$datas	=	array();
		$datas['bloodsugar']	=	$data['bloodsugar'];
		$datas['attr']			=	$data['attr'];
		
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bsugar($result,$datas);

		if ( !$result ) {
			return false;
		}

		$msg 	=	array();
		$msg['bloodsugar']['status'] 		= $result['data']['status'];
		$msg['bloodsugar']['type'] 			= $result['data']['type']; ;
		$msg['bloodsugar']['0'] 			= $result['data']['0']; ;
		$msg['bloodsugar']['tests'] 		= $result['data']['tests'];
		$msg['bloodsugar']['bloodsugar'] 	= $result['data']['bloodsugar'];
		$msg['bloodsugar']['msg'] 			= $result['data']['title'];
		$msg['bloodsugar']['attr'] 			= $result['data']['attr'];
		$msg['bloodsugar']['extime']		= $data['examtime'] ? $data['examtime'] : '';
		//分析结果
		$msg['bloodsugar']['suggest'] = array(
			'title'    	=>	$result['title'],
			'state'		=>	$result['state'],
			'clinical'	=>	$result['clinical'],
			'result'	=>	$result['result'],
			'danger'	=>	$result['danger'],
			'nutrition'	=>	$result['nutrition'],
			'recovery'	=>	$result['recovery'],
		);

		return $msg;
	}

	/**
	 * 血酮
	 * @param  array  $data 血酮检测数据信息
	 * @return array  返回血酮分析结果数据
	 */
	public function bloodketone( $data = array() ) {
		if( !$data ){
			return '';
		}

		$result		=	\Common\Analysisclass\result::factory()->bloodketone($data['bk']); 
		if ( !$result ) {
			return false;
		}
		$datas	=	array();
		$datas['bk']	=	$data['bk'];
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bloodketone($result,$datas);

		$msg['bk']['status']	= $result['data']['status'];
		$msg['bk']['type']		= $result['data']['type']; ;
		$msg['bk']['0']			= $result['data']['0']; ;
		$msg['bk']['tests']		= $result['data']['tests'];
		$msg['bk']['bk']		= $result['data']['bk'];
		$msg['bk']['msg']		= $result['data']['msg'];
		$msg['bk']['extime']	= $data['examtime'] ? $data['examtime'] : '';
		//分析结果
		$msg['bk']['suggest'] = array(
			'title'    	=>	$result['title'],
			'state'		=>	$result['state'],
			'clinical'	=>	$result['clinical'],
			'result'	=>	$result['result'],
			'danger'	=>	$result['danger'],
			'nutrition'	=>	$result['nutrition'],
			'recovery'	=>	$result['recovery'],
		);
		
		return $msg;
	}


	/**
	 * 血尿酸
	 * @param  array  $data 血尿酸检测数据信息
	 * @return array  返回血尿酸分析结果数据
	 */
	public function renal( $data = array() ) {
		if( !$data ){
			return '';
		}
		
		$member		=	\Kbox\Model\Member_detailModel::getsex($data['userid']);
		$data['sex']=	$member ? $member['sex'] : 0;

		$result		=	\Common\Analysisclass\result::factory()->renal($data['suricacid'],$data['sex']); 

		if ( !$result ) {
			return false;
		}
		$datas	=	array();
		$datas['suricacid']	=	$data['suricacid'];
		$datas['sex']		=	$data['sex'];
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::renal($result,$datas);
		$msg['suricacid']['status']	= $result['data']['status'];
		$msg['suricacid']['type']		= $result['data']['type']; ;
		$msg['suricacid']['0']			= $result['data']['0']; ;
		$msg['suricacid']['tests']		= $result['data']['tests'];
		$msg['suricacid']['suricacid']		= $result['data']['suricacid'];
		$msg['suricacid']['msg']		= $result['data']['msg'];
		$msg['suricacid']['extime']	= $data['examtime'] ? $data['examtime'] : '';
		//分析结果
		$msg['suricacid']['suggest'] = array(
			'title'    	=>	$result['title'],
			'state'		=>	$result['state'],
			'clinical'	=>	$result['clinical'],
			'result'	=>	$result['result'],
			'danger'	=>	$result['danger'],
			'nutrition'	=>	$result['nutrition'],
			'recovery'	=>	$result['recovery'],
		);
		
		return $msg;
	}


	/**
	 * 尿微量白蛋白
	 * @param  array  $data 尿微量白蛋白检测数据信息
	 * @return array  返回尿微量白蛋白分析结果数据
	 */
	public function umprotein( $data = array() ) {
		if( !$data ){
			return '';
		}

		$result		=	\Common\Analysisclass\result::factory()->umprotein($data['um']); 
		if ( !$result ) {
			return false;
		}
		$datas	=	array();
		$datas['um']	=	$data['um'];
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::umprotein($result,$datas);

		$msg['um']['status']	= $result['data']['status'];
		$msg['um']['type']		= $result['data']['type']; ;
		$msg['um']['0']			= $result['data']['0']; ;
		$msg['um']['tests']		= $result['data']['tests'];
		$msg['um']['um']		= $result['data']['um'];
		$msg['um']['msg']		= $result['data']['msg'];
		$msg['um']['extime']	= $data['examtime'] ? $data['examtime'] : '';
		//分析结果
		$msg['um']['suggest'] = array(
			'title'    	=>	$result['title'],
			'state'		=>	$result['state'],
			'clinical'	=>	$result['clinical'],
			'result'	=>	$result['result'],
			'danger'	=>	$result['danger'],
			'nutrition'	=>	$result['nutrition'],
			'recovery'	=>	$result['recovery'],
		);
		
		return $msg;
	}

}