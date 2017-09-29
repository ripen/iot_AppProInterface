<?php
namespace Kbox\Model;
use Think\Model;
/**
 * @author Administrator
 *  血尿酸
 */
class Kangbao_renalModel extends BaseModel {

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
		return 'kangbao_renal';
	}
	/**
	 * 获取最近一次的血尿酸数据
	 * @param number $userid 用户id
	 * @param number $id bbsugarid 
	 * */
	public function getone($userid=0,$id=0){
			$where	=	'1=1';
			$where .=	' AND userid='.$userid.'';
			$where .=	' AND id='.$id.'';
			return M(self::tablename())->where($where)->find();
		}

}