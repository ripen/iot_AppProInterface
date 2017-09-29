<?php
namespace Kbox\Model;
use Think\Model;
/**
 * @author tangchengqi
 * 队列model
 */
class QueueModel extends Model {
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'kangbao_queue';
	}
	/**
	 *读取队列表的1条信息
	 *   
	 */
	public function getone(){
		return M(self::tablename())->where('status=0')->find();
	}
	
	
	/**
	 * 更新队列状态
	 * @param number $id
	 */
	public function update($id=0){
		if(empty($id)){
			return '';
		}
		$data['status']	=	1;
		return M(self::tablename())->where('id='.$id.'')->save($data);
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
	
}