<?php
namespace Analysis\Controller;
use Think\Controller;


/**
 * 健康数据分析处理
 *
 * 	流程：
 *  	1、读取数据库
 *  	2、对数据进行分析，对指定的分析结果进行范围设定
 *  	3、根据设定的范围，生成对应的redis键值
 *   	4、分析结果包含：取值范围、正异常区分、检测状态、检测值、
 *   		检测值（包含单位）、检测名称、临床意义、结果及建议、危险因素
 *  	5、将分析结果存入到redis中
 * 
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class IndexController extends Controller {
	
	public $datamodel;

	public $redis;

 	public function __construct(){
		parent::__construct();

		$this->datamodel	=	new \Analysis\Model\AnalysisModel();

		$this->redis 		=	new \Common\Common\phpredis();
	}
	

	/**
	 * 循环处理
	 *
	 * @author  wangyangyang
	 * @version V1.0
	 */
	public function index(){
		// 血糖
		$this->gl();
		usleep(100000);

		// 血氧
		$this->ox();
		usleep(100000);
		
		// 恒温
		$this->tm();
		usleep(100000);

		// 血压
		$this->bp();
		usleep(100000);

		// 血脂
		$this->bf();
		usleep(100000);

		// 体成分
		$this->we();
		usleep(100000);

		// 尿常规
		$this->ur();
		usleep(100000);

		// 心电---心率
		$this->el();
		usleep(100000);

		// 血酮
		$this->bk();
		usleep(100000);

		// 血尿酸
		$this->re();
		usleep(100000);
		
		// 尿微量白蛋白
		$this->um();
		usleep(100000);

		// 血尿酸
		$this->renew();
		usleep(100000);

		// 整体大项
		$this->resources();


		exit('OK');
	}

	/**
	 * 血糖数据处理
	 * 
	 * 	影响因素：测试时间，糖尿病史
	 *
	 * 	redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:gl
	 *  	2、生成分析结果,键值：anslysis:data:gl:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围,进食状态、
	 *  		病史
	 * 
	 * 
	 */
	private function gl(){
		$data 	=	$this->datamodel->gl();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array( 
				'min'		=> $value['min'],
				'max'		=> $value['max'],
				'times'		=> $value['times'],
				'history'	=> $value['history']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:gl:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:gl';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 血氧数据处理
	 * 
	 * 	影响因素：暂无
	 *
	 * redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:ox
	 *  	2、生成分析结果,键值：anslysis:data:ox:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围
	 *
	 * 
	 */
	private function ox(){
		$data 	=	$this->datamodel->ox();
		if ( !$data ) {
			return false;
		}
		
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['types']][$value['id']]	=	array(
				'min'=>$value['min'],
				'max'=>$value['max'],
				'types'=>$value['types']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:ox:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:ox';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 恒温数据处理
	 * 
	 * 	影响因素：暂无
	 *  
	 *  redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:tm
	 *  	2、生成分析结果。键值：anslysis:data:tm:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围
	 * 
	 * 
	 */
	private function tm(){
		$data 	=	$this->datamodel->tm();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array('min'=>$value['min'],'max'=>$value['max']);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:tm:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:tm';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 血压数据处理
	 * 
	 * 	影响因素：暂无
	 *  
	 *  redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:bp
	 *  	2、生成分析结果。键值：anslysis:data:bp:***
	 *  	3、范围redis记录为二维数组，记录高压最小值，高压最大值，
	 *  		低压最小值，低压最大值
	 * 
	 * 
	 */
	private function bp(){
		$data 	=	$this->datamodel->bp();
		if ( !$data ) {
			return false;
		}
		
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array(
				'hmin' => $value['hmin'],
				'hmax' => $value['hmax'],
				'lmin' => $value['lmin'],
				'lmax' => $value['lmax']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:bp:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:bp';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 血脂数据处理
	 * 
	 * 	影响因素：暂无
	 *  
	 *  redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:bf
	 *  	2、生成分析结果。键值：anslysis:data:bf:***
	 *  	3、范围redis记录为二维数组(血脂检测项目为多项的，生成的结果
	 *  		中先按照types区分，然后在存储最小值，最大值范围)
	 * 
	 */
	private function bf(){
		$data 	=	$this->datamodel->bf();
		if ( !$data ) {
			return false;
		}
		
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['types']][$value['id']]	=	array(
				'min' 	=> $value['min'],
				'max' 	=> $value['max'],
				'types' => $value['types']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:bf:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:bf';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}

	/**
	 * 体成分数据处理
	 * 
	 * 	影响因素：体脂率（％）区分男女性别
	 *  
	 *  redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:we
	 *  	2、生成分析结果。键值：anslysis:data:we:***
	 *  	3、范围redis记录为二维数组(体成分检测项目为多项的，生成的结果
	 *  		中先按照types区分，然后在存储最小值，最大值范围)
	 * 
	 */
	private function we(){
		$data 	=	$this->datamodel->we();
		if ( !$data ) {
			return false;
		}
		
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['types']][$value['id']]	=	array(
				'min' 	=> $value['min'],
				'max' 	=> $value['max'],
				'types' => $value['types'],
				'sex'   => $value['sex']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:we:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:we';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}

	/**
	 * 尿常规数据处理
	 * 	尿常规传递的数值为：
	 * 		亚硝酸盐 尿胆原 白细胞 潜血 尿蛋白 尿酮 胆红素 尿糖 维生素C（
	 * 		为 - 或者 + ）
	 * 		酸碱度 尿比重为数值
	 * 	影响因素：阴性，阳性区分
	 *  
	 *  redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:ur
	 *  	2、生成分析结果。键值：anslysis:data:ur:***
	 *  	3、范围redis记录为二维数组(体成分检测项目为多项的，生成的结果
	 *  		中先按照types区分，然后在存储最小值，最大值范围)
	 * 
	 */
	private function ur(){
		$data 	=	$this->datamodel->ur();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['types']][$value['id']]	=	array(
				'min' 	=> $value['min'],
				'max' 	=> $value['max'],
				'types' => $value['types'],
				'division' => $value['division']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:ur:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:ur';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 心电数据处理
	 * 	心电传递的数值为：
	 * 		解析心率
	 * 	影响因素：暂无
	 *  
	 *  redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:el
	 *  	2、生成分析结果。键值：anslysis:data:el:***
	 *  	3、范围redis记录为二维数组(体成分检测项目为多项的，生成的结果
	 *  		中先按照types区分，然后在存储最小值，最大值范围)
	 * 
	 */
	private function el(){
		$data 	=	$this->datamodel->el();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array( 
				'min'		=> $value['min'],
				'max'		=> $value['max']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:el:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:el';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 血酮数据处理
	 * 
	 * 	redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:bk
	 *  	2、生成分析结果,键值：anslysis:data:bk:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围,进食状态、
	 *  		病史
	 * 
	 * 
	 */
	private function bk(){
		$data 	=	$this->datamodel->bk();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array( 
				'min'		=> $value['min'],
				'max'		=> $value['max']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:bk:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:bk';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}


	/**
	 * 血尿酸数据处理
	 * 
	 * 	redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:re
	 *  	2、生成分析结果,键值：anslysis:data:re:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围,进食状态、
	 *  		病史
	 * 
	 * 
	 */
	private function re(){
		$data 	=	$this->datamodel->re();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array(
				'min' 	=> $value['min'],
				'max' 	=> $value['max'],
				'sex'	=> $value['sex'],
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:re:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:re';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}



	/**
	 * 血尿酸数据处理μmol/L
	 * 
	 * 	redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:re
	 *  	2、生成分析结果,键值：anslysis:data:re:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围,进食状态、
	 *  		病史
	 * 
	 * 
	 */
	private function renew(){
		$data 	=	$this->datamodel->renew();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array(
				'min' 	=> $value['min'],
				'max' 	=> $value['max'],
				'sex'	=> $value['sex'],
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:renew:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:renew';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}

	/**
	 * 尿微量白蛋白数据处理
	 * 
	 * 	redis键值说明：
	 *  	1、生成范围redis ，键值：anslysis:range:um
	 *  	2、生成分析结果,键值：anslysis:data:um:***
	 *  	3、范围redis记录为二维数组，记录最小值，最大值范围,进食状态、
	 *  		病史
	 * 
	 * 
	 */
	private function um(){
		$data 	=	$this->datamodel->um();
		if ( !$data ) {
			return false;
		}
		// 范围
		$rangeArr	=	array();
		// 详情
		$info 		=	array();
		foreach ($data as $key => $value) {
			// 范围写入到redis中
			$rangeArr[$value['id']]	=	array(
				'min' 	=> $value['min'],
				'max' 	=> $value['max']
			);

			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:data:um:'.$value['id'];
			$this->redis->formatdataset($rangeKey,$value);
		}

		$redisKey	=	'anslysis:range:um';
		$this->redis->formatdataset($redisKey,$rangeArr);

		return true;
	}

	/**
	 * 整体大项
	 *  
	 *  redis键值说明：
	 *  	1、生成结果redis ，键值：anslysis:resources:***
	 *  	
	 * 
	 */
	private function resources(){
		$data 	=	$this->datamodel->resources();
		if ( !$data ) {
			return false;
		}
		
		foreach ($data as $key => $value) {
			// 分析结果写入到redis中
			$rangeKey 	=	'anslysis:resources:'.$value['types'];

			$result 	=	array();
			$result['types']	=	$value['types'];
			$result['result']	=	$value['result'];

			$this->redis->formatdataset($rangeKey,$result);
		}

		return true;
	}
}