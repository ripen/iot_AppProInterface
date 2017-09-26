<?php
/**
 * @author Administrator
 * 获取NFC 健康档案的分类列表
 */
namespace Mobileastronautic\Model;
use Think\Model;

class LinkageModel extends Model {
	//表名
	protected  $TableName = 'linkage'; 
	//类别id
	protected  $keyid ='3360';
	public  function __construct(){
		parent::__construct();
	}
	/**
	 * 获取分类
	 * 
	 */
	public  function getlinkage(){
		$data = array();
		$data = M($this->TableName)->where("keyid=".$this->keyid)->order('listorder')->select();		
		return $data;
	}
	
	
}
	