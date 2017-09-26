<?php
namespace Mobileselfdoctor\Controller;

use Think\Controller;
use Common\MyClass\Hicsdk;
class DatasController extends Controller {
	
 	public function __construct(){
		parent::__construct();
		$this->memberdb	= M('member');
	}

	/**
	* http://local.api.yicheng120.com/Mobileselfdoctor/Datas/getdata
	* @param authid:合作方用户ID
	* @param sn:设备码
	* @param attrs:检测状态
	* @param bloodsugar:血糖值
	* @param cardid:卡号ID
	* @author ripen_wang@163.com
	* @data 
	*/
	public function getdata(){
		$infoArr	= I();

		$hissdk	= new Hicsdk();
		// 指定服务地址
		$hissdk->api_url=$_SERVER['SERVER_NAME'];

		//访问帐号
		$username="user1";
		//访问口令
		$encrypt="7MxCxF";
		//$encrypt="12346";
		// 验证身份, Token值
		$infos	= $hissdk->checkAuth($username,$encrypt);
		$infos	= json_decode($infos,1);//将Json值转化为PHP 数组格式
		//获取验证成功，获取到Token值
		if($infos['result'] =='resultOK'){
			$values	= 9;								//测试的血糖值[必填项]
			$attrs	= 4;								//测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
			$authid	= array('authid'=>$infos['authid']);//访问者身份ID[必填项]

			//其他自带信息[选填项]，放在这个数组里，例如用户id、身份信息。
			$dates	= array('userid'=>12,'sexy'=>'男'); 

			$others	= array_merge($authid,$dates);
			echo $infos['resultmsg'];
			echo "Token为: ".$infos['token']." <br/>";

			echo "获取JSON值: <br />";
			echo $getData	= $hissdk->getData($infos['token'],$values,$attrs,$others);

			echo " <br />输出数组值:";
			echo '<pre>';
			print_r(json_decode($getData,1));

		}else{
			echo $infos['resultmsg'];
			return false;
		}

	}

}