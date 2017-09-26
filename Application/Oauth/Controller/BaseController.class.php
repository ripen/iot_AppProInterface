<?php
namespace Oauth\Controller;
use Think\Controller;



class BaseController extends Controller {
    
	public $oauth;

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
    	oauthjson(10002,'没有权限');
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
     * 获取卡号状态信息
     * 
     * @param string $card 卡号
     * @param string $client_id client_id
     * @author wangyangyang
     * @version V1.0
     */
    public function checkcardinfo( $card = '' , $client_id = '' ){
        if ( !$card ) {
            $this->noparam();
            exit;
        }

        // 判断用户是否含有药店
        $drugs  =   $this->oauth->get_drug_id( $client_id );
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        // 获取卡号信息
        $info   =   D('Card')->getcard($card);
        if ( !$info ) {
            oauthjson(20001,'卡号无效');
            exit;
        }

        // 判断卡号是否能被当前药店访问
        $drugsArr   =   extractArray($drugs,'drugsid');
        if ( !in_array($info['drugid'],$drugsArr) ) {
            oauthjson(20001,'卡号无效');
            exit;
        }

        return $info;
    }

    /**
     * 判断检测数据是否有权限可读
     * @param  string $drugid    药店ID
     * @param  string $client_id client_id
     * @return [type]            [description]
     */
    public function checkdatainfo( $drugid = '' , $client_id = '' ){
        if ( !$drugid ) {
            $this->nopriv();
            exit;
        }

        // 判断用户是否含有药店
        $drugs  =   $this->oauth->get_drug_id( $client_id );
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        // 判断卡号是否能被当前药店访问
        $drugsArr   =   extractArray($drugs,'drugsid');
        if ( !in_array($drugid,$drugsArr) ) {
            oauthjson(20001,'卡号无效');
            exit;
        }

        return $info;
    }
}

