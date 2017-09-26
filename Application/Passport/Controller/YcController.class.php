<?php
namespace Passport\Controller;
use Passport\Controller\BaseController;


/**
 * 用户基本信息(网络医院数据池)
 * 
 * @author wangyangyang
 * @version V1.0
 */
class YcController extends BaseController {  

    public function __construct(){
    	parent::__construct();  

        if ( !IS_POST ) {
            $this->nopriv();
            exit;
        }
    }

    /**
     * 获取网络医院用户详情
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        $username     =   I('post.username','','htmlspecialchars,trim,strip_tags');
        $username     =   $username ? $username : '';

        if ( !$username ) {
            $this->noparam();
            exit;
        }

        $info   =   D('Ycmember')->getuserinfo( $username ) ;

        if ( !$info ) {
            oauthjson(30001,'未获取到用户信息');
            exit;
        }

        oauthjson(200,'获取成功',$info);
        exit;
    }


    /**
     * 补全用户基本信息
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function add(){
        $username   =   I('post.username','','htmlspecialchars,trim,strip_tags');   //  登录账号
        
        $Ycmember   =   D('Ycmember');

        // 先判断账号是否已经存在
        $info           =   $Ycmember->getmember( $username ) ;

        if ( $info ) {
            oauthjson(30002,'登录账号已经存在');
            exit;
        }

        // 添加用户信息，所有用户信息统一为普通用户
        $data   =   array();    //  主表信息
        $detail =   array();    // 附表信息
        
        // 普通用户
        $data['nickname']       =   I('post.nickname','','htmlspecialchars,trim,strip_tags');   //  用户昵称
        $data['username']       =   $username;

        // 性别转换为系统性别
        $sex    =   I('post.sex','','intval');   //  性别 1：男 2：女
        switch ( $sex ) {
            case '1':
                $sex    =   0;
                break;
            case '2':
                $sex    =   1;
                break;
            default:
                $sex    =   '';
                break;
        }
        $detail['sex']          =   $sex;                                
        $detail['birthday']     =   I('post.birthday','','htmlspecialchars,trim,strip_tags');   //  出生日期
        $detail['height']       =   I('post.height','','htmlspecialchars,trim,strip_tags');     //  身高
        $detail['bsugartype']   =   I('post.bsugartype','4','intval');                          //  糖尿病类型
        $detail['bsugartime']   =   I('post.bsugartime','','htmlspecialchars,trim,strip_tags'); //  糖尿病确诊日期


        $userid     =   $Ycmember->save_member($data,$detail);
        if ( !$userid ) {
            oauthjson(30003,'保存失败');
            exit;
        }

        $result =   array();
        $result['userid']   =   $userid;

        oauthjson(200,'获取成功',$result);
        exit;
    }


    /**
     * 更新用户基本信息
     * 
     * @return 
     */
    public function updateinfo(){
        $userid         =   I('userid','','intval');   //  用户id  

        $Membersystem   =   D('Ycmember');

        // 查询用户名所属模型
        $userinfo   =   $Membersystem->getuserbyid($userid);

        if ( !$userinfo ) {
            oauthjson(30004,'用户信息有误');
            exit;
        }

        $input  =   I('post.','','htmlspecialchars,trim,strip_tags');

        //  主表
        $member =   array('nickname');

        // 附表字段
        $field  =   array('sex','birthday','height','bsugartype','bsugartime');
        $result =   array();

        // 更新附表
        foreach ($field as $key => $value) {
            if (isset($input[$value])) {
                $result[$value] =   $input[$value];
            }
        }

        if ( $result ) {
            $Membersystem->save_member_detail($result,$userid);
        }
        
        // 更新主表
        $result     =   array();
        foreach ($member as $key => $value) {
            if (isset($input[$value])) {
                $result[$value] =   $input[$value];
            }
        }

        if ( $result ) {
            $Membersystem->save_member_sys($result,$userid);
        }
        
        oauthjson(200,'更新成功');
        exit;
    }


}

