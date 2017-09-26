<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 检测报告
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ReportModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 血糖列表数据
	 * @param  array   $drugs    药店ID
	 * @param  integer $p        当前页
	 * @param  integer $pagesize 每页显示条数
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function lists( $drugs = array(),$p = 1 , $pagesize = 10 ){
		if ( !$drugs || !is_array($drugs) ) {
			return false;
		}

		$curpage=	( $p - 1 ) * $pagesize;

		$map 	=	array();
		$map['drugid']	=	array('in',implode(',',$drugs));

		$db 	=	M('kangbao_report');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,data,extime,cardid')->where($map)->limit($curpage.','.$pagesize)->order('id desc')->select();
		
		$info 	=	$this->handlelist($info);

		$data 	=	array();
		$data['p']			=	$p;
		$data['pagesize']	=	$pagesize;
		$data['total']		=	$total;
		$data['info']		=	$info;

		return $data;
	}

	/**
	 * 通过卡号ID查询所有检测数据
	 * 
	 * @param  integer $cardid   卡号ID
	 * @param  integer $p        当前页
	 * @param  integer $pagesize 每页显示条数
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function listsbycard( $cardid = 0,$p = 1 , $pagesize = 10){
		if ( !$cardid || !is_numeric($cardid) ) {
			return false;
		}

		$curpage=	( $p - 1 ) * $pagesize;

		$map 	=	array();
		$map['cardid']	=	$cardid;

		$db 	=	M('kangbao_report');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,data,extime')->where($map)->limit($curpage.','.$pagesize)->order('id desc')->select();
		// 处理进食状态
		$info	=	$this->handlelist($info,false);
		
		$data 	=	array();
		$data['p']			=	$p;
		$data['pagesize']	=	$pagesize;
		$data['total']		=	$total;
		$data['info']		=	$info;

		return $data;
	}

	/**
	 * 获取检测数据
	 * 
	 * @param  integer $id 检测数据表ID
	 * @param  string $field 显示字段
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getinfo( $id = 0 ){
		if ( !$id ) {
			return false;
		}

		$info 	=	M('kangbao_report')->where(array('id'=>$id))->find();

		return $info ? $info : false;
	}


	/**
	 * 列表处理得到的数据
	 * @param  array  $data 检测数据
	 * @param  bool 	$showcard 是否显示卡号
	 * @return [type]       [description]
	 */
	public function handlelist( $data = array() , $showcard = true ){
		if ( !$data ) {
			return false;
		}

		$result 	=	array();

		foreach ($data as $key => $value) {
			$result[$key]['id']	=	$value['id'];
			$result[$key]['extime']	=	$value['extime'];

			if ($showcard) {
				$result[$key]['cardid']	=	$value['cardid'];
			}
			
			$temp 	=	unserialize($value['data']);
			$temp 	=	$temp ? json_decode($temp,true) : array();
			
			

			// 查询当次都检测了那些项目
			$result[$key]['gl']	=	isset($temp['gl']) ? 1 : 0;
			$result[$key]['bf']	=	isset($temp['bf']) ? 1 : 0;
			$result[$key]['bp']	=	isset($temp['bp']) ? 1 : 0;
			$result[$key]['ox']	=	isset($temp['ox']) ? 1 : 0;
			$result[$key]['el']	=	isset($temp['el']) ? 1 : 0;
			$result[$key]['we']	=	isset($temp['we']) ? 1 : 0;
			$result[$key]['ur']	=	isset($temp['ur']) ? 1 : 0;

			$result[$key]['gltype']	=	'';
			$result[$key]['glupdate']	=	0;
			$result[$key]['glattr']		=	'';
			// 判断是否可以修改血糖进食状态
			if (isset($temp['gl']) && $temp['gl']['extime'] ) {
				$extime 	=	date('Y-m-d',$temp['gl']['extime']);
				$result[$key]['gltype']	=	$temp['gl']['bloodsugar']['msg'];
				$result[$key]['glattr']	=	$temp['gl']['bloodsugar']['attr'];

				if ( $extime == date('Y-m-d',time()) ) {
					$result[$key]['glupdate']	=	1;
				}
			}
		}

		return $result ? $result : false;
	}


	/**
	 * 判断结果是否含有血糖检测项
	 * @param  array  $data 检测数据
	 * @return [type]       [description]
	 */
	public function checkgl( $data = array() ){
		if ( !$data ) {
			return false;
		}

		$result 	=	array();

		
		$temp 	=	unserialize($data);
		$temp 	=	$temp ? json_decode($temp,true) : array();
		
		// 查询当次都检测了那些项目
		$result['gl']	=	isset($temp['gl']) ? 1 : 0;
		$result['insertid']	=	$temp['gl']['insertid'];

		return $result ? $result : false;
	}


	/**
	 * 更新血糖进食状态
	 * @param  integer $glid 血糖数据ID
	 * @param  string  $attr 进食状态
	 * @return 
	 */
	public function upAttr( $glid = 0 , $attr = '' ){
		if ( !$glid || !$attr ) {
			return false;
		}
		$where 			= 	array();
		$where['id'] 	= 	$glid;

		$data 			= 	array();
		$data['attr'] 	= 	$attr;
		$result = M("kangbao_bbsugar") -> where($where) -> save($data);
		
		$where 			= 	array();
		$where['insertid'] 	= 	$glid;

		$data 				= 	array();
		$data['status'] 	= 	0;
		$data['type']		=	'gl';

		$re 	= 	M("kangbao_queue") -> where($where) -> save($data);
		
		return true ;
	}
}