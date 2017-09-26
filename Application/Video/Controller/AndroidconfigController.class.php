<?php
namespace Video\Controller;
use Think\Controller;
class AndroidconfigController extends Controller {
	private	$serverAddr		= "v.yicheng120.com";
	private	$serverPort		= "8906";
	private	$serverAds		= "http://api.yicheng120.com/Public/Video/images/bg.jpg";

	
 	public function __construct(){
		parent::__construct();
	}

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	public function index(){
			$dataArr	= array(
							'mDefaultServerAddr'=>$this->serverAddr,
							'mDefaultServerPort'=>$this->serverPort,
							'mDefaultAdsAddr'=>$this->serverAds,
						);
			echo $this->ajaxReturn($dataArr);
	}

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	public function addvideoinfos(){
		$infos	= I();
		$resourceArr	= array(
							'yicheng'=>1,
							'webapi'=>2,
							'appapi'=>3,
						);
		$time 	=	strtotime(date('Y-m-d 00:00:00'));
    	$etime	=	strtotime(date('Y-m-d 23:59:59'));
		$whereSQL	= array(
						'uid'		=>$infos['userid'],
						'username'	=>$infos['username'],
						'status'	=>array('ELT',3),
						'create_time'	=>array(array('EGT',$time),array('ELT',$etime)),
					);
		$id	= M("reservation")->where($whereSQL)->getField('id');
		if ($id) {
			$data['status']	= '-1';
			$data['msg']	= '重复申请视频邀请';
			echo $this->ajaxReturn($data);
		}
		$data	= array(
						'uid'		=>$infos['userid'],
						'create_time'	=>time(),
						'roomid'	=>$infos['roomnum'],
						'ip'		=>get_client_ip(),
						'username'	=>$infos['username'],
						'resourse'	=>$resourceArr[$infos['resource']] ? $resourceArr[$infos['resource']] : 0,
					);
		$insertid	= M("reservation")->add($data);
		if ($insertid) {
			$data['status']  = 1;
			$data['msg']	= '申请视频邀请成功';
		}else{
			$data['status']  = 0;
			$data['msg']	= '申请视频邀请失败';
		}
		echo $this->ajaxReturn($data);
	}

}