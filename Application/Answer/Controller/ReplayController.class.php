<?php
namespace Answer\Controller;
use Think\Controller;

/**
 * 网络医院 问答咨询 API
 *  医生回复后反馈
 *
 * @author      wangyangyang
 * @version     V1.0
 */
class ReplayController extends Controller {
    

	private $apicode		=	'';

	public function __construct(){
		parent::__construct();

		$this->apicode		=	C('apicode');
	}


	/**
	 * 回复问答咨询内容
	 * 	 
	 *  说明
	 *    数据传输方式 POST 	
	 *    参数
	 *    	token       通过授权获得  (必填)
	 *    	id 		    咨询标题      (必填)
     *      replayid    回复id        (必填)  
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

        $token  =	I('token','','htmlspecialchars,trim,strip_tags');
        // 病情ID
        $id     =	I('id','','intval');
        
        // 病情replayid
        $replayid     =   I('replayid','','intval');

        if ( !$token || !$id || !$replayid ) {
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

        // 通过ID查找咨询信息
        $where  =   array();
        $where['dataid']    =   $id;
        $info   =   M('form_kangbao_ask')->where($where)->field('dataid,title,content,img,source,connectid')->find();

        // 未查找到数据，无来源 无第三方关联关系的，返回失败
        if ( !$info || !$info['source'] || !$info['connectid'] ) {
            $data['code']       =   40006;
            $data['message']    =   $this->apicode['40006'];

            exit($this->ajaxReturn($data));
        }

        // 根据 source 查询 API 用户信息，获得callback回调地址
        $where      =   array();
        $where['userid']    =   $info['source'];
        $apiinfo    =   M('member_api')->where($where)->find();

        if ( !$apiinfo || !$apiinfo['callback'] ) {
            $data['code']       =   40003;
            $data['message']    =   $this->apicode['40003'];

            exit($this->ajaxReturn($data));
        }

        // 获取问答回复内容
        $where      =   array();
        $where['dataid']    =   $replayid;
        $where['contentid'] =   $id;
        $repinfo    =   M('form_kangbao_ask_replay')->where($where)->field('dataid,datetime,doctorid,content')->find();

        if ( !$repinfo ) {
            $data['code']       =   40007;
            $data['message']    =   $this->apicode['40007'];

            exit($this->ajaxReturn($data));
        }

        // 获取医生用户信息
        $where      =   array();
        $where['userid']    =   $repinfo['doctorid'];
        $dinfo          =   M('member')->where($where)->field('username,nickname')->find();
        $dinfoDetile    =   M('member_kdoctor')->where($where)->field('sex,hospital,subject,job,goodat')->find();
        if ( !$dinfo || !$dinfoDetile ) {
            $data['code']       =   40007;
            $data['message']    =   $this->apicode['40007'];

            exit($this->ajaxReturn($data));
        }

        $dinfo  =   array_merge($dinfo,$dinfoDetile);
        if ( $dinfo['sex'] && $dinfo['sex'] == 1 ) {
            $dinfo['sex']   =   '男';
        }else if ( $dinfo['sex'] && $dinfo['sex'] == 2 ) {
            $dinfo['sex']   =   '女';
        }
        $dinfo['username']  =   $dinfo['nickname'] ? $dinfo['nickname'] : $dinfo['username'] ;


        // 处理数据，使用curl post提交到第三方需要反馈的地址
        $result =   array();
        $result['connectid']    =   $info['connectid'];
        $result['title']        =   $info['title'];
        $result['replay']       =   $repinfo['content'];
        $result['replaytime']   =   $repinfo['datetime'] ? date('Y-m-d') : '';
        $result['doc_info']     =   $dinfo['username'];
        $result['doc_sex']      =   $dinfo['sex'];
        $result['doc_hospital'] =   $dinfo['hospital'];
        $result['doc_subject']  =   $dinfo['subject'];
        $result['doc_job']      =   $dinfo['job'];
        $result['doc_goodat']   =   $dinfo['goodat'];


        // curl提交数据
        $result =   Spost($apiinfo['callback'],$result);

        $result['code']       =   0;
        $result['message']    =   $this->apicode['0'];
        
        exit($this->ajaxReturn($result));
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


}