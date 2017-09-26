<?php
namespace Apiyc\Controller;
use Think\Controller;

/**
 * 请求报告
 * 	
 * 	
 * @author      wangyangyang
 * @version     V1.0
 */
class ReportController extends BaseController {
	
 	public function __construct(){
		parent::__construct();
	}
	

	/**
	 * 请求报告获取数据
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return json
	 */
	public function index(){
		if ( !IS_POST ) {
			$this->errorpost();
			exit;	
		}

		$data 		=	array();
		$data['status']	=	'200';
		
		$userid 	=	I('post.userid','','intval');
		if ( !$userid ) {
			$data['status']	=	'-1';
			$this->ajaxReturn($data);
			exit;
		}

		// 判断读取的用户是否和该api有关
		$where 	=	array();
		$where['apiuserid']	=	$this->apiuserid;
		$where['userid']	=	$userid;
		$check 	=	M('user_api_from')->where($where)->find();
		if ( !$check ) {
			$data['status']	=	'-2';
			$this->ajaxReturn($data);
			exit;
		}

		// 获取该用户的最新报告信息
		$this->phpredis = new \Common\Common\phpredis();
		$redisdata  =	array();
		// 读取redis 获取用户最新的报告信息
		$keyname	=	$this->phpredis->createkeyname('report',$userid);
		$redisdata		=	array();
		$redisdata		=	$this->phpredis->formatdataget($keyname);
		if ( !$redisdata ) {
			$redisdata 	=	array();
			$redisdata 	=	M('kangbao_report')->where(array('userid'=>$userid))->order('id desc')->find();

			if ( $redisdata ) {
				$result 	=	json_decode(unserialize($redisdata['data']),true);
				$result['reportcode']	=	$redisdata['reportcode'];
			}
			
		}else{
			$result 	=	$redisdata;
		}

		if (!$result) {
			$data['status']	=	'200';
			$data['info']	=	array();
			$this->ajaxReturn($data);
			exit;
		}

		// 得到的分析报告处理,去掉无需展现的字段
		$info 	=	array();
		if (isset($result['extime']) && $result['extime'] ) {
			$info['extime']	=	$result['extime'];
		}
		if (isset($result['reportcode']) && $result['reportcode'] ) {
			$info['reportcode']	=	$result['reportcode'];
		}


		if (isset($result['gl']) && $result['gl'] ) {
			unset($result['gl']['extime'],$result['gl']['cardid'],$result['gl']['insertid']);
			$info['gl']	=	$result['gl'];
		}
		if (isset($result['ur']) && $result['ur'] ) {
			unset($result['ur']['extime'],$result['ur']['cardid'],$result['ur']['insertid']);
			$info['ur']	=	$result['ur'];
		}
		if (isset($result['ox']) && $result['ox'] ) {
			unset($result['ox']['extime'],$result['ox']['cardid'],$result['ox']['insertid']);
			$info['ox']	=	$result['ox'];
		}
		if (isset($result['we']) && $result['we'] ) {
			unset($result['we']['extime'],$result['we']['cardid'],$result['we']['insertid']);
			$info['we']	=	$result['we'];
		}
		if (isset($result['bp']) && $result['bp'] ) {
			unset($result['bp']['extime'],$result['bp']['cardid'],$result['bp']['insertid']);
			$info['bp']	=	$result['bp'];
		}
		if (isset($result['bf']) && $result['bf'] ) {
			unset($result['bf']['extime'],$result['bf']['cardid'],$result['bf']['insertid']);
			$info['bf']	=	$result['bf'];
		}
		if (isset($result['el']) && $result['el'] ) {
			unset($result['el']['extime'],$result['el']['cardid'],$result['el']['insertid']);
			$info['el']	=	$result['el'];
		}
		if (isset($result['tm']) && $result['tm'] ) {
			unset($result['tm']['extime'],$result['tm']['cardid'],$result['tm']['insertid']);
			$info['tm']	=	$result['tm'];
		}

		if (isset($result['resources']) && $result['resources'] ) {
			$info['resources']	=	$result['resources'];
		}

		
		$data['status']	=	'200';
		$data['info']	=	$info;
		$this->ajaxReturn($data);
		exit;
	}
}