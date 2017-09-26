<?php
namespace Oauthmanage\Controller;

use Oauthmanage\Controller\BaseController;

/**
 * 应用管理
 * 
 * 
 */
class ApplyController extends BaseController{


    public function __construct(){
		parent::__construct();
	}

	/**
	 * 应用管理
     * 
     * @author wangyangyang
     * @version V1.0
	 */
    public function index(){
    	
        $p  =   I('p',1,'intval');
        $p  =   max($p,1);
        $pagesize   =   10;

        $curpage    =   ( $p - 1 ) * $pagesize;

        $db     =   M('client','oauth_','DB_CONFIG2');
        // 获取总数
        $total  =   $db->count();

        $list   =   array ();
        if ( $total ) {
            //获取用户基本信息
            $list   =   $db->order('create_time desc ,id desc') ->limit($curpage,$pagesize)->select();
        }

        $pages  =   getpage($total,$pagesize);

        $this->assign ('list', $list);
        $this->assign ('pages', $pages);

        $this->display();
    }

    /**
     * 添加应用
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function add(){
        $result   =   array();
        $result['status']   =   0;
        if ( !IS_POST ) {
            $result['msg']  =   '非法请求';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $title  =   I('post.title','','htmlspecialchars,trim,strip_tags');
        if ( !$title ) {
            $result['msg']  =   '名称不能为空';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $islock     =   I('post.islock','','intval');
        $types      =   I('post.types','','intval');
        $status     =   I('post.status','','intval');

        if (!$types || !in_array($types,array(1,2,3,4,5,6) ) ) {
            $types  =   1;
        }

        $data       =   array();
        $data['client_id']      =   random(10);
        $data['client_secret']  =   md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand())));
        $data['create_time']    =   time();
        $data['type']           =   2;
        $data['user_type']      =   $types ? $types : 1;
        $data['islock']         =   $islock ? 1 : 0;
        $data['title']          =   $title;
        $data['status']         =   $status ? $status : 1;

        $db     =   M('client','oauth_','DB_CONFIG2');

        $id     =   $db->add($data);

        if ( !$id ) {
            $result['msg']  =   '添加失败';
            $this->ajaxReturn($result,'JSON');
            exit;
        }
        $result['status'] =   1;

        $this->ajaxReturn($result,'JSON');
        exit;
    }

    /**
     * 更新应用
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function edits(){
        $result   =   array();
        $result['status'] =   0;
        if ( !IS_POST ) {
            $result['msg']  =   '非法请求';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $clientid   =   I('post.clientid','','intval');
        if ( !$clientid ) {
            $result['msg']  =   '非法操作';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $title  =   I('post.title','','htmlspecialchars,trim,strip_tags');
        if ( !$title ) {
            $result['msg']  =   '名称不能为空';
            $this->ajaxReturn($result,'JSON');
            exit;
        }


        $islock     =   I('post.islock','','intval');
        $types      =   I('post.types','','intval');

        if (!$types || !in_array($types,array(1,2,3,4,5,6) ) ) {
            $types  =   1;
        }

        $data       =   array();
        $data['user_type']      =   $types ? $types : 1;
        $data['islock']         =   $islock ? 1 : 0;
        $data['title']          =   $title;

        $db     =   M('client','oauth_','DB_CONFIG2');

        $id     =   $db->where( array('id'=>$clientid))->save($data);

        $result['status'] =   1;

        $this->ajaxReturn($result,'JSON');
        exit;
    }

    /**
     * 应用详情
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function getclient(){
        $id =   I('post.id',0,'intval');
        if ( !$id ) {
            exit();
        }

        $db     =   M('client','oauth_','DB_CONFIG2');

        $info   =   array();
        $info   =   $db->where( array('id'=>$id))->find();
        
        if ( !$info ) {
            exit();
        }

        echo $this->ajaxReturn ( $info );
        exit ();
    }



    /**
     * 权限查看
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function purview(){
        $id =   I('get.id',0,'intval');
        if ( !$id ) {
            redirect('/Oauthmanage');
            exit;
        }

        // 查询client信息
        $client =   M('client','oauth_','DB_CONFIG2')->where( array('id'=>$id))->find();

        if (!$client ) {
            redirect('/Oauthmanage');
            exit;
        }
        
        $this->assign('client',$client);

        $db     =   M('kbox','oauth_','DB_CONFIG2');

        $info   =   $db->where( array('clientid'=> $id ) )->order('addtime DESC')->select();

        $drugArr        =   array();

        // 查询对应药店信息
        if ( $info ) {
            $drugArr    =   extractArray($info,'drugsid');
        }

        $drugs          =   array();
        if ( $drugArr ) {
            $where      =   array();
            $where['userid']    =   array('in',$drugArr);

            $drugs      =   M('member')->where($where)->field('userid,username,nickname')->select();
        }

        $drugs  =   $drugs ? handleArrayKey($drugs,'userid') : array();

        if ( $info && $drugs ) {
            foreach ($info as $key => $value) {
                $info[$key]['dname']    =   isset($drugs[$value['drugsid']]) && $drugs[$value['drugsid']]['nickname'] ? $drugs[$value['drugsid']]['nickname'] : $drugs[$value['drugsid']]['username'];
            }
        }


        $this->assign('info',$info);
        $this->assign('clientid',$id);

        $this->display();
    }

    /**
     * 添加关联药店
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function purviewadd(){
        $result   =   array();
        $result['status'] =   0;
        if ( !IS_POST ) {
            $result['msg']  =   '非法请求';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $drugsid  =   I('post.drugsid','','intval');
        if ( !$drugsid ) {
            $result['msg']  =   '药店ID不能为空';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $clientid  =   I('post.clientid','','intval');
        if ( !$clientid ) {
            $result['msg']  =   '非法请求';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        // 判断药店是否存在
        $where      =   array();
        $where['userid']    =   $drugsid;
        $where['modelid']   =   array('in','35,41');
        $checkdrugs =   M('member')->where( $where )->field('userid')->find();

        if ( !$checkdrugs ) {
            $result['msg']  =   '药店ID有误';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $db     =   M('kbox','oauth_','DB_CONFIG2');
        // 判断是否已经添加过
        $check  =   $db->where( array('clientid'=>$clientid,'drugsid'=>$drugsid))->find();
        if ( $check ) {
            $result['msg']  =   '药店已经添加';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $islock     =   I('post.islock','','intval');

        $data       =   array();
        $data['clientid']   =   $clientid;
        $data['drugsid']    =   $drugsid;
        $data['status']     =   $islock ? 2 : 1;
        $data['addtime']    =   date('Y-m-d H:i:s');

        $id     =   $db->add($data);

        if ( !$id ) {
            $result['msg']  =   '添加失败';
            $this->ajaxReturn($result,'JSON');
            exit;
        }
        $result['status'] =   1;

        $this->ajaxReturn($result,'JSON');
        exit;
    }



    /**
     * 更新药店状态信息
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function purviewedit(){
        $result   =   array();
        $result['status'] =   0;
        if ( !IS_POST ) {
            $result['msg']  =   '非法请求';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $clientid   =   I('post.clientid','','intval');
        if ( !$clientid ) {
            $result['msg']  =   '非法操作';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $id  =   I('post.id','','intval');
        if ( !$id ) {
            $result['msg']  =   '非法请求';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $db     =   M('kbox','oauth_','DB_CONFIG2');

        // 判断是否已经添加过
        $check  =   $db->where( array('clientid'=>$clientid,'id'=>$id) )->find();
        if ( !$check ) {
            $result['msg']  =   '非法操作';
            $this->ajaxReturn($result,'JSON');
            exit;
        }

        $data   =   array();
        if ( $check['status'] == 1 ) {
            $data['status']     =   2;
            $result['status']   =   2;
        }else{
            $data['status']     =   1;
            $result['status']   =   1;
        }

        $id     =   $db->where( array('clientid'=>$clientid,'id'=>$id) )->save($data);

        $this->ajaxReturn($result,'JSON');
        exit;
    }
}



