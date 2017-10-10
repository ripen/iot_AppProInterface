<?php
namespace Pad\Controller;
use Pad\Controller\BaseController;

class IndexController extends BaseController {
   
    public function __construct(){
    	parent::__construct();  
    }

    //权限验证  
    public function authorize() {
        $auth_params    =   $this->oauth->post_authorize_params();

        if ( $auth_params && isset($auth_params['client_id']) && is_array($auth_params) )
        {
            $this->oauth->finish_client_authorization( true, 
                $auth_params['response_type'],$auth_params['client_id'],'',
                $auth_params['state'], $auth_params['scope'] );
        }
        exit;
    }

    /**
    * 获取 access_token
    * @author       wangyangyang
    * @copyright    wangyang8839@163.com
    * @version      1.0
    * @param
    */
    public function access_token(){
        $token  =   $this->oauth->grant_access_token();
    }
}

