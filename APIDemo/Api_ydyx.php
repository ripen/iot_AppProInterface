<?php
/**
 * 获取Bmi，其它指标获取方法类似
 * */
$api_token = '8e612699da1b7f521ea4395c985b4bc3';//糖友掌上通后台首页查看并选取
$weight = '50';//由实际情况数据为准
$height = '1.6';//由实际情况数据为准
$condition = 0;//由实际情况数据为准
$post_data = "api_token=$api_token&weight=$weight&height=$height&condition=$condition";
$target = 'http://api.ydyx.yicheng120.com/HBmi';//bmi接口地址
$result = Post($post_data, $target);//获取服务器返回的格式为json字符串结果
echo $result;

function Post($curlPost,$url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
	$return_str = curl_exec($curl);
	curl_close($curl);
	return $return_str;
}