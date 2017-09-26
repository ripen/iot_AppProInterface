<?php
/**
 * api.189.cn短信接口 
 * 
 * 
 */
namespace Common\MyClass;
class Sms {
	//
	 private  $msg_code; 
	      
	function __construct(){
		//189短信接口的默认参数配置		
	 	$this->msg_code = C("MSG_CODE_TOKEN");	
	 }
	 /**
	 *获取accesstoken值
	 */	
	 private  function get_accesstoken(){
	 	$msgToken = S('sms_token');
	 	if(!$access_token ||$msgToken['expires_in']<time()){
	 		$data = array(
	 				'grant_type'=>$this->msg_code['grant_type'],
					'app_id'=>$this->msg_code['app_id'],
					'app_secret'=>$this->msg_code['app_secret']					
					);
			$postdata =  http_build_query($data);
			ksort($data);
	 		$access_token = $this->curl_post($this->msg_code['url'],$postdata);
	 		$access_token = json_decode($access_token, true);
			if($access_token['res_code']==0){
				$msgToken = S('sms_token',array('access_token'=>$access_token['access_token'],'expires_in'=>time()+$access_token['expires_in']));
			}
	 	}
	 	$msgToken = S('sms_token');
	 	return $msgToken['access_token'];	
	 }
	 
	 
	 
	/**发送短信
	 *
	 *@param  $mobile 手机号
	 *@param  $code  6位手机码
	 */		 
	 
   public  function send($mobile='',$msg=''){
   		$access_token = $this->get_accesstoken();
   		$template_id = $this->msg_code['template_id'];
		$template_param = '{"param1":"'.$msg.'"}';
		$timestamp = date("Y-m-d H:i:s",time());
		$data = array(
			'app_id'        => $this->msg_code[app_id],
			'access_token'  =>$access_token,
			'acceptor_tel'  =>$mobile,
			'template_id'   =>$template_id,
			'template_param'=>$template_param,
			'timestamp'=>$timestamp
		);
		$data =  http_build_query($data);
		ksort($data);
		$access_token = $this->curl_post($this->msg_code['sendSms'],$data);
		return $access_token;	
   }
   

   
	/**
	 * 唐管家发送短信专用
	 * @param  string $mobile 手机号
	 * @param  string $val    检测值
	 * @param  string $url    url
	 * @return [type]         [description]
	 */
   	public  function send_tgj($mobile='',$val='',$url = ''){
   		$access_token = $this->get_accesstoken();
   		$template_id = $this->msg_code['template_tgj_id'];
		$template_param = '{"param1":"'.$val.'","param2":"'.$url.'"}';
		$timestamp = date("Y-m-d H:i:s",time());
		$data = array(
			'app_id'        => $this->msg_code[app_id],
			'access_token'  =>$access_token,
			'acceptor_tel'  =>$mobile,
			'template_id'   =>$template_id,
			'template_param'=>$template_param,
			'timestamp'=>$timestamp
		);
		$data =  http_build_query($data);
		ksort($data);
		$access_token = $this->curl_post($this->msg_code['sendSms'],$data);
		return $access_token;	
   }
   
   
   function curl_get($url='', $options=array()){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    if (!empty($options)){
        curl_setopt_array($ch, $options);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}



private function curl_post($url='', $postdata='', $options=array()){
    $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //
		if (!empty($options)){
			curl_setopt_array($ch, $options);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
}
   
   
}
?>