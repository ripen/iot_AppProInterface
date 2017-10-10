<?php
namespace Pad\Controller;
use Think\Controller;


class BaseController extends Controller {
    
	public $oauth;

    public $client_id;

    public $client_info;
    public function __construct(){
    	parent::__construct();

    	header("Content-Type: application/json");  
        header("Cache-Control: no-store");  

		$this->oauth = new \Org\OAuth\ThinkOAuth2();

    }

    /**
     * 缺少参数统一返回
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function noparam(){
    	oauthjson(10001,'缺少参数');
    }


    /**
     * 没有权限
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function nopriv(){
    	oauthjson(10002,'没有权限!!');
    }


    /**
     * 日志记录
     *     记录用户所有的访问记录
     * 
     * @param string $client_id client_id
     * @author wangyangyang
     * @version V1.0
     */
    public function visitlog( $client_id = '' ){
        if ( !$client_id ) {
            return false;
        }

        $data   =   array();
        $data['visitime']   =   date('Y-m-d H:i:s');
        $data['client_id']  =   $client_id;
        $data['m']          =   MODULE_NAME;
        $data['c']          =   CONTROLLER_NAME;
        $data['a']          =   ACTION_NAME;
        $data['request']    =   REQUEST_METHOD;
        $data['data']       =   $_REQUEST ? json_encode($_REQUEST) : '';
        $data['ip']         =   get_client_ip();
        M('visitlog','oauth_','DB_CONFIG2')->add($data);

        return true;
    }


    
    /**
     * 判断用户是否有可访问权限
     * @param  string $client_id
     * @return [type]            [description]
     */
    public function checkpriv( $client_id = '' ){

        if ( !$client_id ) {
            $this->nopriv();
            exit;
        }

        // 判断应用类型
        $info     =   $this->oauth->get_client_info( $client_id );
        if (!$info || $info['status'] != 4 ) {
            $this->nopriv();
            exit;
        }
        return $info;
    }
}

