<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 心电数据管理
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ElModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 心电列表数据
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
		$map['status']	=	1;
		$db 	=	M('kangbao_electrocardio');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,hr,bpm,examtime,cardid')->where($map)->limit($curpage.','.$pagesize)->order('createtime desc')->select();
		
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

		$map 			=	array();
		$map['cardid']	=	$cardid;
		$map['status']	=	1;

		$db 	=	M('kangbao_electrocardio');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,hr,bpm,examtime,cardid')->where($map)->limit($curpage.','.$pagesize)->order('createtime desc')->select();
		
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
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getinfo( $id = 0 ){
		if ( !$id ) {
			return false;
		}

		$info 	=	M('kangbao_electrocardio')->where(array('id'=>$id))->find();
		return $info ? $info : false;
	}

	/**
	 * 获取解读信息
	 * 
	 * @param  integer $id 检测数据表ID
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getanswer( $id = 0 ){
		if ( !$id ) {
			return false;
		}
		$result =	array();

		// 查询解读信息
		$map 	=	array();
		$map['qid']	=	$id;
		$info 	=	M('kangbao_electoranswer')->where($map)->find();

		if ( !$info ) {
			return false;
		}

		// 获取参考图
		$imginfo 	=	array();
		if ( $info['imgid'] ) {
			$imginfo 	=	M('elimg')->where(array('id'=>$info['imgid']))->find();
		}
		
		$result['content']	=	$info['content'];
		$result['addtime']	=	$info['addtime'];
		$result['thumb']	=	$imginfo ? $imginfo['thumb'] : '';
		$result['title']	=	$imginfo ? $imginfo['title'] : '';
		
		return $result;
	}

}