<?php
namespace Bsugarmobile\Model;
use Think\Model;
/**
 * @author tangchengqi
 * 用户卡号model
 */
class Drug_card_userModel extends Model {
	
	
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'drug_card_user';
	}
	
	/**
	 * 返回用户对应的卡列表
	 * @param unknown $userid 用户id  */
	public function getcardlist($userid=0){
		return M(self::tablename())->where('userid="'.$userid.'"')->select();
	}
	
	/**
	 * 判定卡号是否和用户绑定
	 * @param number $userid  用户id 
	 *  @param number $card  用户卡号 
	 * */
	public function getcheckcard($userid=0,$card=''){
		if(!$card){
			return '';
		}
		$card =trim($card);
		$cardprev = substr($card,0,2);
		//$cardnum = substr($card,2,6);中间字符
		for($i=0;$i<strlen($card)-2;$i++){
			if($i>1){
				$cardnum .= $card[$i];
			}
		}
		$cardencrypt = substr($card,strlen($card)-2,2);
		return M(self::tablename())
				->field('status')
				->where('userid='.$userid.' AND cardprev='.$cardprev.' AND cardnum='.$cardnum.' AND cardencrypt='.$cardencrypt.'')
				->find();
	}
	
}