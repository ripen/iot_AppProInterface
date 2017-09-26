<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/30
 * Time: 10:11
 */

if ($_GET['del']=='yes') {
	$dbh = mysqli_connect('192.168.137.10', 'platforms', 'platform!)@(#*','yc_platform' );
	$id	= intval($_GET['id']);
    if ($dbh) {
        $dbh->query("delete from yicheng where id='$id'");
		Header("HTTP/1.1 301 Moved Permanently"); //301:永久性跳转 302:临时性跳转。对于SEO来说301很友好
		header("Location: http://api.yicheng120.com/demo/");
	}
}
$json = $_POST['kbjson'];
$jsonObject = (array)json_decode($json);
$messgaeType = $jsonObject['messageType'];

switch ($messgaeType) {
    case 'powerOn'://开机注册
        print_r('{resultcode:success}');
        break;
    case 'exam'://上传血压
        //print_r('{resultcode:0,desc: "success"}');
        print_r('{resultcode:success}');
        break;
    case  'blood'://上传血糖
        //print_r('{resultcode:201, desc:"no this person"}');
        print_r('{resultcode:success}');
        break;
    case 'Config'://请求配置信息
       // print_r('{BP:"C0:15:91:A1:90:8A",GL:" B0:A1:11:31:A1:81" }');
        print_r('{resultcode:success}');
        break;
    case 'Heartbeat'://心跳信息
        print_r('{resultcode:success}');
        break;
    case 'ExamStatus'://体检状态信息
        $kahao= $jsonObject['personID'];
        $shuzhi=$jsonObject['ExamState'];
        $OnGoing=$jsonObject['ExamOnGoing'];
       examStatus($kahao,$shuzhi,$OnGoing);
        print_r('{resultcode:success}');
        break;
    default:
        print_r('messageTypeError');
        break;
};
function  examStatus($kahao, $shuzhi,$OnGoing)
{//更改状态
    $shuzhi=hexdec($shuzhi);
    $wendu = '1';
    $tizhong = '1';
    $xintiao = '1';
    $xuezhi = '1';
    $niaoye = '1';
    $xueyang = '1';
    $xuetang = '1';
    $xueya = '1';
    $zhuangtai='cl';
    if (( $shuzhi & 0x80)==0) {
        $xueya = '0';
    }
    if (( $shuzhi & 0X40)==0) {
        $xuetang = '0';
    }
    if (( $shuzhi & 0X20)==0) {
        $xueyang = '0';
    }
    if (( $shuzhi & 0X10)==0) {
        $niaoye = '0';
    }
    if (( $shuzhi & 0X08)==0) {
        $xuezhi = '0';
    }
    if (( $shuzhi & 0X04)==0) {
        $xintiao = '0';
    }
    if (( $shuzhi & 0X02)==0) {
        $tizhong = '0';
    }
    if (( $shuzhi & 0X01)==0) {
        $wendu = '0';
    }
    switch($OnGoing){
        case 'TM':
            $wendu='2';
            break;
        case 'WE':
            $tizhong='2';
            break;
        case 'EL':
            $xintiao='2';
            break;
        case 'BF':
            $xuezhi='2';
            break;
        case 'UR':
            $niaoye='2';
            break;
        case 'OX':
            $xueyang='2';
            break;
        case 'GL':
            $xuetang='2';
            break;
        case 'BP':
            $xueya='2';
            break;
        case 'DONE':
            $zhuangtai='wc';
            break;
        case 'IDLE':
            $zhuangtai='dd';
            break;
        default:
            break;
    }
    $dbh = mysqli_connect('192.168.137.10', 'platforms', 'platform!)@(#*','yc_platform' );

    if ($dbh) {
        $return_data = $dbh->query("select * from yicheng where kahao='$kahao'");

		$update_new = $dbh->query("update yicheng set wendu='1',tizhong ='1',xintiao='1',xuezhi='1',niaoye='1',xueyang='1',xuetang='1',xueya='1' WHERE kahao= '$kahao '");

        if ($return_data->num_rows > 0) {

            $updata = $dbh->query("update yicheng set wendu='$wendu',tizhong ='$tizhong',xintiao='$xintiao',xuezhi='$xuezhi',niaoye='$niaoye',xueyang='$xueyang',xuetang='$xuetang',xueya='$xueya' ,zhuangtai='$zhuangtai' WHERE kahao= '$kahao '");
            return 'x111111';
        } else {
            //该条数据不存在，新建一条数据
            $insert= $dbh->query("INSERT INTO yicheng (kahao,wendu,tizhong,xintiao,xuezhi,niaoye,xueyang,xuetang,xueya,zhuangtai)VALUES('$kahao','$wendu','$tizhong','$xintiao','$xuezhi','$niaoye','$xueyang','$xuetang','$xueya','$zhuangtai')");

            //return $insert;
            return 'j222222';
        }


    } else {
        echo '555555';
    }
}