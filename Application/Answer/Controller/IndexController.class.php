<?php
namespace Answer\Controller;
use Think\Controller;

/**
 * 网络医院 问答咨询 API
 * 
 *
 * @author      wangyangyang
 * @version     V1.0
 */
class IndexController extends Controller {
    

	private $apicode		=	'';

	public function __construct(){
		parent::__construct();

		$this->apicode		=	C('apicode');
	}


	/**
	 * 添加问答咨询内容
	 * 	 
	 *  说明
	 *    数据传输方式 POST 	
	 *    参数
	 *    	token 		通过授权获得  (必填)
	 *    	title 		标题          (必填)
	 *    	content 	病情描述      (必填)
	 *    	img 		病情图片      ( 图片已 url 方式提交，指向第三方图片地址 )
	 *    	catid 		分类id	
	 *    	sid 		子类id
	 *    	sex 		用户性别 （0男,1:女）
	 *    	age 		用户年龄
	 *    	connectid 	第三方关联字段
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

        $token		=	I('token','','htmlspecialchars,trim,strip_tags');
        // 病情标题
        $title		=	I('title','','htmlspecialchars,trim,strip_tags');
        // 病情描述
        $content	=	I('content','','htmlspecialchars,trim,strip_tags');
        // 病情图片
        $imgs       =   I('imgs','','htmlspecialchars,trim');

        // 用户性别、年龄
        $sex		=	I('sex','','intval');
        $age		=	I('age','','intval');

        // 咨询分类
        $catid		=	I('catid',0,'intval');
        $sid		=	I('sid',0,'intval');

        // 第三方关联关系
        $connectid	=	I('connectid','','htmlspecialchars,trim,strip_tags');

        if ( !$token || !$title || !$content || !$connectid ) {
        	$data['code']		=	40002;
        	$data['message']	=	$this->apicode['40002'];

        	exit($this->ajaxReturn($data));
        }

        // 判断token
        $gettoken 	=	$this->get_token();
        if ( !$gettoken || $token != $gettoken ) {
        	$data['code']		=	40005;
        	$data['message']	=	$this->apicode['40005'];

        	exit($this->ajaxReturn($data));
        }

        // 获取用户id
        $userid 	=	S('AnswerApiUser');

        if ( !$userid ) {
        	$data['code']		=	40001;
        	$data['message']	=	$this->apicode['40001'];

        	exit($this->ajaxReturn($data));
        }

        $info   =	array();
        $info['userid']		=	$userid;
        $info['datetime']	=	time();
        $info['ip']			=	get_client_ip();
        $info['catid']		=	$catid;
        $info['sid']		=	$sid;
        $info['status'] 	= 	1;
	    $info['isdisplay'] 	= 	0;

        $info['title']		=	$title;
        $info['content']	=	$content;
        $info['sex']		=	$sex ? $sex : '99';
        $info['age']		=	$age;

        $info['source']		=	$userid;
        $info['connectid']	=	$connectid;

        // 图片以url地址方式提交 判断图片格式是否为url地址
        $imgarr     =   array();
        if ( $imgs ) {
            $imgarr =   explode(',',$imgs);
            $imgarr =   array_unique(array_filter($imgarr));
        }
        if ( $imgarr ) {
            foreach ($imgarr as $key => $value) {
                $check  =   $this->url_exists($value);
                if (!$check) {
                    unset($imgarr[$key]);
                }
            }
            $info['img']    =   implode(',',$imgarr);
        }


        $insert	=	M('form_kangbao_ask')->add($info);

        if ( !$insert ) {
        	$data['code']		=	'-1';
        	$data['message']	=	$this->apicode['-1'];

        	exit($this->ajaxReturn($data));
        }

        $data['code']		=	'0';
    	$data['message']	=	$this->apicode['0'];

    	exit($this->ajaxReturn($data));
    }


    /**
	 * 问答分类
	 * 	 
	 *  说明
	 *    数据传输方式 POST 	
	 *    参数
	 *    	token 		通过授权获得 			（必填）
	 * 
	 * @return json 返回json格式数据
	 */
    public function category(){
    	$data 	=	array();

        if ( !IS_POST ) {
        	$data['code']		=	40001;
        	$data['message']	=	$this->apicode['40001'];

        	exit($this->ajaxReturn($data));
        }

        $token		=	I('token','','htmlspecialchars,trim,strip_tags');
       
        if ( !$token ) {
        	$data['code']		=	40002;
        	$data['message']	=	$this->apicode['40002'];

        	exit($this->ajaxReturn($data));
        }

        // 判断token
        $gettoken 	=	$this->get_token();
        if ( !$gettoken || $token != $gettoken ) {
        	$data['code']		=	40005;
        	$data['message']	=	$this->apicode['40005'];

        	exit($this->ajaxReturn($data));
        }

        // 获取分类
        $category	=	S('answer_model_202');
        if( !$category ){
            $McateModel =   new \Answer\Model\McateModel;

			$category	=	$McateModel->getmodel();
		}

		if ( !$category ) {
			$data['code']		=	'-1';
        	$data['message']	=	$this->apicode['-1'];

        	exit($this->ajaxReturn($data));
		}

        $info   =   array();
        foreach ($category as $key => $value) {
            $info[$key]['id']       =   $value[1];
            $info[$key]['title']    =   $value[0];
            $info[$key]['sub']      =   $value['sub'];
        }

		$data['code']		=	0;
		$data['message']	=	$this->apicode['0'];
		$data['category']	=	$info;

		exit($this->ajaxReturn($data));
    }

    
   
	/**
     * 获取token
     *
     * @param string $name 系统保存的 token session 名
     * @author wangyangyang
     * @version V1.0
     */
	private function get_token($name = 'AnswerApiToken') {
		return S($name);
	}


    /**
     * 判断url图片地址是否存在
     *     
     * 
     * @param  string $url 图片地址
     * @return bool true/false
     */
    private function url_exists($url = '') {
        if ( !$url ) {
            return false;
        }


        $ch = curl_init(); 
        curl_setopt ($ch, CURLOPT_URL, $url); 
        //不下载
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        //设置超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
        if($http_code == 200) {
            return true;
        }
        return false;
    }
}