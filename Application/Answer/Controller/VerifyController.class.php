<?php
namespace Answer\Controller;
use Think\Controller;


/**
 * 网络医院 问答咨询 API
 *  
 *  获取token
 * 
 * @author      wangyangyang
 * @version     V1.0
 */
class VerifyController extends Controller {
    
	// token 存活有效期 单位：秒
	private $tokenlifetime	=	'3600';

	private $apicode		=	'';

	public function __construct(){
		parent::__construct();

		$this->apicode		=	C('apicode');
	}


	/**
	 * 获取访问权限
	 * 	 
	 *  说明：
	 *    数据传输方式：POST 	
	 *    参数：appId 、appSecret
	 *    appId 、appSecret 由网站方提供
	 * 
	 * @return json 返回json格式数据
	 */
    public function index(){
    	$data 	=	array();

        if ( !IS_POST ) {
        	$data['code']		=	40001;
        	$data['message']	=	$this->apicode['40001'];

        	exit($this->ajaxReturn($data));
        }

        $appId		=	I('appid','','htmlspecialchars,trim,strip_tags');
        $appSecret	=	I('appsecret','','htmlspecialchars,trim,strip_tags');

        if ( !$appId || !$appSecret ) {
        	$data['code']		=	40002;
        	$data['message']	=	$this->apicode['40002'];

        	exit($this->ajaxReturn($data));
        }

        // 查询用户
        $where	=	array();
        $where['username']	=	$appId;
        $where['encrypt']	=	$appSecret;

       	$info	=	M('member')->where($where)->field('userid,islock,username')->find();
		
		if ( !$info ) {
			$data['code']		=	40003;
        	$data['message']	=	$this->apicode['40003'];

        	exit($this->ajaxReturn($data));
		}

		// 如果用户已经被锁定，则无权限在进行操作处理
		if ( $info['islock'] ) {
			$data['code']		=	40004;
        	$data['message']	=	$this->apicode['40004'];

        	exit($this->ajaxReturn($data));
		}

		$token 	=	$this->set_token();
		
		if ( !$token ) {
			$data['code']		=	'-1';
        	$data['message']	=	$this->apicode['-1'];

        	exit($this->ajaxReturn($data));
		}

		$data['code']			=	0;
		$data['message']		=	$this->apicode['0'];
		$data['token']			=	$token;
		$data['expires_in']		=	$this->tokenlifetime;

		S('AnswerApiUser',$info['userid'],$this->tokenlifetime);

		exit($this->ajaxReturn($data));
    }



    /**
     * 设置token
     * 
     * @param string $name session名称
     * @author wangyangyang
     * @version V1.0
     * @return string 加密的token值
     */
    private function set_token($name = 'AnswerApiToken') {
		$token	= md5(microtime(true));
		S($name,$token,$this->tokenlifetime);

		return $token;
	}

}