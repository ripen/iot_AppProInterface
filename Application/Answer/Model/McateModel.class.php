<?php
namespace Answer\Model;
use Think\Model;
/**
 * @author tangchengqi
 * 在线问答分类,从疾病百科里面读取
 */
class McateModel extends Model {
	//页数
	private static  $pagesize = 10;
	public   function __construct(){
		
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'model_field';
	}
	/**
	 * 
	 * @param number $fieldid 模型字段id ,读取疾病百科的 */
	public function getmodel( $fieldid = 202 ){

		$category	=	self::getcache($fieldid);

		if ($category) {
			return $category;
		}

		// 查询主分类信息
		$one = M(self::tablename())->where("fieldid='{$fieldid}'")->find();

		if ( !$one ) {
			return false;
		}

		$str 	= 	$one['setting'];
		eval("\$str = $str;");

		if (!$str['options']) {
			return false;
		}

		$str 	= 	trim($str['options']);
		$arr 	= 	explode("\n",$str);
		
		foreach( $arr as $k=>$v ){
			if( $k==0 ){
				unset($arr[$k]);
				continue;
			}
			$arr[$k] = explode('|',$v);
		}

		//子类数据获取
		foreach($arr as $k=>$v){
			
			$list	= 	self::getlist($v['1']);
			if( $list ){
				$arr[$k]['sub'] = $list['list'];
			}
		}
		self::setcache($fieldid,$arr);

		return $arr;
		
	}
	


	/**
	 * 在线问答的子分类
	 * @param number $id 子类型id
	 * @return string  */
    public function getlist( $id=0 ){
    	if(empty($id)){
    		return '';
    	}
    	$arr = $list = array();
    	$ids = '';
    	$list = M('encyclopedia')
    			->field('id,title')
    			->where("system_diseases=$id")
    			->select();
    	foreach($list as $k=>$v){
    		$ids .= $v['id'].',';
    	}
    	//子类id组成字符串
    	$arr['ids'] = rtrim($ids,',');
    	$arr['list'] = $list;
    	return $arr;
    }


    /**
	 * 获取缓存
	 * @param unknown $name 缓存名称
	 * @return mixed  */
	public function getcache($name=''){
		return S('answer_model_'.$name);
	}
	/**
	 * 设置缓存
	 * @param string $name 缓存名称
	 * @param unknown $value 缓存值
	 * @param string $time 过期时间 */
	public function setcache($name='',$value,$time='3600'){
		if(!S('answer_model_'.$name)){
		  S($name,$value,$time);
		}
	}
}