该接口是给公共类接口数据调用接口；
2015/9/5
技术负责人：王成震

------------使用方法
//访问帐号
$username="user1";
//访问口令
$encrypt="7MxCxF";

$hissdk	= new Hicsdk();
// 指定服务地址
$hissdk->api_url=$_SERVER['SERVER_NAME'];

//$encrypt="12346";
// 验证身份, Token值
$infos	= $hissdk->checkAuth($username,$encrypt);
$infos	= json_decode($infos,1);//将Json值转化为PHP 数组格式
//获取验证成功，获取到Token值
if($infos['result'] =='resultOK'){
	$values	= 9;								//测试的血糖值[必填项]
	$attrs	= 4;								//测试血糖时的状态：1为空腹测 4为饭后两小时测[必填项]
	$authid	= array('authid'=>$infos['authid']);//接口访问者身份ID[必填项]
	$others	= array_merge($authid,$dateArray);

	echo $hissdk->getData($infos['token'],$values,$attrs,$others);
}
