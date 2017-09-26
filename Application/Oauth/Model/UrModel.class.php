<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 尿常规数据管理
 * 
 * @author wangyangyang
 * @version V1.0
 */
class UrModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 尿常规列表数据
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

		$db 	=	M('kangbao_urine');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,urobilinogen,nitrite,whitecells,redcells,urineprotein,ph,urine,urineketone,bili,sugar,vc,cardid,examtime')->where($map)->limit($curpage.','.$pagesize)->order('createtime desc')->select();
		
	

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

		$db 	=	M('kangbao_urine');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,urobilinogen,nitrite,whitecells,redcells,urineprotein,ph,urine,urineketone,bili,sugar,vc,cardid,examtime')->where($map)->limit($curpage.','.$pagesize)->order('createtime desc')->select();
		
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

		$info 	=	M('kangbao_urine')->where(array('id'=>$id))->find();
		return $info ? $info : false;
	}

}