<?php
namespace Org\OAuth;

import("ORG.OAuth.OAuth2");  
class ThinkOAuth2 extends OAuth2 {  

	public function __construct() {  
		parent::__construct();
	}  

	/**
	 * 新增客户
	 * 
	 * @param string $client_id     client_id
	 * @param string $client_secret client_secret
	 * @param string $redirect_uri  回调地址
	 * @author wangyangyang
	 * @version V1.0 方法初始化
	 */
	public function add_client($client_id, $client_secret, $redirect_uri) {  
		if (!$client_id || !$client_secret || !$redirect_uri ) {
			return false;
		}

		$data 	=	array();
		$data['client_id']		=	$client_id;
		$data['client_secret']	=	$client_secret;
		$data['redirect_uri']	=	$redirect_uri;
		$data['create_time']	=	time();
		
		$id 	=	M('client','oauth_','DB_CONFIG2')->add($data);
		return $id ? $id : false;
	}

	
	/**
	 * 判断key、密钥是否合法
	 * 
	 * @param  string $client_id     client_id
	 * @param  string $client_secret client_secret
	 * @author wangyangyang
	 * @version V1.0 方法初始化
	 */
	protected function auth_client_credentials($client_id, $client_secret = NULL) {  
		if ( !$client_id ) {
			return false;
		}

		$info 	=	$this->get_client_info($client_id);
		if ( !$info ) {
			return false;
		}
		
		if ($info['client_secret'] != $client_secret ) {
			return false;
		}

		return true;
	}  


	/**
	 * 根据 client_id 获取回调地址
	 * 
	 * @param  string $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_redirect_uri( $client_id ){
		if ( !$client_id ) {
			return false;
		}

		$info 	=	$this->get_client_info($client_id);
		if ( !$info ) {
			return false;
		}
		
		return $info['redirect_uri'];
	}

	
	/**
	 * 获取 access_token 信息
	 * 
	 * @param  string $access_token access_token
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_access_token($access_token) {
		if ( !$access_token ) {
			return false;
		}

		$info 	=	M('token','oauth_','DB_CONFIG2')->where(array('access_token'=>$access_token))->find();

		if ( !$info ) {
			return false;
		}
		
		return $info;
	}  

	/**
	 * 新增access_token
	 * 
	 * @param  string  $access_token access_token
	 * @param  string  $client_id    client_id
	 * @param  integer $expires      有效期
	 * @param  string  $scope        权限（目前暂未用到）
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function store_access_token($access_token, $client_id, $expires, $scope = NULL) {  
		if (!$access_token || !$client_id || !$expires ) {
			return false;
		}

		$data 	=	array();
		$data['access_token']	=	$access_token;
		$data['client_id']		=	$client_id;
		$data['expires']		=	$expires;
		$data['scope']			=	$scope;
		$data['refresh_token']	=	'';
		$data['addtime']		=	time();
		$id 	=	M('token','oauth_','DB_CONFIG2')->add($data);

		return $id ? $id : false;
	}  

	/**
	 * 获取不同用户的token有效期
	 * 
	 * @param  string  $client_id    client_id
	 * @param  integer $times     	 有效期
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_token_time( $client_id , $times = '3600'){
		$type	=	$this->get_client_user_type( $client_id );

		$type 	= 	$type ? $type : 1;

		$frequency 	=	$this->get_app_token_time($type);

		$days		=	isset($frequency['tokentime']) && $frequency['tokentime'] ? 
			$frequency['tokentime'] : 1;

		$result	=	$times * 24 * $days;
		Return $result;
	}

	/**
	* 根据用户类型获取系统设定的token有效期时间
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		$type int 用户类型
	* @return		$result array 
	*/
	protected function get_app_token_time( $type ){

		$data	=	array();
		$data	=	M('frequency','oauth_','DB_CONFIG2')->where( array('user_type'=>$type))->find();
		
		Return $data ? $data : '';
	}

	/**
	 * [get_supported_grant_types description]
	 * 
	 * @return [type] [description]
	 */
	protected function get_supported_grant_types() {  
		 return array(AUTH_CODE_GRANT_TYPE);
	}  

	/**
	 * 获取 code 信息
	 * 
	 * @param  string $code code
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_stored_auth_code($code) {  
		if ( !$code ) {
			return false;
		}

		$info 	=	M('code','oauth_','DB_CONFIG2')->where(array('code'=>$code))->find();
		return $info ? $info : false;
	}  

	/**
	 * 添加code信息
	 * 
	 * @param  string $code         code
	 * @param  string $client_id    client_id
	 * @param  string $redirect_uri 回调地址
	 * @param  integer $expires     有效时间
	 * @param  string $scope        权限
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function store_auth_code($code, $client_id, $redirect_uri, $expires, $scope = NULL) {  

		$data 	=	array();
		$data['code']			=	$code;
		$data['client_id']		=	$client_id;
		$data['redirect_uri']	=	$redirect_uri;
		$data['scope']			=	$scope;
		$data['expires']		=	$expires;
		$id 	=	M('code','oauth_','DB_CONFIG2')->add($data);
		return $id ? $id : false;
	}  

	/**
	 * 删除code
	 * 
	 * @param  string $code      code
	 * @param  string $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function delete_auth_code( $code , $client_id ){
		if ( !$code || !$client_id ) {
			return false;
		}
		$where 	=	array();
		$where['code']		=	$code;
		$where['client_id']	=	$client_id;
		$rows	=	M('code','oauth_','DB_CONFIG2')->where( $where )->delete();
		return $rows ? $rows : false;
	}
	
	/**
	 * [check_user_credentials description]
	 * @param  [type] $client_id [description]
	 * @param  [type] $username  [description]
	 * @param  [type] $password  [description]
	 * @return [type]            [description]
	 */
	protected function check_user_credentials($client_id, $username, $password){  
		return TRUE;  
	}  


	/**
	 * 记录访问次数
	 * @param  string $clientid clientid
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function frequency( $clientid ){
		if ( !$clientid )  {
			Return FALSE;
		}
		// 获取用户类型
		$user_type		=	$this->get_client_user_type( $clientid );
		
		// 获取访问次数
		$limit_times	=	$this->get_limit_frequency( $user_type );

		//	获取或统计用户访问此时,默认使用mysql
		$count		=	$this->get_user_frequency( $clientid , $limit_times );
	
		if ( $count > $limit_times ) {
			Return FALSE;
		}
	}

	/**
	 * 获取用户生成的token有效期类型
	 * 
	 * @param  string $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_client_user_type( $client_id ){
		if ( !$client_id ) {
			return false;
		}

		$info 	=	$this->get_client_info($client_id);
		
		$result	=	isset($info['user_type']) && $info['user_type'] ? $info['user_type'] : 1;
		Return $result;
	}

	/**
	 * 获取用户类型每天可请求的次数
	 * 
	 * @param  integer $type 用户类型
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_limit_frequency( $type ){
		$data	=	array();
		$result	=	array();
		$data	=	M('frequency','oauth_','DB_CONFIG2')->where(array('user_type'=>$type))->find();

		if (!$data ) {
			return FALSE;
		}

		return $data['times'] ? $data['times'] : false;
	}

	/**
	 * 记录访问次数
	 * 
	 * @param  string  $client_id   client_id
	 * @param  integer $limit_times 用户已经访问次数
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_user_frequency( $client_id , $limit_times ){
		$times	=	0;
		
		$nowtime=	date('Y-m-d',time());
		
		$where	=	array('visittime'=>$nowtime,'client_id'=>$client_id);
		//	先获取
		$get_data	=	M('usertimes','oauth_','DB_CONFIG2')->where($where)->find();
		
		if ( isset($get_data['times']) && $get_data['times'] == $limit_times ) {
			Return $limit_times + 1;
		}

		if ( $get_data ) {
			$times	=	$get_data['times'] + 1;
			$data	=	array('times'=>$times);

			M('usertimes','oauth_','DB_CONFIG2')->where(array('id'=>$get_data['id']) )->save($data);
		}else{
			$where['times']	=	1;
			M('usertimes','oauth_','DB_CONFIG2')->add($where);
			$times  =	1;
		}
		Return $times;
	}

	/**
	 * 获取用户类型
	 * @param  string $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	protected function get_client_type( $client_id ){
		if ( !$client_id ) {
			return false;
		}

		$info 	=	$this->get_client_info($client_id);
		
		$result	=	isset($info['type']) && $info['type'] ? $info['type'] : 1;
		Return $result;
	}

	/**
	 * 获取当天获取token次数
	 * 
	 * @param  string $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化
	 */
	protected function get_today_token_time( $client_id ){
		if ( !$client_id ) {
			return false;
		}

		$bdate 	=	date('Y-m-d 00:00:00');
		$edate 	=	date('Y-m-d 23:59:59');

		$btimes	=	strtotime($bdate);
		$etimes	=	strtotime($edate);

		$where 	=	array();
		$where['clientid']	=	$client_id;
		$where['addtime']	=	array('between',array($btimes,$etimes));

		$total 	=	M('token','oauth_','DB_CONFIG2')->where( $where )->count();
		
		F('tokentimes',$total);

		return $total ? $total : 0;
	}

	/**
	 * 获取用户绑定的药店信息
	 * @param  string] $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	public function get_drug_id( $client_id ){
		if ( !$client_id ) {
			return false;
		}

		$cinfo 	=	$this->get_client_info($client_id);

		if ( !$cinfo ) {
			return false;
		}

		$info 	=	M('kbox','oauth_','DB_CONFIG2')->where(array('clientid'=>$cinfo['id'],'status'=>1))->select();
		
		return $info ? $info : false;
	}


	/**
	 * 获取客户信息
	 * @param  string $client_id client_id
	 * @author wangyangyang
	 * @version V1.0 方法初始化 
	 */
	public function get_client_info( $client_id ){
		if ( !$client_id ) {
			return false;
		}

		$info 	=	M('client','oauth_','DB_CONFIG2')->where(array('client_id'=>$client_id))->find();

		return $info ? $info : false;
	}
}