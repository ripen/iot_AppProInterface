<?php

namespace Bsugarmobile\Controller;
use Think\Controller;

/**
* IOS 5D-8B APP 
*
* @author		wangyangyang
* @copyright	wangyang8839@163.com
* @version		1.0
* @param
*/
class IosController extends Controller {
	
	//	token验证
	private $token;

	public function __construct() {
		parent::__construct ();

		$this->token	=	md5(md5('IOS5D-8B').'yicheng');
	}
	
	/**
	* 
	* @author	wangyangyang
	* @copyright	wangyang8839@163.com
	* @version	1.0
	* @param		
	* @return		
	*/
	public function index() {
		$result =	array ();
		
		$token	=	I('token','','htmlspecialchars,trim');
		if ( !$token || $token != $this->token ) {

			$result['msg']		=	'token验证失败';
			$this->assign('result',$result);
			$this->display('index_error');
			exit;
		}

		//	血糖值
		$gl		=	I('gl','','htmlspecialchars,trim');
		if ( !$gl ) {
			
			$result['msg']		=	'血糖数据不能为空';
			$this->assign('result',$result);
			$this->display('index_error');
			exit;
		}
		
		//	血糖进食状态
		$attr	=	I('attr','','intval');
		if ( !$attr ) {
			$attr	=	3;	//	随机血糖
		}
		
		$datas = Array (
				'addtime'	=> date('Y-m-d H:i:s'),
				'data'		=> $gl,
				'sign'		=> 'IOS_gl',
				'devicename'=> 'IOS',
				'equstatus'	=> 0,
		);

		// 防止页面被重复刷新
		$refnum			=	cookie('iosrefnum');
		$refnum			=	$refnum ? $refnum : 0;

		if ( $refnum > 5 ) {
			$result['msg']		=	'血糖数据不能为空';
			$this->assign('result',$result);
			$this->display('index_error');
			exit;
		}

		$checkcookie	=	cookie('iosgl');
		if ( $checkcookie && $checkcookie == $gl ) {
			$insertid	=	true;

			$refnum ++;
			cookie('iosrefnum',$refnum,array('expire'=>10));
		}else{
			$insertid 	= 	M("equipment_log")->add($datas);
			
			$refnum ++;
			cookie('iosgl',$gl,array('expire'=>600));
			cookie('iosrefnum',$refnum,array('expire'=>10));
		}
		
		
		// 获取分析结果
		$result 	=	array();
		if ( $insertid ) {
			$result		=	\Common\Analysisclass\result::factory()->bsugar(
			$gl,$attr,2);
			if ( $result ) {
				$datas	=	array();
				$datas['bloodsugar']	=	$gl;
				$datas['attr']			=	$attr;
				
				//	处理获取到的数据
				$result		=	\Common\Analysisclass\handle::bsugar($result,$datas);
			}
		}

		if ( $result ) {
			$result['clinical']	=	strip_tags(htmlspecialchars_decode($result['clinical']));
			$result['result']	=	strip_tags(htmlspecialchars_decode($result['result']));
			$result['danger']	=	strip_tags(htmlspecialchars_decode($result['danger']));

			$result['status']	=	$result['data']['status'];
			$result['type']		=	$result['data']['type'];
		}

		$this->assign('result',$result);
		$this->display();
	}
	
	
}