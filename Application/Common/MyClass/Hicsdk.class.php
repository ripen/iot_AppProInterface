<?php

namespace Common\MyClass;
/**
* HIC SDK
* 
* @param 
* @author ripen_wang@163.com
* @data 2015/6/26
*/

class Hicsdk {
	public	$api_url;
	private $param_url	= "/Data/Index/";

	// 辅助函数: 使用 GET 方式发起一个 HTTP 请求, 并返回结果
	function httpCurl($url,$data_arr,$val_arr){
		$url_query = array();

		if(!is_array($data_arr)||!is_array($val_arr))	return;
		if(count($data_arr)!=count($val_arr))	return;

		for($i=0;$i<count($data_arr);$i++){
			array_push($url_query, join("=", array($data_arr[$i], urlencode($val_arr[$i]))));
		}

		$curlopt_url = join("",array($url,"?", join("&", $url_query)));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curlopt_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	/**
	 * 认证, 获取操作权限
	 *
	 * @param string [$username] 帐号 [ * ]
	 * @param string [$encrypt]  口令 [ * ]
	 * @return 返回操作令牌 (32位字符串), 操作失败返回“tokenError”
	 */
	function checkAuth($username,$encrypt){
		return $this->httpCurl(join($this->param_url, array($this->api_url,'index')), array("username","encrypt"),array($username,$encrypt));
	}

	/**
	 * 获取测量血糖数据的结果值
	 *
	 * @param string [$token">操作令牌 </param>
	 * @param string [$type">应用数据类型 (默认是：blood glucose：血糖 ，也即gbdata)</param>
	 * @return 血糖数据的结果值 ( Json 格式 ), 为空表示操作失败 </returns>
	 */
	function getData($token,$values,$attrs,$others='',$type='bgdata'){
		if (is_array($others)) {
			$others	= json_encode($others);
		}
		return $this->httpCurl(
			join($this->param_url, array($this->api_url, "getdata")),
			array("token",'values','attrs','others',"type"),array($token,$values,$attrs,$others,$type)); 
	}
}

?>