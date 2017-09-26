<?php
namespace Common\Analysisclass;

/**
 * 获取检测结果
 *
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class result {
	
	public function factory(){
		// 获取读取状态
		$readtype	=	C('ANALYSISREADTYPE');

		switch( $readtype ){
			case '1':
				return new \Common\Analysisclass\dataredis();
				break;
			default:
				return new \Common\Analysisclass\datadb();
				break; 
		}
	}

}
