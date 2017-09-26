<?php
namespace Oauthai\Controller;
use Oauthai\Controller\BaseController;


/**
 * 解决方案 
 *     药品改善
 * 
 * 
 * @author wangyangyang
 * @version V1.0
 */
class DrugsController extends BaseController {  

    public function __construct(){
    	parent::__construct();
    }

    /**
     * 药品改善列表
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        $id     =   I('post.id','','intval');
        $id     =   $id ? $id : '';

        $page       =   I('post.page','','intval');
        $pagesize   =   I('post.pagesize','','intval');

        $page       =   $page ? $page : 1;

        if ( !$pagesize || $pagesize > 20 ) {
            $pagesize   =   20;
        }

        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 查询疾病信息
        $info  =   D('Diseases')->show($id);
        if ( !$info ) {
            oauthjson(50001,'暂无信息');
            exit;
        }

        // 增加商品类型处理
        // 商品类型 1药品，2保健品，3医疗器械，4食品，5虚拟商品，6其他 
        // 7：中药控糖 8:西药控糖 9：保健品控糖 9：穴位贴敷
        $type    =  I('post.type','','intval');
        $type    =  $type ? $type : 1;


        $lists   =   D('Diseases')->shopslists( $id , $page , $pagesize , $type ) ;
        
        // 处理图片地址，时间戳格式
        if ( $lists ) {
            $hosts  =   C('AI_HOST');
            foreach ($lists as $key => $value) {
                $lists[$key]['img'] =   $value['img'] ? $hosts.$value['img'] : '';
                $lists[$key]['addtime'] =   date('Y-m-d',$value['addtime']);
            }
        }

        $result     =   array();
        $result['info']     =   $info ? $info : array();
        $result['lists']    =   $lists ? $lists : array();
        $result['page']     =   $page;
        $result['pagesize'] =   $pagesize;

        oauthjson(200,'获取成功',$result);
        exit;
    }
}

