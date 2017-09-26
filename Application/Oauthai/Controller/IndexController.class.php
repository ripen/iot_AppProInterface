<?php
namespace Oauthai\Controller;
use Oauthai\Controller\BaseController;


/**
 * 疾病列表
 * 
 * @author wangyangyang
 * @version V1.0
 */
class IndexController extends BaseController {  

    public function __construct(){
    	parent::__construct();  
    }

    /**
     * 疾病列表
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        $id     =   I('get.id','','intval');
        $id     =   $id ? $id : 1;

        // 后续可增加其他类型大病分类
        if ( !in_array($id,array(1,2)) ) {
            $this->noparam();
            exit;
        }

        $info   =   D('Diseases')->lists( $id ) ;

        if ( !$info ) {
            oauthjson(20001,'暂无信息');
            exit;
        }

        
        oauthjson(200,'获取成功',$info);
        exit;
    }

}

