<?php

namespace Common\Common;
/**
 * @author tangchengqi
 * 得到康宝8项检测结果,分析报告,及是否异常，本次结果上升还是下降
 */
class factorykangbao {
	
	public  function create($name,$data = array()) {
		switch ($name) {
			//体温
			case 'tm' :
				return checkkangbao::tm($data);
				break;
		   //血脂 
			case 'bf' :
				return checkkangbao::bloodfat($data);
				break;
		   //血压		
			case 'bp' :
				return checkkangbao::bloodp($data);
				break;
		  //心电		
			case 'el' :
				return checkkangbao::electrocardio($data);
				break;
		 //人体		
			case 'we' :
				return checkkangbao::humanbody($data);
				break;
	    //血氧			
			case 'ox' :
				return checkkangbao::oxygen($data);
				break;
	    //尿11项			
			case 'ur' :
				return checkkangbao::urine($data);
				break;
		// 血酮
			case 'bk':
				return checkkangbao::bloodketone($data);
				break;
		// 血尿酸
			case 're':
				return checkkangbao::renal($data);
				break;
		// 尿微量白蛋白
			case 'um':
				return checkkangbao::umprotein($data);
				break;
		// 血糖  gl
			default:
				return checkkangbao::bbsugar($data);
				break;
		}
	}
}