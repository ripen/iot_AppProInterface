<?php
namespace Kbox\Model;
use Think\Model;
/**
 * @author Administrator
 *  血糖Model
 */
class BaseModel extends Model {
	Protected $autoCheckFields = false;
	
	
	/**
	 * 队列表获取数据类型,查找对应的数据
	 * @param string $type
	 * @param number $userid
	 * @param number $id
	 */
	public function getdata($name='gl',$userid=0,$id=0){
		
		switch ($name) {
			case 'tm' :
				return \Kbox\Model\Kangbao_tmModel::getone($userid,$id);
				break;
				//血脂
			case 'bf' :
				return \Kbox\Model\Kangbao_bloodfatModel::getone($userid,$id);
				break;
				//血压
			case 'bp' :
				return \Kbox\Model\Kangbao_bloodpModel::getone($userid,$id);
				break;
				//心电
			case 'el' :
				return \Kbox\Model\Kangbao_electrocardioModel::getone($userid,$id);
				break;
				//人体
			case 'we' :
				return \Kbox\Model\Kangbao_humanbodyModel::getone($userid,$id);
				break;
				//血氧
			case 'ox' :
				return \Kbox\Model\Kangbao_oxyenModel::getone($userid,$id);
				break;
				//尿11项
			case 'ur' :
				return \Kbox\Model\Kangbao_urineModel::getone($userid,$id);
				break;
			//血酮
			case 'bk' :
				return \Kbox\Model\Kangbao_bloodketoneModel::getone($userid,$id);
				break;
			//血尿酸
			case 're' :
				return \Kbox\Model\Kangbao_renalModel::getone($userid,$id);
				break;
			//尿微量白蛋白
			case 'um' :
				return \Kbox\Model\Kangbao_umproteinModel::getone($userid,$id);
				break;
				// 血糖
			default:
				return \Kbox\Model\Kangbao_bbsugarModel::getone($userid,$id);
				break;
		}
	}
}
