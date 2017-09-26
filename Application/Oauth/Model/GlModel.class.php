<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 血糖数据管理
 * 
 * @author wangyangyang
 * @version V1.0
 */
class GlModel extends Model {  

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

		$db 	=	M('kangbao_bbsugar');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,bloodsugar,cardid,attr,examtime')->where($map)->limit($curpage.','.$pagesize)->order('createtime desc')->select();
		// 处理进食状态
		$info	=	$this->getattr($info);

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

		$db 	=	M('kangbao_bbsugar');
		// 查询总数
		$total	=	$db->where($map)->count("id");

		if ( !$total ) {
			return false;
		}

		$info 	=	$db->field('id,bloodsugar,attr,examtime')->where($map)->limit($curpage.','.$pagesize)->order('createtime desc')->select();
		// 处理进食状态
		$info	=	$this->getattr($info);
		
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

		$info 	=	M('kangbao_bbsugar')->where(array('id'=>$id))->find();
		return $info ? $info : false;
	}


	/**
	 * 处理进食状态
	 * @param  array  $data 检测数据
	 * @author wangyangyang
	 * @version V1.0
	 */
	private function getattr( $data = array() ){
		if ( !$data ) {
			return false;
		}

		$attrnameArr    =   array(
            '1'=>'空腹血糖','2'=>'早餐后2小时血糖','3'=>'随机血糖',
            '5'=>'午餐前血糖','6'=>'午餐后2小时血糖','7'=>'晚餐前血糖',
            '8'=>'晚餐后2小时血糖','9'=>'睡前血糖'
        );

        foreach ($data as $key => $value) {
        	$data[$key]['attr']	=	isset($attrnameArr[$value['attr']]) ? $attrnameArr[$value['attr']] : $attrnameArr[3];
        }

        return $data;
	}
}