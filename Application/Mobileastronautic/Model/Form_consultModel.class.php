<?php
/**
 * @author Administrator
 * 获取NFC 健康档案的分类列表
 */
namespace Mobileastronautic\Model;
use Think\Model;
use Mobileastronautic\Model\BaseModel;
class Form_consultModel extends BaseModel {
	//表名
	//protected  $TableName = 'form_consult'; 
	//页数
	private static  $pagesize = 10;
	//类别id
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名 
	 *  
	 */
	public  function tablename(){
		return 'form_consult';
	}
	/**
	 * 咨询+医生回复详情列表
	 *  @param $userid 用户id
	 *  @param $bid 内容id 
	 */
	public function getlistinfo($userid=0,$bid=0,$page=1){
		//主咨询
		$data = array();
		$curpage = ($page-1) * self::$pagesize;
		$data = M(self::tablename())->where("bid={$bid} AND userid={$userid}")->order('datetime')->limit($curpage,self::$pagesize)->select();
		foreach($data as $k=>$v){
			$data[$k]['replay'] = self::getdoctorreplay($v['dataid'],$v['bid'],1);
			$data[$k]['datetime'] = date('Y-m-d H:i:s',$v['datetime']);
		}
		return $data;
	}
	/**
	 * 咨询列表,并判定是否有回复
	 * @param  $userid 用户id 
	 * @param  $pid 医生回复对应内容id
	 * @param  $page 页数   
	 */
	public function getconsultlist($userid=0 , $pid=0, $page =1){
		/* if(empty($userid)){
			return '';
		} */
		$curpage = ($page-1) * self::$pagesize;
		$data = M(self::tablename())->where("userid={$userid} AND pid=0 AND doctorid=0")->order('datetime')->limit($curpage,self::$pagesize)->select();
		foreach($data as $k=>$v){
			$data[$k]['replay'] = self::getdoctorreplay($v['dataid'],$v['bid']);	
			$data[$k]['datetime'] = date('Y-m-d H:i:s',$v['datetime']);
		}
		return $data;
	}
	/**
	 * 医生回复
	 *  @param $pid 医生回复对应内容id
	 *  @param $bid 对应的咨询id
	 *  @param $status 1 返回回复内容,0 返回回复状态
	 *  已判定是否有回复 1 已回复 ,0 未回复  
	 */
	public function getdoctorreplay($pid=0,$bid=0, $status=0){
		
		$data = array();
		$data =M(self::tablename())->where("pid='{$pid}' AND bid='{$bid}'")->find();
		if(empty($status)){			
			return $data ?1:0;
		}else{
			return $data;
		} 
	}
}
	