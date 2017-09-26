<?php
namespace Passport\Controller;
use Passport\Controller\BaseController;


/**
 * 用户基本信息
 * 
 * @author wangyangyang
 * @version V1.0
 */
class IndexController extends BaseController {  

    public function __construct(){
    	parent::__construct();  

        if ( !IS_POST ) {
            $this->nopriv();
            exit;
        }
    }

    /**
     * 获取用户池内的用户详情
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

        $info   =   D('Membersystem')->getuserinfo( $username ) ;

        if ( $info['status'] != 3 ) {
            oauthjson(20001,$info['message']);
            exit;
        }

        oauthjson(200,'获取成功',$info['info']);
        exit;
    }


    /**
     * 补全用户基本信息（ 注册 ）
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function add(){
        $username   =   I('post.username','','htmlspecialchars,trim,strip_tags');   //  登录账号
        
        $Membersystem    =   D('Membersystem');


        // 先判断账号是否已经存在
        $info            =   $Membersystem->getmembersystem( $username ) ;

        if ( $info ) {
            oauthjson(20002,'登录账号已经存在');
            exit;
        }

        // 保存到主表中
        $member     =   array();
        $member['kuserid']      =   I('post.kuserid',0,'intval');  //  原网络医院用户ID
        $member['muserid']      =   I('post.muserid',0,'intval');  //  原丰拓用户ID
        // 密码
        $password               =   I('post.password','','htmlspecialchars,trim,strip_tags'); 
        $random                 =   random(6);
        if ( $password ) {
            $member['password']     =   password($password,$random);
            $member['encrypt']      =   $random;
        }
        
        $member['updatetime']   =   time();

        // 判断网络医院ID或者丰拓用户ID是否已经存在
        $checkmember    =   $Membersystem->checkmember($member['kuserid'],$member['muserid']);

        if ( !$checkmember ) {
            $member['addtime']      =   time();
            $userid     =   $Membersystem->add_member($member);
        }else{
            $userid     =   $checkmember;
        }

        if ( !$userid ) {
            oauthjson(20003,'保存失败');
            exit;
        }

        // 保存到登录账号体系表
        $system     =   array();
        $system['userid']   =   $userid;
        $system['username'] =   $username;
        $system['addtime']  =   time();
        $system['regtime']  =   I('post.regtime','','intval');  //  注册时间
        $system['regtype']  =   I('post.regtype','0','intval');  //  注册来源
        $system['systype']  =   I('post.systype','0','intval');  //  系统来源
        $system['acctype']  =   I('post.acctype','0','intval');  //  账户类型
        $system['modelid']  =   I('post.modelid','0','intval');  //  用户类型

        $id     =   $Membersystem->add_member_system($system);
        if ( !$id ) {
            oauthjson(20004,'保存失败');
            exit;
        }

        $detail =   array();
        $detail['userid']   =   $userid;
        $detail['modelid']  =   $system['modelid'];
        // 附属表信息,需要根据用户类型进行不同数据的添加，判断
        $input  =   I('post.','','htmlspecialchars,trim,strip_tags');
        $postdata   =   $this->handledata($input,$system['modelid']);
        $postdata   =   $postdata ? $postdata : array();

        $detail     =   array_merge($detail,$postdata);

        $id     =   $Membersystem->save_member_detail($detail,$checkmember);
        if ( !$id ) {
            oauthjson(20005,'保存失败');
            exit;
        }

        $result =   array();
        $result['userid']   =   $userid;
        $result['kuserid']  =   $member['kuserid'];
        $result['muserid']  =   $member['muserid'];

        oauthjson(200,'获取成功',$result);
        exit;
    }

    /**
     * 更新muserid 或者kuserid字段
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function updatekmuserid(){
        $userid     =   I('userid','','intval');   //  用户池id

        $fuserid    =   I('fuserid','','intval');   //  1：网络医院用户ID 2：丰拓用户ID

        $type       =   I('type','1','intval');   //  1：网络医院用户ID 2：丰拓用户ID

        if ( !$userid || !$type || !$fuserid ) {
            $this->noparam();
            exit;
        }

        $Membersystem   =   D('Membersystem');

        // 获取用户池用户ID信息
        $userinfo       =   $Membersystem->getuserbyid($userid);

        if ( !$userinfo ) {
            oauthjson(20007,'用户信息有误');
            exit;
        }

        $data   =   array();
        $data['updatetime'] =   time();
        switch ($type) {
            case 1:
                $data['kuserid']    =   $fuserid;
                break;
            case '2':
                $data['muserid']    =   $fuserid;
                break;
        }


        $Membersystem->updatekmuserid($userid,$data);

        oauthjson(200,'更新成功');
        exit;
    }

    /**
     * 更新密码
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function updatepass(){
        $userid     =   I('userid','','intval');   //  用户id

        $type       =   I('type','0','intval');   //  用户类型 0：用户池ID 1：网络医院用户ID 2：丰拓用户ID
        
        $password   =   I('password','','htmlspecialchars,trim,strip_tags');   //  老密码

        $newpass    =   I('newpass','','htmlspecialchars,trim,strip_tags');   //  老密码

        if ( !$newpass || !$userid ) {
            $this->noparam();
            exit;
        }

        if ( $password == $newpass ) {
            oauthjson(20006,'新旧密码一致');
            exit;
        }

        $Membersystem   =   D('Membersystem');

        // 判断旧密码是否正确
        $userpass       =   $Membersystem->getuserbyid($userid,$type);

        if ( !$userpass ) {
            oauthjson(20007,'用户信息有误');
            exit;
        }

        $mpass      =   password($password,$userpass['encrypt']);

        if ( $mpass != $userpass['password'] && $userpass['password'] && $password ) {
            oauthjson(20008,'旧密码有误');
            exit;
        }

        $encrypt    =   random(6);
        $new        =   password($newpass,$encrypt);

        // 更新密码
        $info       =   $Membersystem->updatepass($userpass['userid'],$new,$encrypt);

        if (!$info) {
            oauthjson(20009,'更新密码失败');
            exit;
        }

        oauthjson(200,'更新密码成功');
        exit;
    }


    /**
     * 更新用户基本信息
     * 
     * @return 
     */
    public function updateinfo(){
        $userid     =   I('userid','','intval');   //  用户id

        $type       =   I('type','0','intval');   //  用户类型 0：用户池ID 1：网络医院用户ID 2：丰拓用户ID

        $Membersystem   =   D('Membersystem');

        // 查询用户名所属模型
        $userinfo   =   $Membersystem->getuserbyid($userid,$type);
        if ( !$userinfo ) {
            oauthjson(20007,'用户信息有误');
            exit;
        }

        $input  =   I('post.','','htmlspecialchars,trim,strip_tags');

        $detail =   array();
        $detail =   $this->handledata($input,$userinfo['modelid']);
        $detail['userid']   =   $userinfo['userid'];

        $Membersystem->save_member_detail($detail,$detail['userid']);

        oauthjson(200,'更新成功');
        exit;
    }

    /**
     * 处理更新字段时候的数据信息
     * @param  array $data    
     * @param  [type] $modelid [description]
     * @return [type]          [description]
     */
    private function handledata($data,$modelid){
        if ( !$data || !is_array($data) ) {
            return false;
        }

        // 附属表信息,需要根据用户类型进行不同数据的添加，判断
        switch ($modelid) {
            case 0:
                // 普通用户 member_detail
                $field    =   array('nickname','avatar','sex','birthday','height','bsugartype','bsugartime');
               
                // nickname     用户昵称
                // avatar       头像地址
                // sex          性别 1：男 2：女
                // birthday     出生日期
                // height       身高
                // bsugartype   糖尿病类型
                // bsugartime   糖尿病确诊日期
                break;

            case 1:
                // 怡成专家 member_ycdoctor
                $field    =   array('nickname','avatar','sex','hospital','subject','job','goodat','content','work');
                
                // nickname 用户昵称
                // avatar   头像地址
                // sex      性别 1：男 2：女
                // hospital 所属医院
                // subject  科室
                // job      职称
                // goodat   擅长
                // content  医生简介
                // work     值班时间
                break;
            case '2':
                // 丰拓医生 member_mndoctor
                $field    =   array('nickname','avatar','sex','hospital','subject','job','goodat','content','researchfieldid','workplace','department','actived');

                // nickname 用户昵称
                // avatar   头像地址
                // sex      性别 0：男 1：女 3：保密
                // hospital 所属医院
                // subject  科室
                // job      职称
                // goodat   擅长
                // content  医生简介
                // researchfieldid  研究领域
                // workplace        工作单位
                // department       工作科室
                // actived          普通医生角色是否有效 0-无效 1-有效
                break;

            case '3':
                // 丰拓院长 member_mndean
                $field    =   array('nickname','avatar','sex','hospitalnumber','abstract','activerole');
                //  nickname 昵称
                //  avatar   头像
                //  sex      性别 0：男 1：女 3：保密
                //  hospitalnumber 医院ID 院长所在医院编号
                //  abstract 院长简介
                //  activerole 院长卡权限 默认2,1为拥有权限
                break;

            case '4':
                // 丰拓商务 member_mnbusiness
                $field    =   array('nickname','avatar','sex','abstract','stationrole');
                //  nickname 昵称
                //  avatar   头像
                //  sex      性别 0：男 1：女 3：保密
                //  abstract 商务卡简介
                //  stationrole 商务卡用户权限 默认0,1为拥有权限
                break;

            case '5':
                // 丰拓健康管理师 member_mnhealthmanager
                $field    =   array('nickname','avatar','sex','hospital','subject','job','goodat','content','researchfieldid','workplace','department','actived');

                // nickname 用户昵称
                // avatar   头像地址
                // sex      性别 0：男 1：女 3：保密
                // hospital 所属医院
                // subject  健康管理师科室
                // job      职称
                // goodat   擅长
                // content  健康管理师简介
                // researchfieldid  研究领域
                // workplace        工作单位
                // department       工作科室
                // actived          普通健康管理师角色是否有效 0-无效 1-有效
                break;
            case '6':
                // 丰拓站长  member_mnstation
                $field    =   array('nickname','type','title','phone','businessuserid');
                // businessuserid 商务代表ID 体检站激活需要用商务ID绑定 关联用户表userid
                // nickname 体检站昵称
                // type 体检站类型 目前只有一种 综合型体检
                // title 体检站标语
                // phone 联系电话
                break;
            case '7':
                // 怡成CRM member_ycstore
                $field    =   array('nickname','introduction','mechanism','contactsname');
                //  nickname 昵称
                //  introduction 简介
                //  mechanism 用户全称
                //  contactsname 联系人
                break;
            case '8':
                // 怡成商业定制 member_ycbstore
                $field    =   array('nickname','introduction','mechanism','contactsname');
                //  nickname 昵称
                //  introduction 简介
                //  mechanism 用户全称
                //  contactsname 联系人
                break;
        }

        $result     =   array();
        foreach ($field as $key => $value) {
            if ( isset($data[$value]) ) {
                $result[$value] =   $data[$value];
            }
        }

        return $result;
    }
}

