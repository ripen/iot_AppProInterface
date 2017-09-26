<?php

namespace Kbox\Controller;
use Think\Controller;

/**
 * 微信推送报告
 *
 * @author      wangyangyang
 * @version     V1.0
 */
class WxreportController extends Controller{
	
	// 获取微信基础token
	private $wx_token_url	=	'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';

	// 发送模版消息url
	private $wx_temp_url	=	'https://api.weixin.qq.com/cgi-bin/message/template/send';

	private $typeArray = array('gl' => '血糖', 'bp' => '血压', 'we' => '体成分', 'bf' => '血脂', 'ox' => '血氧', 'ur' => '尿常规', 'tm' => '体温', 'el' => '心电');

	public function __construct(){
		parent::__construct();
	}
	

	public function index(){
		set_time_limit(0);

		while(true){
			$info 	=	array();
			$info 	=	M('wx_report')->where('status=0')->find();

			if( !$info ){
				// 等待1分钟执行
				sleep(60);	
			}else{
				//	等待3分钟，防止报告未生成的情况
				// sleep(180);
				
				if ( $info['resource'] == 1 ) {
					// 微信推送
					$wxresult 	=	$this->getinfo($info['userid'],$info['wxid']);
				}elseif ( $info['resource'] == 2  ) {
					// API用户推送
					$wxresult 	=	$this->pushinfo($info['userid'],$info['apiid']);
				}

				// 更新推送报告信息记录表状态
				$data	=	array('updatetime'=>date('Y-m-d H:i:s',time()));
				if ($wxresult < 1 ) {
					$data['status']		=	$wxresult;
				}else{
					$data['status']		=	1;
					$data['reportid']	=	$wxresult;
				}
				M('wx_report')->where(array('id'=>$info['id']))->save($data);
			}
		}
	}

	/**
	 * 查询相关数据
	 * @param  integer $userid 用户id
	 * @param  integer $wxid   微信ID
	 * @return 
	 */
	private function getinfo($userid = 0,$wxid = 0 ){
		if ( !$userid || !$wxid ) {
			return '-1';
		}

		// 查询openid
		$wxinfo	=	M('wx_member')->where(array('id'=>$wxid))->find();
		if ( !$wxinfo ) {
			return '-2';
		}

		// 查询用户信息
		$userinfo	=	M('member')->where(array('userid'=>$userid))->field('userid,username,nickname')->find();

		$username	=	$userinfo['nickname'] ? $userinfo['nickname'] : $userinfo['username'];

		// 查询基本报告基本信息
		$report 	=	M('kangbao_report')->where(array('userid'=>$userid,'types'=>0))->order('id desc')->limit(1)->find();

		if ( !$report ) {
			return '-3';
		}

		// 判断获取到的report数据
		$rinfo 	=	unserialize($report['data']);
		$rinfo	=	$rinfo ? json_decode($rinfo,true) : array();
		
		if ( !$rinfo ) {
			return '-3';
		}
		
		// 获取微信基础token
		$token 	=	$this->gettoken();
		if ( !$token ) {
			return 0;
		}

		// 微信推送时候 remark 文字信息
		// 感谢您参与检测！在此次评估项目中，您参与了 血糖 血脂 2 项检测，本次评估有 2 项指标异常
		$remark	=	$this->getmark($rinfo);
		$remarkstr	=	'';
		if ($remark && $remark['abnormal'] == 0 && $remark['item'] ) {
			$remarkstr	=	'您最近一次共进行了'.$remark['item'].' 项检测，您的各项检测指标正常，请继续保持...';
		}else if( $remark['abnormal'] && $remark['name'] && $remark['item']){
			$remarkstr	=	'感谢您参与检测！在此次评估项目中，您参与了 '.$remark['name'].' '.$remark['item'].' 项检测，本次评估有 '.$remark['abnormal'].' 项指标异常...'; 
		}else{
			$remarkstr	=	'感谢您参与检测...';
		}


		$result	=	$this->sendwsmsg($wxinfo['open_id'],$token,$rinfo,$report['id'],$username,$remarkstr);

		return $result ? $report['id'] : 0;
	}


	/**
	 * 推送相关数据
	 * @param  integer $userid 用户id
	 * @param  integer $apiid   API应用用户ID
	 * @return 
	 */
	private function pushinfo( $userid = 0 , $apiid = 0 ){
		if ( !$userid ) {
			return '-1';
		}

		// 查询用户信息
		$info	=	M('user_api_from')->where(array('userid'=>$userid,'apiuserid'=>$apiid))->find();
		if ( !$info ) {
			return '-2';
		}

		// 查询API用户是否存在
		$apiinfo	=	M('member')->where(array('userid'=>$apiid))->field('userid')->find();
		if (!$apiinfo) {
			return '-3';
		}

		// 查询API用户是否含有回调地址
		$apideinfo	=	M('member_api')->where(array('userid'=>$apiid))->find();
		if (!$apideinfo || !$apideinfo['callback'] ) {
			return '-4';
		}


		// 查询基本报告基本信息
		$report 	=	M('kangbao_report')->where(array('userid'=>$userid,'types'=>0))->order('id desc')->limit(1)->find();

		if ( !$report ) {
			return '-5';
		}

		// 判断获取到的report数据
		$rinfo 	=	unserialize($report['data']);
		$rinfo	=	$rinfo ? json_decode($rinfo,true) : array();
		
		if ( !$rinfo ) {
			return '-6';
		}

		$result 	=	array();
		$result['status']	=	'200';
		$result['userid']	=	$userid;
		$result['apiuserid']=	$info['tuserid'];
		$result['gate']		=	$report['gate'];

		if (isset($rinfo['extime']) && $rinfo['extime'] ) {
			$result['extime']	=	$rinfo['extime'];
		}else{
			$result['extime']	=	'';
		}
		if (isset($rinfo['reportcode']) && $rinfo['reportcode'] ) {
			$result['reportcode']	=	$rinfo['reportcode'];
		}else{
			$result['reportcode']	=	'';
		}

		if (isset($rinfo['gl']) && $rinfo['gl'] ) {
			unset($rinfo['gl']['extime'],$rinfo['gl']['cardid'],$rinfo['gl']['insertid']);
			$result['gl']	=	$rinfo['gl'];
		}
		if (isset($rinfo['ox']) && $rinfo['ox'] ) {
			unset($rinfo['ox']['extime'],$rinfo['ox']['cardid'],$rinfo['ox']['insertid']);
			$result['ox']	=	$rinfo['ox'];
		}
		if (isset($rinfo['bp']) && $rinfo['bp'] ) {
			unset($rinfo['bp']['extime'],$rinfo['bp']['cardid'],$rinfo['bp']['insertid']);
			$result['bp']	=	$rinfo['bp'];
		}
		if (isset($rinfo['bf']) && $rinfo['bf'] ) {
			unset($rinfo['bf']['extime'],$rinfo['bf']['cardid'],$rinfo['bf']['insertid']);
			$result['bf']	=	$rinfo['bf'];
		}
		if (isset($rinfo['we']) && $rinfo['we'] ) {
			unset($rinfo['we']['extime'],$rinfo['we']['cardid'],$rinfo['we']['insertid']);
			$result['we']	=	$rinfo['we'];
		}
		if (isset($rinfo['ur']) && $rinfo['ur'] ) {
			unset($rinfo['ur']['extime'],$rinfo['ur']['cardid'],$rinfo['ur']['insertid']);
			$result['ur']	=	$rinfo['ur'];
		}
		if (isset($rinfo['el']) && $rinfo['el'] ) {
			unset($rinfo['el']['extime'],$rinfo['el']['cardid'],$rinfo['el']['insertid']);
			$result['el']	=	$rinfo['el'];
		}
		if (isset($rinfo['tm']) && $rinfo['tm'] ) {
			unset($rinfo['tm']['extime'],$rinfo['tm']['cardid'],$rinfo['tm']['insertid']);
			$result['tm']	=	$rinfo['tm'];
		}
		if (isset($rinfo['resources']) && $rinfo['resources'] ) {
			$result['resources']	=	$rinfo['resources'];
		}
		
		$this->http_request($apideinfo['callback'],array('data'=>json_encode($result)));
		

		return $report['id'];
	}


	/**
	 * 获取检测结果状态信息
	 * @param  array  $data 报告信息
	 * @return [type]       [description]
	 */
	private function getmark( $data = array() ){
		if ( !$data ) {
			return false;
		}
		
		// 总共检测项目统计
		$item		=	0;
		// 检测项目名称
		$name		=	'';
		// 异常检测项目统计
		$abnormal	=	0;
		foreach ($this->typeArray as $key => $value) {
			if ( isset($data[$key]) && is_array($data[$key]) ) {
				$item++;
				$name .= $value . " ";
				foreach ($data[$key] as $k => $val) {
					if (isset($val) && is_array($val)) {
						if ($val['type'] == 1) {
							$data['abnormal'][$value][] = $val;
							$abnormal++;
						}
					}
				}
			}
		}

		$result 	=	array();
		$result['item']		=	$item;
		$result['name']		=	$name;
		$result['abnormal']	=	$abnormal;
		return $result;
	}


	/**
	 * 获取微信基础token
	 * 
	 * @return string 返回获取到的token信息
	 */
	private function gettoken(){
		$wx_yconline_token	=	S('wx_yconline_token');
		$wx_yconline_time	=	S('wx_yconline_time');

		$nowtime			=	time();

		$info 				=	array();

		if ( !$wx_yconline_token || $nowtime > $wx_yconline_time) {
			// 获取微信token
			$url 	=	$this->wx_token_url.'&appid='.C('wx_yconline_appid').'&secret='.C('wx_yconlin_appsecret');
			$info	=	$this->GetJson($url);
			$info 	=	$info ? json_decode($info,true) : array();
			
		}

		if ( $info && isset($info['errcode'])) {
			return false;
		}elseif ( $info && isset($info['access_token']) && $info['access_token']) {
			S('wx_yconline_token',$info['access_token']);
			S('wx_yconline_time',time()+7000);

			return $info['access_token'];
		}else{
			return $wx_yconline_token;
		}

		return false;
		
	}

	/**
	 * 返回json数据
	 * @param [type] $url [description]
	 */
	private function GetJson( $url ) {
		return file_get_contents($url);
	}


	/**
	 * 发送模版消息
	 * @param  string $openid 微信 openid
	 * @param  string $token  微信基础token
	 * @param  array  $data   报告基础数据
	 * @param  integer $reportid 报告id
	 * @param  string $username 用户昵称
	 * @param  string $remarkstr 微信remark信息
	 * @return 
	 */
	private function sendwsmsg($openid = '',$token = '',$data = array() ,$reportid = 0 ,$username = '',$remarkstr = ''){
		if (!$openid || !$token || !$data || !$reportid ) {
			return false;
		}

		// 处理基础数据
		
		$wx = '"data":{
				   "first": {
					   "value":"您的健康评估报告已生成\n",
					   "color":"#0A0A0A"
				   },
				   "keyword1":{
					   "value":"怡成康宝健康评估报告\n"
				   },
				   "keyword2": {
					   "value":"'.$username.'"
				   },
				   "keyword3": {
					   "value":"'.date('Y-m-d', $data['extime']).'"
				   },
				   "remark":{
                       "value":"\n'.$remarkstr.'",
                       "color":"#fa8b26"
                   }
		   }';

		// 跳转url
		$sign 		=	md5(md5('yichengkb').$reportid);
		$infourl 	=	'wx.yicheng120.com/ycwx/Wxreport/index/id/'.$reportid.'/sign/'.$sign;
		$postdata 	=  '{
		           "touser":"'.$openid.'",
		           "template_id":"GNOflL2K7VgjbkezaLCGn6YPhV79E1a9mipfiOQBoUM",
		           "url":"'.$infourl.'",
		           "topcolor":"#FF0000",
		           '.$wx.'
		       }';
		
		$url 		= 	$this->wx_temp_url.'?access_token='.$token;
		$result 	=	$this->http_request($url,$postdata);
		
		$result 	=	$result ? json_decode($result,true) : '';

		if (isset($result['errcode']) && $result['errcode'] == 0) {
			return true;
		}
		return false;
	}


	private function http_request($url,$data=null){
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}

}