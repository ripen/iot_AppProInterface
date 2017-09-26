<?php
/**
 * @author Administrator
 * 血糖数据资料
 */
namespace Mobileastronautic\Model;
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
	public function getinfo($bloodsugar=''){
		if(empty($bloodsugar)){
			return '';
		}
		$data = array();
		$data = M(self::tablename())->where("type_id=1 AND attr_id=4")->select();
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