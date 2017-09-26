<?php
namespace Oauthai\Controller;
use Oauthai\Controller\BaseController;


/**
 * 解决方案 
 *     保健护理
 * 
 * @author wangyangyang
 * @version V1.0
 */
class NursingController extends BaseController {  

    public function __construct(){
        
    	parent::__construct();
    }

    /**
     * 保健护理
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        $id     =   I('post.id','','intval');
        $id     =   $id ? $id : '';

        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 查询疾病信息
        $info  =   D('Diseases')->show($id);
        if ( !$info ) {
            oauthjson(70001,'暂无信息');
            exit;
        }

        $reuslt         =   array();
        // $result['link'] =  'https://h5.youzan.com/v2/showcase/feature?alias=8b55yfas&sf=wx_menu&redirect_count=1&sls=o24Rg4';   

        $result['link']     =   'https://mp.weixin.qq.com/s?__biz=MzI5NDU4NTYzNQ==&mid=2247484183&idx=1&sn=774828985c1fbc505692e4a4c1fb72b1&chksm=ec61d75bdb165e4d1d1e238df695513501e31fa121cc7eb41e3cb3e09c7e58c055b7612aaaeb#rd';

        $data           =   array();
        $data['info']   =   $info;
        $data['result'] =   $result;

        oauthjson(200,'获取成功',$data);
        exit;
    }
}

