<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 检测报告
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ReportController extends BaseController {  

    // client_id
    private $client_id   =   '';

    public function __construct(){
    	parent::__construct();  
        $this->client_id     =   $this->oauth->verify_access_token();
       
        // 记录日志
        $this->visitlog( $this->client_id );

    }

    /**
     * 报告列表信息
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

        $info   =   D('Report')->lists($drugsid,$p,$pagesize);


        if ( !$info ) {
            oauthjson(13001,'未获取到数据');
            exit;
        }

        // 查询卡号
        $data       =   $info['info'];
        $cardid     =   extractArray($data,'cardid');
        $cardinfo   =   D('Card')->getcardbyidarr($cardid);

        // 返回数据为展示卡号
        foreach ($data as $key => $value) {
            if (isset($cardinfo[$value['cardid']]) && $cardinfo[$value['cardid']] ) {
                $data[$key]['card'] =   $cardinfo[$value['cardid']]['wholecard'];
            }else{
                $data[$key]['card'] =   '';
            }

            unset($data[$key]['cardid']);
        }
        $info['info']   =   $data;

        oauthjson(200,'获取成功',$info);
        exit;
    }


    /**
     * 血糖数据列表信息
     *     具体某张卡号的所有数据
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function lists(){
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

        $card   =   I('post.card','','htmlspecialchars,trim,strip_tags');
        $card   =   '1300000391';
        if ( !$card ) {
            $this->noparam();
            exit;
        }
        $cardinfo   =   $this->checkcardinfo( $card , $this->client_id );
    
        // 查询用户数据列表信息 
        $info   =   D('Report')->listsbycard($cardinfo['id'],$p,$pagesize);
        if ( !$info ) {
            oauthjson(13002,'未获取到数据');
            exit;
        }


        oauthjson(200,'获取成功',$info);
        exit;
    }

    /**
     * 报告数据详情
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
        $info   =   D('Report')->getinfo($id);

        if ( !$info ) {
            oauthjson(13003,'未查询到数据');
            exit;
        }

        // 判断检测数据是否可展示
        $this->checkdatainfo( $info['drugid'] ,$this->client_id );


        // 通过统一接口，读取报告信息
        $analyreport    =   new \Oauth\Common\analyreport();
        $data   =   $analyreport->reporttable($id,false);
        
        if ( !$data ) {
            oauthjson(13003,'未查询到数据');
            exit;
        }

        // 判断卡号是否为空
        if ( !$data['cardinfo']) {
            $cardinfo   =   D('Card')->getcardbyid($info['cardid']);
            $data['cardinfo']   =   $cardinfo ? $cardinfo['wholecard'] : '';
        }
        
        // 删除用户基本信息，只保留性别、身高、生日
        if ( isset($data['userinfo']) ) {
            unset($data['userinfo']['userid'],$data['userinfo']['username'],$data['userinfo']['nickname'],$data['userinfo']['age']);
        }
        
        // 判断是否有心电，如有心电，单独处理心电解读方法
        $elinfo     =   array();
        $eldata     =   isset($data['table']['el']) ? $data['table']['el'] : array();
        if ( isset($eldata['el']['report']) && $eldata['el']['report']['qid'] ) {
            $elinfo     =   D('El')->getanswer($eldata['el']['report']['qid']);
            unset($data['table']['el']['el']['report']);
        }
        $data['elinfo'] =   $elinfo ? $elinfo : array();

        oauthjson(200,'获取成功',$data);
        exit;

    }


    /**
     * 更新进食状态
     * 
     * @return [type] [description]
     */
    public function updates(){
        $id     =   I('post.id','','intval');
        $type   =   I('post.type','','intval');
        
        if ( !$id || !$type ) {
            $this->noparam();
            exit;
        }

        // 判断进食状态是否有效
        $data    =   array(
            '1'=>'空腹血糖','2'=>'早餐后2小时血糖','3'=>'随机血糖',
            '5'=>'午餐前血糖','6'=>'午餐后2小时血糖','7'=>'晚餐前血糖',
            '8'=>'晚餐后2小时血糖','9'=>'睡前血糖'
        );
        if (!isset($data[$type])) {
            oauthjson(13004,'进食状态有误');
            exit;
        }

        // 查询id对应的卡号信息，判断其是否有操作权限
        $info   =   D('Report')->getinfo($id);

        if ( !$info ) {
            oauthjson(13003,'未查询到数据');
            exit;
        }

        // 判断是否含有血糖检测项
        $checkgl    =   D('Report')->checkgl($info['data']);
        if ( !$checkgl || $checkgl['gl'] != 1 ) {
            oauthjson(13005,'非法操作');
            exit;
        }

        // 判断是否为当天
        $btime  =   strtotime(date('Y-m-d 00:00:00'));
        $etime  =   strtotime(date('Y-m-d 23:59:59'));
        if ( !( $info['extime'] >= $btime && $info['extime'] <= $etime ) ) {
            oauthjson(13005,'非法操作');
            exit;
        }

        // 判断检测数据是否可展示
        $this->checkdatainfo( $info['drugid'] ,$this->client_id );
       
        // 更新血糖信息
        $result     =   D('Report')->upAttr($checkgl['insertid'],$type);

        if ( !$result ) {
            oauthjson(13006,'更新失败');
            exit;
        }

        oauthjson(200,'更新成功');
        exit;
    }

}

