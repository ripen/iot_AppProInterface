<?php
/**
 * ��ȡBmi������ָ���ȡ��������
 * */
$api_token = '8e612699da1b7f521ea4395c985b4bc3';//��������ͨ��̨��ҳ�鿴��ѡȡ
$weight = '50';//��ʵ���������Ϊ׼
$height = '1.6';//��ʵ���������Ϊ׼
$condition = 0;//��ʵ���������Ϊ׼
$post_data = "api_token=$api_token&weight=$weight&height=$height&condition=$condition";
$target = 'http://api.ydyx.yicheng120.com/HBmi';//bmi�ӿڵ�ַ
$result = Post($post_data, $target);//��ȡ���������صĸ�ʽΪjson�ַ������
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