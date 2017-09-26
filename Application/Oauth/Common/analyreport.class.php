<?php
namespace Oauth\Common;

/**
 * 检测报告数据处理
 * 
 * 
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class analyreport {

	public  $phpredis;
	
	private $retime	=	array('resources','userinfo');//每个分项的整项报告,检测时间

	private	$reel	=	array('resources');// 每个分项的整项报告

	private	$namearray	   =	array('gl'=>'血糖','ox'=>'血氧','tm'=>'体温','we'=>'体成分','bp'=>'血压','bf'=>'血脂','ur'=>'尿常规','el'=>'心电','8'=>'resources','bk'=>'血酮','re'=>'血尿酸','um'=>'尿微量白蛋白');//转换名称

	private $names 			=	array('gl','ox','tm','we','bf','bp','ur','el','bk','re','um');

	public function __construct( $classname = __CLASS__ ){
		
	}

	/**
	 * 获取检测报告
	 * 	读取最新的报告信息
	 * 
	 * @param  integer $userid 用户id
	 * @param  bool $elshow 是否显示心电原始数据，默认为不显示
	 * @return 
	 */
	public function report( $userid = 0 , $elshow = true){
		if ( !$userid ) {
			return false;
		}

		$this->phpredis = new \Common\Common\phpredis();
		$data	=	array();
		$data	=	$this->reportredis($userid);

		if ( !$data ) {
			$data 	=	$this->reportmysql($userid);
		}

		if ( !$data ) {
			return false;
		}


		$result		=	$this->handle($data);
		if ( $elshow ) {
			$result =	$this->doel($result);
		}

		// 获取用户信息
		if (isset($data['userinfo']) && $data['userinfo'] ) {
			$userinfo	=	$data['userinfo'];
		}else{
			$userinfo 	=	$this->userinfo($userid);
		}
		$result['userinfo']	=	$userinfo ? $userinfo : array();

		return $result;
	}

	/**
	 * 获取检测报告
	 * 	读取历史报告信息
	 * 
	 * @param  integer $userid 用户id
	 * @param  integer $id 报告表id
	 * @param  bool $elshow 是否显示心电原始数据，默认为不显示
	 * @return 
	 */
	public function reporttable( $id = 0 , $elshow = true ){
		if ( !$id ) {
			return false;
		}

		$data 	=	$this->reportmysqlid($id);
		
		if ( !$data ) {
			return false;
		}

		$result		=	$this->handle($data);
		if ( $elshow ) {
			$result =	$this->doel($result);
		}

		// 获取用户信息
		if (isset($data['userinfo']) && $data['userinfo'] ) {
			$userinfo	=	$data['userinfo'];
		}else{
			$userinfo 	=	$this->userinfo($data['userid']);
		}
		$result['userinfo']	=	$userinfo ? $userinfo : array();
		
		return $result;
	}

	/**
	 * 处理心电数据表格展示问题
	 * 	心电数据需要特殊处理
	 * 
	 * @return [type] [description]
	 */
	private function doel( $result ){
		if ( !$result ) {
			return false;
		}
		if (isset($result['table']['el']) && $result['table']['el']['el']) {
			unset($result['table']['el']['el']);
		}
		return $result;
	}

	/**
	 * 处理 redis 或者mysql 中获取到的报告信息
	 * @param  array  $data 
	 * @return array
	 */
	private function handle( $data = array() ){
		if ( !$data ) {
			return false;
		}

		$result 	=	array();
		//	总共检测了几项
		$result['num']	=	$this->getnum($data);
		$result['check']=	$this->checkname($data);

		// 判断是否存在异常项目
		$result['type']	=	$this->checkexception($data);

		// 获取异常项
		$result['excearr']	=	$this->getexceptions($data);

		// 计算异常项目个数
		$result['exccount']	=	0;
		if ( $result['excearr'] ) {
			$exccount	=	0;
			foreach ( $result['excearr'] as $key => $value) {
				if ( $value['list'] ) {
					$exccount	+=	count($value['list']);
				}
			}
			$result['exccount']	=	$exccount;
		}
		ksort($data['resources'],SORT_NUMERIC );

		$result['resources']	=	$data['resources'] ? $data['resources'] : array();
		
		$result['extime']		=	$data['extime'];
		$result['reportcode']	=	$data['reportcode'];

		// 评估结果综述
		$tables		=	$this->tables($data);
		$result['table']	=	$tables['result'] ? $tables['result'] : '';
		$result['cardinfo']	=	$tables['cardinfo'] ? $tables['cardinfo'] : '';
		return $result;
	}

	/**
	 * 获取用户信息
	 *
	 * 
	 * @return array
	 */
	private function userinfo( $userid ){
		if ( !$userid || !is_numeric($userid) ) {
			return false;
		}
		$where 	=	array('userid'=>$userid);
		$info 	=	M('member')->field('userid,username,nickname')->where($where)->find();
		$info2 	=	M('member_detail')->field('sex,height,birthday')->where($where)->find();

		if ( !$info && !$info2 ) {
			return false;
		}

		$result =	array_merge($info,$info2);
		$result['sex']	=	$result['sex'] == 0 ? '男' : '女';
		$result['age']	=	$result['birthday'] ? age($result['birthday']) : '';

		return $result;
	}

	/**
	 * 评估结果综述
	 * 	
	 * @param  array  $data 检测项
	 * @return 
	 */
	private function tables( $data = array() ){
		if ( !$data ) {
			return false;
		}
		
		$result 	=	array();
		foreach ($data as $key => $value) {
			if( !is_array($value) || !in_array($key,$this->names) ) {
				continue;
			}

			foreach ( $value as $k => $v ) {
				if ( !is_array($v) ) {
					continue;
				}

				// 隐藏掉去脂体重 BEGIN
				if ($key == 'we' && $k == 'fatweight') {
					continue;
				}
				// 隐藏掉去脂体重 END

				if ($k == 'report' && $key == 'el' ) {
					$result[$key]['el']['report']	=	$v;
				}else{
					$result[$key][$k]	=	array(
						'name' 		=> $v['msg'],
						'datas'		=> $v['tests'],
						'range'		=> $v['0'],
						'status' 	=> $v['status']
					);
				}

				if ($k == 'el' && $key == 'el') {
					$result[$key][$k]['image']	=	$v['image'] ? $v['image'] : '';
				}
				
			}
			$result[$key]['extime']	=	$value['extime'];

			$cardid 	=	$value['cardid'];
		}

		if ( $cardid ) {
			$cardinfo 	=	M('drug_card_user')->where(array('id'=>$cardid))->field('wholecard')->find();
		}

		$return 	=	array();
		$return['cardinfo']	=	$cardinfo ? $cardinfo['wholecard'] : '';
		$return['result']	=	$result;
		return $return;
	}


	/**
	 * 检测项目有多少个
	 * 	传入8项检测数组 ,数组存在就+1
	 * @param  array  $data 检测项
	 * @return integer 返回个数
	 */
	private function  getnum( $data =array() ){
		$num = 0;
	
		$arr = array();
		foreach( $data as $k=>$v ){
			if( is_array($v) && !in_array($k,$this->retime)){
				$num++;
			}
		}
		return $num;
	}


	/**
	 * 传入8项检测数组 ,是否存在异常
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function checkexception($data =array()){
		if( !$data ){
			return false;
		}
		
		$type =	0;
		foreach( $data as $k => $v ){
			if($v){
				foreach($v as $key=>$val){
					// 隐藏掉去脂体重 BEGIN
					if ($k == 'we' && $key == 'fatweight') {
						continue;
					}
					// 隐藏掉去脂体重 END


					if( isset($val['type']) && $val['type'] ){
						$type = 1;
						break;
					}
				}
			}
			if($type){
				break;
			}
		}
		return $type ;

	}


	/**
	 * 获取检测8项中的异常项
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	private function getexceptions( $data =array() ){
		if( !$data ){
			return false;
		}

		$arr = array();
		foreach( $data as $k => $v ){
			if( $v ){
				
				foreach($v as $key=>$val){
					if( is_array($val) ){

						// 隐藏掉去脂体重 BEGIN
						if ($key == 'fatweight' && $k == 'we') {
							continue;
						}
						// 隐藏掉去脂体重 END

						if($val['type'] && !in_array($k,$this->reel) && $val['suggest'] && is_array($val['suggest'])){
							// 获取分类中文名称
							$arr[$k]['name']	=	isset($this->namearray[$k]) ?	$this->namearray[$k]	:	'';
							//异常列表
							$arr[$k]['list'][] 	=	$val;
						}
					}
				}
			}
		}
		return $arr;
	}


	/**
	 * 获取检测了哪些项目
	 * @param  array  $data 
	 * @return string
	 */
	private function checkname( $data =array() ){
		if( !$data ){
			return false;
		}
		
		$str = '';
		foreach( $data as $k => $v ){
			if( !in_array($k,$this->reel) && $v && is_array($v) ){

				$temp	=	isset($this->namearray[$k]) ?	$this->namearray[$k]	:	'';
				$str 	.=	$temp ? $temp.' ' : '';	
			}
		}
		return $str;
	}

	/**
	 * 检测报告---读取redis记录
	 *
	 * 
	 * @param  integer $userid 用户id
	 * @return 
	 */
	private function reportredis( $userid = 0 ){
		if ( !$userid ) {
			return false;
		}

		$data 		=	array();
		// 读取redis 获取用户最新的报告信息
		$keyname	=	$this->phpredis->createkeyname('report',$userid);
		$data		=	array();
		$data		=	$this->phpredis->formatdataget($keyname);

		return $data ? $data : false;
	}


	/**
	 * 检测报告---读取mysql记录
	 *
	 * 
	 * @param  integer $userid 用户id
	 * @return 
	 */
	private function reportmysql($userid = 0){
		if ( !$userid ) {
			return false;
		}
		$data 	=	array();
		$data 	=	M('kangbao_report')->where(array('userid'=>$userid))->order('id desc')->find();

		if (!$data ) {
			return false;
		}

		$result 	=	json_decode(unserialize($data['data']),true);
		
		$result['reportcode']	=	$data['reportcode'];
		
		return $result;
	}


	/**
	 * 检测报告---读取mysql记录
	 *
	 * 
	 * @param  integer $id 报告表id
	 * @return 
	 */
	private function reportmysqlid( $id = 0 ){
		if ( !$id ) {
			return false;
		}
		$data 	=	array();
		$data 	=	M('kangbao_report')->where(array('id'=>$id))->find();

		if (!$data ) {
			return false;
		}

		$result 	=	json_decode(unserialize($data['data']),true);
		
		$result['reportcode']	=	$data['reportcode'];
		$result['userid']		=	$data['userid'];
		$result['userinfo']		=	$data['userinfo'] ? unserialize($data['userinfo']) : '';
		return $result;
	}
}
