<?php
/**
* YiCheng HIC【Health Indicator Center】
* 怡成血糖健康指标分析数据
* @author ripen_wang@163.com
* @data 2015/8/10
*/
include("videosdk.php");
$videosdk	= new videosdk();
// 指定服务地址
$videosdk->api_url="http://api.yicheng120.com";

//访问帐号
$authname="user1";
//访问口令
$encrypt="7MxCxF";
//$encrypt="12346";
// 验证身份, Token值
$infos	= $videosdk->checkAuth($authname,$encrypt);
$infos	= json_decode($infos,1);//将Json值转化为PHP 数组格式

//获取验证成功，获取到Token值
if($infos['result'] =='resultOK'){
	$username	= $_GET['username'] ? $_GET['username'] : 'Ripen';
	$userid		= $_GET['userid']	? intval($_GET['userid']) : 0;
	$roomnum	= $_GET['roomnum']	? intval($_GET['roomnum']) : 0;//进入的房间号，默认为0，也即随机产生
	$result	=	$videosdk->getVideo($infos['token'],$username,$userid,$roomnum);

	$videos	= json_decode($result,1);
	if ($videos['status'])
		echo $videos['html'];
	else
		echo 'error!';
}else{
	echo $infos['resultmsg'];
	return false;
}
?>
