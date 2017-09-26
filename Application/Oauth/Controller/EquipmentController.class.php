<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 设备列表
 * 
 * @author wangyangyang
 * @version V1.0
 */
class EquipmentController extends BaseController {  

    // client_id
    private $client_id   =   '';

    public function __construct(){
    	parent::__construct();  
        $this->client_id     =   $this->oauth->verify_access_token();
        
        // 记录日志
        $this->visitlog( $this->client_id );

    }

    /**
     * 设备列表
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        // 当前页
        $p  =   I('post.p',1,'intval');
        $p  =   max(1,$p);
        
        // 每页展示条数
        $pagesize   =   I('post.pagesize','','intval');
        $pagesize   =   $pagesize ? intval($pagesize) : 10;
        if ($pagesize > 20 ) {
            $pagesize   =   20;
        }

        // 获取用户绑定的药店信息
        $drugs      =   $this->oauth->get_drug_id($this->client_id);

        // 未查询到信息 返回无权限
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        $drugsid=   extractArray($drugs,'drugsid');

        $info   =   D('Equipment')->lists($drugsid,$p,$pagesize);

        if ( !$info ) {
            oauthjson(14001,'未获取到数据');
            exit;
        }

        oauthjson(200,'获取成功',$info);
        exit;
    }



    /**
     * 血糖数据分析
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function show(){
        $id     =   I('post.id','','intval');
        
        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 查询id对应的卡号信息，判断其是否有操作权限
        $info   =   D('Equipment')->getinfo($id);

        if ( !$info ) {
            oauthjson(14002,'未查询到数据');
            exit;
        }

        // 判断检测数据是否可展示
        $this->checkdatainfo( $info['userid'] ,$this->client_id );

        unset($info['userid']);

        oauthjson(200,'获取成功',$info);
        exit;

    }

    /**
     * 更新设备wifi信息
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function updates(){
        $id     =   I('post.id','','intval');
        $wifi   =   I('post.wifi','','htmlspecialchars,trim,strip_tags');
        $wifipw =   I('post.wifipw','','htmlspecialchars,trim,strip_tags');

        if ( !$id || !$wifi || !$wifipw ) {
            $this->noparam();
            exit;
        }

        if ( preg_match("/[\x{4e00}-\x{9fa5}]+/u",$wifi) ) {
            oauthjson(14003,'wifi名不能含有中文');
            exit;
        }

        if ( preg_match("/[\x{4e00}-\x{9fa5}]+/u",$wifipw) ) {
            oauthjson(14004,'wifi密码不能含有中文');
            exit;
        }

        // 查询id对应的卡号信息，判断其是否有操作权限
        $info   =   D('Equipment')->getinfo($id);

        if ( !$info ) {
            oauthjson(14002,'未查询到数据');
            exit;
        }

        // 判断检测数据是否可展示
        $this->checkdatainfo( $info['userid'] ,$this->client_id );

        $info   =   D('Equipment')->updates($id,$wifi,$wifipw);

        oauthjson(200,'更新耿功');
        exit;
    }
}

