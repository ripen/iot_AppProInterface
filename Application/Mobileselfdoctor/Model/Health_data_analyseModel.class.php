<?php
/**
 * @author Administrator
 * 血糖数据资料
 */
namespace Mobileselfdoctor\Model;
use Think\Model;
use Mobileastronautic\Model\BaseModel;

class Health_data_analyseModel extends BaseModel {
	
	
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 * 表名 
	 */
	public  function tablename(){
		return 'health_data_analyse';
	}
	/**
	 * 获得1条血糖信息 
	 *   
	 */
	public function getinfo($bloodsugar='',$attr=''){
		if(empty($bloodsugar)){
			return '';
		}
		$data = array();
		$where =" AND 1=1";
		if($attr){
			$where .= " AND attr_id='$attr' ";
		}
		$data = M(self::tablename())->where("type_id=1".$where)->select();
		foreach($data as $k=>$v){
			$formula = str_replace('%','',$v['formula']);
			$formula = str_replace("#val#",$bloodsugar,$v['formula']);
			 if(@eval("return $formula;")) { 
				$infos	= array(
							'conclusion'=>$v['analysis'],
							'reason'	=>$v['reason'],
							'suggest'	=>$v['suggest'],
						);
				return $infos;
			} 
		}
	}
}