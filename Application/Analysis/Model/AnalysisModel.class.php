<?php
namespace Analysis\Model;
use Think\Model;

/**
 * 数据分析结果获取
 *
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class AnalysisModel extends Model {
	
	public $table	=	'';

	public function __construct(){
		
	}

	/**
	 * 获取血糖分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回血糖数据分析原始数据
	 */
	public function gl(){
		$this->table 	=	'analysis_bbsugar' ;
		return $this->getdata();
	}
	
	/**
	 * 获取血氧分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function ox(){
		$this->table 	=	'analysis_oxygen' ;
		return $this->getdata();
	}

	/**
	 * 获取恒温分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function tm(){
		$this->table 	=	'analysis_tm' ;
		return $this->getdata();
	}

	/**
	 * 体成分分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function we(){
		$this->table 	=	'analysis_humanbody' ;
		return $this->getdata();
	}

	/**
	 * 血压分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function bp(){
		$this->table 	=	'analysis_bloodp' ;
		return $this->getdata();
	}

	/**
	 * 血脂分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function bf(){
		$this->table 	=	'analysis_bloodfat' ;
		return $this->getdata();
	}

	/**
	 * 尿常规分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function ur(){
		$this->table 	=	'analysis_urine' ;
		return $this->getdata();
	}

	/**
	 * 心电分析结果（心率）
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回数据分析原始数据
	 */
	public function el(){
		$this->table 	=	'analysis_el' ;
		return $this->getdata();
	}

	/**
	 * 获取血酮分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回血酮数据分析原始数据
	 */
	public function bk(){
		$this->table 	=	'analysis_bloodketone' ;
		return $this->getdata();
	}

	/**
	 * 获取血尿酸分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回血尿酸数据分析原始数据
	 */
	public function re(){
		$this->table 	=	'analysis_renal' ;
		return $this->getdata();
	}

	/**
	 * 获取血尿酸分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回血尿酸数据分析原始数据
	 */
	public function renew(){
		$this->table 	=	'analysis_renalnew' ;
		return $this->getdata();
	}

	/**
	 * 获取尿微量白蛋白分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回尿微量白蛋白数据分析原始数据
	 */
	public function um(){
		$this->table 	=	'analysis_umprotein' ;
		return $this->getdata();
	}

	/**
	 * 获取正项目分析结果
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @return array 返回血糖数据分析原始数据
	 */
	public function resources(){
		$this->table 	=	'analysis_resources' ;
		return $this->getdata();
	}


	private function getdata(){
		if ( !$this->table ) {
			return false;
		}

		$data 	=	M($this->table)->select();
		return $data;
	}
}