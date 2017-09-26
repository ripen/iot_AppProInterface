<?php
namespace Oauthai\Controller;
use Oauthai\Controller\BaseController;


/**
 * 解决方案 
 *     疾病改善
 * 
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ImproveController extends BaseController {  

    public function __construct(){
    	parent::__construct();
    }

    /**
     * 疾病改善列表
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
            oauthjson(40001,'暂无信息');
            exit;
        }

        $lists   =   D('Diseases')->improvelists( $id , $page , $pagesize ) ;
        
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

    /**
     * 疾病改善列表详情
     * 
     * @return [type] [description]
     */
    public function show(){
        // 改善ID
        $id     =   I('post.id','','intval');
        $id     =   $id ? $id : '';

        // 疾病id
        $did    =   I('post.did','','intval');
        $did    =   $did ? $did : '';

        if ( !$id || !$did ) {
            $this->noparam();
            exit;
        }

        // 判断疾病与改善详情是否有关联关系
        $check  =   D('Diseases')->checkimprove($id,$did);
        if ( !$check ) {
            oauthjson(40003,'获取详情失败');
            exit;
        }

        // 查询疾病信息
        $dinfo  =   D('Diseases')->show($did);
        if ( !$dinfo ) {
            oauthjson(40002,'暂无信息');
            exit;
        }

        // 疾病改善详情
        $info   =   D('Diseases')->improveshow( $id ) ;
        if ( $info ) {
            $info['img']    =   $info['img'] ? C('AI_HOST').$info['img'] : '';
            $info['addtime']=   date('Y-m-d',$info['addtime']);
        }

        $result     =   array();
        $result['info'] =   $info ? $info : array();
        $result['dinfo']=   $dinfo ? $dinfo : array();        

        oauthjson(200,'获取成功',$result);
        exit;
    }
}

