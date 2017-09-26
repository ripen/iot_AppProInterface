<?php
use Think\Cache;
//自定义函数


/**
 * @作用域: 快捷输出 格式化print_r()函数
 * @访问权限: 公共函数
 * @参数1: 数组 对象 $arr 需要格式化输出的数组或对象
 * @返回值: 格式化后的输出结果
 */
function p($arr) {
	echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><pre>');
	print_r($arr);
	echo("</pre>");
}
/**************************************************************
 *
*	将数组转换为JSON字符串（兼容中文）
*	@param	array	$array		要转换的数组
*	@return string		转换得到的json字符串
*	@access public
*
*************************************************************/
function JSON($array) {
	arrayRecursive($array, 'urlencode', true);
	$json = json_encode($array);
	return urldecode($json);
}
/**************************************************************
 *
*	使用特定function对数组中所有元素做处理
*	@param	string	&$array		要处理的字符串
*	@param	string	$function	要执行的函数
*	@return boolean	$apply_to_keys_also		是否也应用到key上
*	@access public
*
*************************************************************/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
	static $recursive_counter = 0;
	if (++$recursive_counter > 1000) {
		die('possible deep recursion attack');
	}
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			arrayRecursive($array[$key], $function, $apply_to_keys_also);
		} else {
			$array[$key] = $function($value);
		}

		if ($apply_to_keys_also && is_string($key)) {
			$new_key = $function($key);
			if ($new_key != $key) {
				$array[$new_key] = $array[$key];
				unset($array[$key]);
			}
		}
	}
	$recursive_counter--;
}
//验证手机号
function checkphone($phone){
	return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17\d{9}$#', $phone) ? true : false;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length = 6, $chars = '0123456789abcdefjhijklmnopqrstuvwxyz'){
	$code = "";
	while (strlen($code)<$length){
		$code.=substr($chars,(mt_rand()%strlen($chars)),1);
	}
	return $code;
}


/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : random();
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}

/**
* 判断是否是微信端
* 
* @param 
* @author ripen_wang@163.com
* @data 2014/11/5
*/
function isWeiXin(){
	if (strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")) {
		return true;
	}else {
		return false;
	}
}

//post提交方式
function Spost($remote_server,$post_data) {
	$ch = curl_init();//初始化curl
	curl_setopt($ch, CURLOPT_TIMEOUT,360);
	curl_setopt($ch, CURLOPT_URL,$remote_server);//抓取指定网页
	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$data = curl_exec($ch);//运行curl
	curl_close($ch);
	Return $data;
}

/**
 * 获取微信版本号
 * @return [int] [版本号]
 */
function get_weixin_version(){
	$_vesion = 52;
	preg_match_all("/.*MicroMessenger\/(\d+)\.(\d+)\.*/", $_SERVER['HTTP_USER_AGENT'], $arr);

	if($arr && !empty($arr[1]) && !empty($arr[2])){
		$_vesion = current($arr[1]).current($arr[2]);
	}

	return intval($_vesion);
}

/**
 * 判断是否为iPhone客户端1
 * @return boolean [description]
 */
function is_iphone(){
	//F('iiiiii',print_r($_SERVER,TRUE));
	$is_iphone = false;
	if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'iphone')){
		$is_iphone = true;
	}
	return $is_iphone;
}

/**
 * 获取iPhone版本号
 * @return [int] [description]
 */
function get_iphone_version(){
	$_vesion = 0;
	preg_match_all("/.*iPhone OS (\d_\d).*/", $_SERVER['HTTP_USER_AGENT'], $arr);
	if(current($arr[1])){
		$_v_arr = explode('_', current($arr[1]));
		$_vesion = $_v_arr[0];
	}
	return intval($_vesion);
}

// $string： 明文 或 密文
// $operation：DECODE表示解密,其它表示加密
// $key： 密匙
// $expiry：密文有效期
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
	$ckey_length = 4;

	// 密匙
	$key = md5($key ? $key : C('AU_KEY'));

	// 密匙a会参与加解密
	$keya = md5(substr($key, 0, 16));
	// 密匙b会用来做数据完整性验证
	$keyb = md5(substr($key, 16, 16));
	// 密匙c用于变化生成的密文
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	// 参与运算的密匙
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
	// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	// 产生密匙簿
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	// 核心加解密部分
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		// 从密匙簿得出密匙进行异或，再转成字符
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		// substr($result, 0, 10) == 0 验证数据有效性
		// substr($result, 0, 10) - time() > 0 验证数据有效性
		// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
		// 验证数据有效性，请看未加密明文的格式
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/**
 * 校验日期格式是否正确
 * 
 * @param string $date 日期
 * @param string $formats 需要检验的格式数组
 * @return boolean
 */
function checkDateIsValid($date, $formats = array("Y-m-d")) {
    $unixTime = strtotime($date);
    if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
        return false;
    }
    //校验日期的有效性，只要满足其中一个格式就OK
    foreach ($formats as $format) {
        if (date($format, $unixTime) == $date) {
            return true;
        }
    }

    return false;
}

/**
* 提取数组中的某一项值
* @author		wangyangyang
* @copyright	wangyang8839@163.com
* @version		1.0
* @param
*/
function extractArray( $data, $type ){
	if ( !$data || !$type || !is_array($data) )  Return false;
	$result	=	array();
	foreach($data AS $key => $val){
		if ( array_key_exists($type,$val)  ) {
			$result[]	=	$val[$type];
		}
	}
	Return $result;
}

/**
* 处理二维数组，生成以某个值为键值的数组
* @author		wangyangyang
* @copyright	wangyang8839@163.com
* @version		1.0
* @param
*/
function handleArrayKey( $data , $type ){
	if ( !$data || !$type || !is_array($data) )  Return false;

	$result	=	array();
	foreach($data AS $key => $val){
		if ( array_key_exists($type,$val)  ) {
			$result[$val[$type]]	=	$val;
		}
	}
	Return $result;
}

/**
* 计算年龄
* @author		wangyangyang
* @copyright	wangyang8839@163.com
* @version		1.0
* @param
*/
function age($birthday){
	$age	=	0; 
	$year	=	$month	=	$day	=	0; 
	if ( is_array($birthday) ) { 
		extract($birthday); 
	} else { 
		if ( strpos($birthday, '-') !== false ) { 
			list($year, $month, $day) = explode('-', $birthday); 
			$day	=	substr($day, 0, 2);
		} 
	} 
	
	$age = date('Y') - $year; 
	
	if (date('m') < $month || (date('m') == $month && date('d') < $day)) {
		$age--; 
	}

	return $age; 
}



/**
 * 生成统一格式返回的json数据
 * 
 * @param  integer $code   返回码
 * @param  string $message 返回摘要
 * @param  array  $data    返回数据
 * @return json
 */
function oauthjson($code = 200,$message = '',$data = array() ) {
	$result 	=	array();
	$result['code']	=	$code;
	$result['message']	=	$message;
	$result['data']		=	(array)$data;

	arrayRecursive($data, 'urlencode', true);
	$json = json_encode($result);
	exit($json);
}

/**
 * 随机生成用户名
 * @param  string  $pre 随机用户名前缀
 * @param  integer $length 随机长度
 * @return string  返回用户名
 */
function generate_username( $pre = 'yc_', $length = 9 ) {
    $chars		=	'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $username	=	'';
    for ( $i = 0; $i < $length; $i++ ){
        $username .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }

    // 判断用户名是否已经注册，如有注册，重新生成
    while ( true ) {
    	$check 	=	M('member')->where(array('username'=>$username))->field('userid')->find();
    	if ( !$check ) {
    		$username 	=	$pre.$username;
    		break;
    	}else{
    		generate_username( $pre , $length ); 
    	}
    }

    return $username;
}


/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
	$Page       = new \Think\Page($count,$pagesize);// 实例化分页类 传入总记录数和每页显示的记录数
	$Page->setConfig('theme',"<ul class='pagination'><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li></ul>");
	$show       = $Page->show();// 分页显示输出
	return $show;
}
