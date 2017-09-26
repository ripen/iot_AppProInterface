<?php
namespace Cooperation\Controller;
use Think\Controller;

/**
 * 获取用户信息接口
 * 
 * 
 * @author wangyangyang
 * @version V1.0
 */
class UserController extends Controller {
	

 	public function __construct(){
		parent::__construct();
	}


	/**
	 * 获取丰拓用户信息接口
	 * 	
	 * 
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function fentuo( ){
		$url 	=	'https://113.11.223.13:8443/FTServiceAPI/GetUserInfoServlet?ic=000000000000000001&hospitalnumber=1';

		
	    $ch 	= 	curl_init();  
	    curl_setopt($ch, CURLOPT_URL, $url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);  

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名  
	   
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题  
	    curl_setopt($ch, CURLOPT_POST, true);  
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	    //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode  
	  
	    $result =	curl_exec($ch);  
	    //var_dump(curl_error($ch));  //查看报错信息  
	  
	    curl_close($ch);  

	    p($result);
	    exit;
	}




}