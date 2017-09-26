<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 人体图数据展示
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ProcessController extends BaseController {  

    // client_id
    private $client_id   =   '';

    public function __construct(){
    	parent::__construct();  
        $this->client_id    =   $this->oauth->verify_access_token();
        
        // 记录日志
        $this->visitlog( $this->client_id );

    }

    /**
     * 体检进程
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        // 获取用户绑定的药店信息
        $drugs      =   $this->oauth->get_drug_id($this->client_id);

        // 未查询到信息 返回无权限
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        // 当前页
        $p  =   I('post.p',1,'intval');
        $p  =   max(1,$p);
        
        // 每页展示条数
        $pagesize   =   I('post.pagesize','','intval');
        $pagesize   =   $pagesize ? intval($pagesize) : 10;
        if ($pagesize > 20 ) {
            $pagesize   =   20;
        }

        $drugsid=   extractArray($drugs,'drugsid');

        $info   =   D('Examstate')->listsbody($drugsid,$p,$pagesize);

        if ( !$info ) {
            oauthjson(12001,'未获取到数据');
            exit;
        }

        foreach ($info['info'] as $key => $value) {
            $info['info'][$key]['card'] =   $value['personid'];

            unset($info['info'][$key]['personid']);
        }
        
        oauthjson(200,'获取成功',$info);
        exit;
    }


    /**
     * 通过卡号查询最新的检测数据
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function show(){
        // 获取用户绑定的药店信息
        $drugs      =   $this->oauth->get_drug_id($this->client_id);

        // 未查询到信息 返回无权限
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        $card   =   I('post.card','','htmlspecialchars,trim,strip_tags');
        
        if ( !$card ) {
            $this->noparam();
            exit;
        }
        $cardinfo   =   $this->checkcardinfo( $card , $this->client_id );

        $type       =   I('post.type','0','intval');

        $typeArr    =   array(
            '0' => 'gl', '1' => 'bp', '2' => 'ox', 
            '3' => 'ur', '4' => 'bf', '5' => 'el',
            '6' => 'we'
        );

        $types  =   isset($typeArr[$type]) ? $typeArr[$type] : 'gl';

        // 查询数据，直接读取数据表
        $result     =   D('Exam')->getinfo($cardinfo['userid'],$types);

        if ( !$result ) {
            oauthjson(12002,'获取数据失败');
            exit;
        }

        oauthjson(200,'获取成功',$result);
        exit;
    }
}

