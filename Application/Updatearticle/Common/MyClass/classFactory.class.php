<?php
namespace DataM\Common\MyClass;
/**
* 工厂模式可以创建不同的对象，而不直接使用 new。
* 这样做的目的是为了实现类的多态性。
*
* @author RipenWang
* @copyright ripen_wang@163.com
* @example：
	classFactory::createFactory("classA")->getVar() ;
*/
class classFactory{
	public static function createFactory($var){
		if($var=="bmi"){
			return new bmi();
		}elseif($var=="classA"){
			
		}
	}
}
