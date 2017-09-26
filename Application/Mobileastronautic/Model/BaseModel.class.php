<?php
namespace Mobileastronautic\Model;
use Think\Model;
/**
 * @author Administrator
 * NFC model基类
 * @author tang
 * 2015.9.21
 */

class BaseModel extends Model {     
	Protected $autoCheckFields = false;
	/**
	 * 数据添加
	 * @param $data 要插入的数据
	 * @param $tablename 表名
	 */
	public function add($data = array(),$tablename=''){
		if(!$data){
			return '';
		}
		if(!$tablename){
			return '';
		}
		if($insertid = M($tablename)->add($data)){
			return $insertid;
		}
		return '';
	}
	
	
}