<?php
/**
 * @author Administrator
 * NFC 血糖表model
 */
namespace Mobileastronautic\Model;
use Think\Model;
use Mobileastronautic\Model\BaseModel;

class Form_nfc_bloodsugarModel extends BaseModel {
	//表名
	//protected  $TableName = 'form_nfc_bloodsugar';
	//页数
	private static   $pagesize = 10;
	//类别id
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'form_nfc_bloodsugar';
	}
	
	/**
	 * 获取列表
	 *  @param $userid 用户id
	 *  @param $page 页数 
	 */
	public function getlist($userid=0,$page=1){
		 if(empty($userid)){
			return'';					
		} 
		$data = array();
		$curpage = ($page-1) * self::$pagesize;
		$data = M(self::tablename())->where("userid={$userid}")->order('datetime')->limit($curpage,self::$pagesize)->select();
		foreach($data as $k=>$v){
			$data[$k]['datetime'] = date('Y-m-d H:i:s',$v['datetime']);
			//获取血糖状态
			$bloodsugar = array();
			$bloodsugar = Health_data_analyseModel::getinfo($v['bloodsugar']);
			$data[$k]['conclusion'] = $bloodsugar['conclusion'];
		}
		return $data;		
	}
	
	/**
	 * 详情 
	 *  @param  $dataid 数据id
	 *   
	 */
	public function getinfo($dataid=0){
		if(empty($dataid)){
			return'';
		}
		$data = M(self::tablename())->where("dataid={$dataid}")->find();
		if($data){
			$data['datetime'] = date('Y-m-d H:i:s',$data['datetime']); 
		}
		return $data;
	}
}