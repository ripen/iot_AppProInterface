<?php
namespace Kbox\Model;
use Think\Model;
/**
 * @author Administrator
 *  额温Model
 */
class Kangbao_reportModel extends BaseModel {

	//页数
	private static  $pagesize = 10;
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'kangbao_report';
	}
	
	/**
	 * 查找一条记录
	 * @param number $userid 用户id
	 * @param number $caridid 卡号id
	 * @param number $drugid 药店id
	 * @param number $time   检测时间
	 */
	public function getone($userid=0,$cardid=0,$drugid=0,$time=0,$examstatusid = '0'){
		$day	 =	date('Y-m-d',$time);
		$daytime =	strtotime($day);
		$nexttime=	$daytime+86400;
		//明天
		$where	 =	'1=1';
		$where	.=	' AND userid="'.$userid.'"';
		//$where	.=	' AND cardid='.$cardid.'';
		// $where	.=	' AND drugid= "'.$drugid.'"';
		$where	.=	' AND extime>="'.$daytime.'"';
		$where	.=	' AND extime<="'.$nexttime.'"';
		$where	.=	' AND examstatusid ="'.$examstatusid.'"';
		
		return M(self::tablename())->where($where)->order('id desc')->limit(1)->find();
	}


	/**
	 * 生成报告
	 * @param $userid 用户id
	 * @param $data 报告数据
	 * @param $cardid 卡号id
	 * @param $data 报告数据
	 * @param $type 0:康宝设备,1:单一血糖仪
	 * @param  integer $examstatusid 检测进程ID
	 * 
	 */
	public function add($data=array(),$cardid=0,$drugid=0,$userid=0,$type=0,$gate='',$examstatusid = ''){
		if(!$data){
			return '';
		}
		$report =	array();
		$report	=	self::getone($userid,$cardid,$drugid,$data['extime'],$examstatusid);
		$arr	=	array();
		$arr['userid']	=	$userid;
		$arr['data']	=	serialize(json_encode($data));
		$arr['extime']	=	$data['extime'];

		$arr['examstatusid']	=	$examstatusid ? $examstatusid : 0;

		$reportid	=	$report ? $report['id'] : '';

		if ( $report && ( !$report['drugid'] || $report['drugid'] == 0 ) && $drugid ) {
			$arr['drugid']		=	$drugid;
		}

		if($type){
			//单一血糖仪
			if($data['extime']==$report['extime']){
				M(self::tablename())->save($arr);
				
			}else{
				$arr['reportcode']	=	$data['reportcode'];
				$arr['drugid']		=	$drugid;
				$arr['cardid']		=	$cardid;
				$arr['types']		=	1;

				$arr['userinfo']	=	self::getuserinfo($userid);

				$reportid	=	M(self::tablename())->add($arr);

			}
		}else{
			//康宝数据
			if($report){
				//是否是同1天的数据
				if(!$this->comparetime($report['extime'],$data['extime'])){
					$arr['gate']		=	$gate ? $gate : '';
					$arr['reportcode']	=	$data['reportcode'];
					$arr['drugid']		=	$drugid;
					$arr['cardid']		=	$cardid;
					$arr['userinfo']	=	self::getuserinfo($userid);
					$reportid	=	M(self::tablename())->add($arr);
				}else{
					//更新
					$where 		=	array(
							'id'	=>	$report['id'],
							'extime'=>	$report['extime']
						);
					if ( $examstatusid ) {
						$where['examstatusid']	=	$examstatusid;
					}
					M(self::tablename())->where($where)->save($arr);

				}
			}else{
				$arr['gate']		=	$gate ? $gate : '';
				$arr['reportcode']	=	$data['reportcode'];
				$arr['drugid']		=	$drugid;
				$arr['cardid']		=	$cardid;
				$arr['userinfo']	=	self::getuserinfo($userid);
				$reportid	=	M(self::tablename())->add($arr);
			}
		}
		return $reportid;
	}
	



	/**
	 * 查找一条记录
	 * @param number $userid 用户id
	 * @param number $time   检测时间
	 */
	public function getoneback($userid=0,$time=0,$examstatusid = ''){
		$day	 =	date('Y-m-d',$time);
		$daytime =	strtotime($day);
		$nexttime=	$daytime+86400;
		//明天
		$where	 =	'1=1';
		$where	.=	' AND userid="'.$userid.'"';
		$where	.=	' AND extime>="'.$daytime.'"';
		$where	.=	' AND extime<="'.$nexttime.'"';
		$where	.=	' AND examstatusid ="'.$examstatusid.'"';
		
		return M('kangbao_report_examstatus')->where($where)->order('id desc')->limit(1)->find();
	}

	/**
	 * 生成报告
	 * @param $userid 用户id
	 * @param $data 报告数据
	 * @param $cardid 卡号id
	 * @param $data 报告数据
	 * @param $type 0:康宝设备,1:单一血糖仪
	 * @param  integer $examstatusid 检测进程ID
	 * 
	 */
	public function addback($data=array(),$cardid=0,$drugid=0,$userid=0,$gate='',$examstatusid = ''){
		if(!$data || !$examstatusid ){
			return '';
		}
		$report =	array();
		$report	=	self::getoneback($userid,$data['extime'],$examstatusid);
		$arr	=	array();
		$arr['userid']	=	$userid;
		$arr['data']	=	serialize(json_encode($data));
		$arr['extime']	=	$data['extime'];

		$arr['examstatusid']	=	$examstatusid ? $examstatusid : 0;

		$reportid	=	$report ? $report['id'] : '';

		if ( $report && ( !$report['drugid'] || $report['drugid'] == 0 ) && $drugid ) {
			$arr['drugid']		=	$drugid;
		}

		//康宝数据
		if($report){
			//是否是同1天的数据
			if( !$this->comparetime($report['extime'],$data['extime']) ){
				$arr['gate']		=	$gate ? $gate : '';
				$arr['reportcode']	=	$data['reportcode'];
				$arr['drugid']		=	$drugid;
				$arr['cardid']		=	$cardid;
				$arr['userinfo']	=	self::getuserinfo($userid);
				$reportid	=	M('kangbao_report_examstatus')->add($arr);
			}else{
				//更新
				$where 		=	array(
						'id'	=>	$report['id'],
						'extime'=>	$report['extime'],
						'examstatusid'	=>	$examstatusid,
					);
				M('kangbao_report_examstatus')->where($where)->save($arr);

			}
		}else{
			$arr['gate']		=	$gate ? $gate : '';
			$arr['reportcode']	=	$data['reportcode'];
			$arr['drugid']		=	$drugid;
			$arr['cardid']		=	$cardid;
			$arr['userinfo']	=	self::getuserinfo($userid);
			$reportid	=	M('kangbao_report_examstatus')->add($arr);
		}
		
		return $reportid;
	}


	/**
	 * 两个时间戳转换成天比较
	 * @param number $time1
	 * @param number $time2
	 */
	public function comparetime($time1=0,$time2=0){
		if(empty($time1) || empty($time2)){
			return false;
		}
		$day1	=	date('Y-m-d',$time1);
		$day2	=	date('Y-m-d',$time2);
		if($day1==$day2){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 对应用户报告的最后一次编码
	 * @param number $userid
	 * @return string
	 */
	public function getallcount($drugid=0){
		if(!$drugid){
			return '';
		}
		$count	=	 M(self::tablename())->where('drugid='.$drugid.'')->count();
		return $count ? $count:0;
	}


	/**
	 * 获取用户基本信息
	 * @param  integer $userid 用户ID
	 * @return array 返回用户基本信息（ 用户ID、用户名、昵称 、性别、身高、生日、年龄）
	 * 
	 */
	private function getuserinfo( $userid = 0 ){
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

		return $result ? serialize($result) : '';
	}




}