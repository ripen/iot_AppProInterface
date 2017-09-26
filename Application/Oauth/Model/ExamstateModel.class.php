<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 体检进程
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ExamstateModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 体检进程列表数据
	 * @param  array   $drugs    药店ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function lists( $drugs = array() ){
		if ( !$drugs || !is_array($drugs) ) {
			return false;
		}
		
		$map 	=	array();
		$map['drugid']	=	array('in',implode(',',$drugs));

		$db 	=	M('examstate');

		$info 	=	$db->field('id,gl,bp,ox,ur,bf,el,we,status,time,personid')->where($map)->order('id desc')->select();
		
		
		return $info;
	}

	
	/**
	 * 体检进程人体列表数据
	 * @param  array   $drugs    药店ID
	 * @param  integer $p        当前页
	 * @param  integer $pagesize 每页显示条数
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function listsbody( $drugs = array() ,$p = 1 , $pagesize = 10 ){
		if ( !$drugs || !is_array($drugs) ) {
			return false;
		}

		$curpage=	( $p - 1 ) * $pagesize;

		$map 	=	array();
		$map['drugid']	=	array('in',implode(',',$drugs));

		$db 	=	M('examstate');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('time,personid')->where($map)->limit($curpage.','.$pagesize)->order('id desc')->select();
		
		$data 	=	array();
		$data['p']			=	$p;
		$data['pagesize']	=	$pagesize;
		$data['total']		=	$total;
		$data['info']		=	$info;

		return $data;
	}

	/**
	 * 获取检测进程详情
	 * 
	 * @param  integer $id ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getinfo( $id = '' ){
		if ( !$id ) {
			return false;
		}

		$info 	=	M('examstate')->field('id,drugid')->where(array('id'=>$id))->find();
		return $info ? $info : false;
	}

	/**
	 * 结束进程
	 * 
	 * @param  integer $id ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function finish( $id = 0 ){
		if ( !$id ) {
			return false;
		}

		$where 			=	array();
		$where['id']	=	$id;
		$info 			=	M('examstate')->where($where)->delete();

		return $info ? true : false;
	}

}