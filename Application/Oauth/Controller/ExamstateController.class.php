<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 体检进程
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ExamstateController extends BaseController {  

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

        $drugsid=   extractArray($drugs,'drugsid');

        $info   =   D('Examstate')->lists($drugsid);

        if ( !$info ) {
            oauthjson(11001,'未获取到数据');
            exit;
        }

        foreach ($info as $key => $value) {
            $info[$key]['card'] =   $value['personid'];

            unset($info[$key]['personid']);
        }
        

        oauthjson(200,'获取成功',$info);
        exit;
    }


    /**
     * 体检进程
     *     人工结束体检进行
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function finish(){
        $id     =   I('post.id','','intval');
        
        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 查询id对应的卡号信息，判断其是否有操作权限
        $info   =   D('Examstate')->getinfo($id);

        if ( !$info ) {
            oauthjson(11002,'未查询到数据');
            exit;
        }

        // 获取用户绑定的药店信息
        $drugs      =   $this->oauth->get_drug_id($this->client_id);
       
        // 未查询到信息 返回无权限
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        $drugsid=   extractArray($drugs,'drugsid');
        if ( !in_array($info['drugid'],$drugsid) ) {
            $this->nopriv();
            exit;
        }

        // 结束流程
        $result =   D('Examstate')->finish($info['id']);;

        if ( !$result ) {
            oauthjson(11003,'结束失败');
            exit;
        }


        oauthjson(200,'结束成功');
        exit;
    }
}

