<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 卡号管理API接口
 * 
 * @author wangyangyang
 * @version V1.0
 */
class CardController extends BaseController {  

    // client_id
    private $client_id   =   '';

    public function __construct(){
    	parent::__construct();  
        $this->client_id     =   $this->oauth->verify_access_token();

        // 记录日志
        $this->visitlog( $this->client_id );
    }

    /**
     * 判断卡号是否有效
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        $card   =   I('post.card','','htmlspecialchars_decode,trim,strip_tags');

        $this->checkcardstatus( $card );

        oauthjson(200,'卡号可以使用');
        exit;
    }

    /**
     * 绑定卡号
     *     性别 1：男 2：女
     *     身高 整数（cm）
     *     生日 （年-月-日）
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function bind(){
        // 性别
        $sex        =   I('post.sex','','intval');
        // 身高
        $height     =   I('post.height','','intval');

        // 生日
        $birthday   =   I('post.birthday','','htmlspecialchars_decode,trim,strip_tags');
        // 卡号
        $card       =   I('post.card','','htmlspecialchars_decode,trim,strip_tags');


        if ( !$sex || !$height || !$birthday ) {
            $this->noparam();
            exit;
        }

        if ( !in_array($sex,array(1,2) ) ) {
            oauthjson(20003,'性别参数错误');
            exit;
        }

        if ( !is_numeric($height) ) {
            oauthjson(20004,'身高参数错误');
            exit;
        }

        if ( !checkDateIsValid($birthday) ) {
            oauthjson(20005,'生日参数错误');
            exit;
        }

        // 判断卡号状态
        $cardinfo   =   $this->checkcardstatus( $card );

        // 开卡注册虚拟用户
        $sex    =   $sex == 1 ? 0 : 1;
        $userid =   D('Member')->add_bycard($sex,$height,$birthday);
        if ( !$userid ) {
            oauthjson(20006,'绑卡失败');
            exit;
        }

        // 更新卡号到具体用户
        $bind   =   D('Card')->updates($cardinfo['id'],$userid);

        if ( !$bind ) {
            oauthjson(20006,'绑卡失败');
            exit;
        }

        oauthjson(200,'绑卡成功');
        exit;
    }


    /**
     * 更新卡号基本信息
     *     性别 1：男 2：女
     *     身高 整数（cm）
     *     生日 （年-月-日）
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function update(){
        // 性别
        $sex        =   I('post.sex','','intval');
        // 身高
        $height     =   I('post.height','','intval');

        // 生日
        $birthday   =   I('post.birthday','','htmlspecialchars_decode,trim,strip_tags');
        // 卡号
        $card       =   I('post.card','','htmlspecialchars_decode,trim,strip_tags');


        if ( !$sex || !$height || !$birthday ) {
            $this->noparam();
            exit;
        }

        if ( !in_array($sex,array(1,2) ) ) {
            oauthjson(20003,'性别参数错误');
            exit;
        }

        if ( !is_numeric($height) ) {
            oauthjson(20004,'身高参数错误');
            exit;
        }

        if ( !checkDateIsValid($birthday) ) {
            oauthjson(20005,'生日参数错误');
            exit;
        }

        // 判断卡号状态
        $cardinfo   =   $this->checkcardinfo( $card , $this->client_id );
        if ($cardinfo['status'] != 1 ) {
            oauthjson(20007,'卡号未绑定用户');
            exit;
        }

        // 更新用户基本信息
        $sex    =   $sex == 1 ? 0 : 1;
        $data   =   array();
        $data['sex']        =   $sex;
        $data['height']     =   $height;
        $data['birthday']   =   $birthday;
        $result     =   D('Member')->updates($data,$cardinfo['userid']);

        if ( !$result ) {
            oauthjson(20008,'卡号更新失败');
            exit;
        }

        oauthjson(200,'更新成功');
        exit;
    }


    /**
     * 获取卡号状态信息
     * 
     * @param string $card 卡号
     * @author wangyangyang
     * @version V1.0
     */
    private function checkcardstatus( $card = '' ){
        $info   =   $this->checkcardinfo( $card , $this->client_id );
        
        // 判断卡号状态
        if ( $info['status'] != 0 ) {
            oauthjson(20002,'卡号已用');
            exit;
        }

        return $info;
    }
}

