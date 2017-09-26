<?php
namespace Oauthai\Controller;
use Oauthai\Controller\BaseController;


/**
 * 健康评测
 * 
 * @author wangyangyang
 * @version V1.0
 */
class EvaluationController extends BaseController {  

    public function __construct(){
    	parent::__construct();
    }


    /**
     * 健康评测表单
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        // 疾病ID
        $id =   I('post.id','','intval');

        if ( !$id ) {
            $this->noparam();
            exit;
        }

        $info   =   D('Evaluation')->show( $id ) ;


        if ( !$info ) {
            oauthjson(30001,'暂无信息');
            exit;
        }

        $dinfo      =   D('Diseases')->show($id);
        if ( !$dinfo ) {
            oauthjson(30001,'暂无信息');
            exit;
        }

        $result     =   array();
        $keys       =   0;

        // 数组格式处理
        foreach ($info as $key => $value) {
            $tempdata   =   $value['data'] ? json_decode($value['data'],true) : '';

            if ( !$tempdata || !is_array($tempdata) ) {
                continue;
            }

            $result[$keys]  =   $value;
            unset($result[$keys]['data'],$result[$keys]['did'],$result[$keys]['score'],$result[$keys]['addtime']);

            foreach ($tempdata as $skey => $v) {
                $result[$keys]['data'][]    =   array('key'=>$skey,'title'=>$v['checktitle']);
            }

            $keys ++ ;
        }

        $data   =   array();
        $data['info']   =   $dinfo ? $dinfo : array();
        $data['lists']  =   $result ? $result : array();

        oauthjson(200,'获取成功',$data);
        exit;
    }


    /**
     * 健康评测表单提交获取分析结果
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function show(){
        // 疾病ID
        $id =   I('post.id','','intval');

        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 手机号
        $mobile =   I('post.mobile','','htmlspecialchars,trim');
        if ( $mobile && !checkphone($mobile) ) {
            $this->noparam();
            exit;
        }

        // 问卷选项
        $info   =   I('post.data');
        if ( !$info || !is_array($info) ) {
            $this->noparam();
            exit;
        }

        // 关联关系
        $connectid  =   I('post.connectid' ,'','htmlspecialchars,trim');
        $connectid  =   is_numeric($connectid) ? $connectid : '';


        $result =   array();
        $results=   array();
        // 异常项
        $danger =   array();

        // 表单项
        $finfo  =   D('Evaluation')->show( $id ) ;
        $finfo  =   handleArrayKey($finfo,'id');

        // 判断表单项是否都已经填写
        $nums   =   count(array_filter($info));

        $fnums  =   count($finfo);

        if ( $nums != $fnums ) {
            oauthjson(30003,'表单项不完整');
            exit;
        }

        // 得分
        $score  =   0;
        
        foreach ($info as $key => $value) {
            if ( !$value ) {
                continue;
            }

            $result[$key]   =   $finfo[$key]['title'];

            $tmpvalue       =   explode(',',$value);
           
            if ( is_array($tmpvalue) ) {
                foreach ($tmpvalue as $k => $v) {
                    if ( $v < 0 || !is_numeric($v)) {
                        continue;
                    }

                    $temp   =   $finfo[$key]['data'] ? json_decode($finfo[$key]['data'],true) : array();

                    // 项目所选结果
                    if ( is_array($temp) && isset($temp[$v]) ) {
                        $results[$key][$v]['checktitle']  = $temp[$v]['checktitle'];  //    选项内容
                        $results[$key][$v]['checkstate']  = $temp[$v]['checkstate'];  //    选项是否异常
                        $results[$key][$v]['checkdis']    = $temp[$v]['checkdis'];    //    异常疾病
                        $results[$key][$v]['checksugest'] = $temp[$v]['checksugest']; //    改善建议
                        $results[$key][$v]['checkscore']  = $temp[$v]['checkscore'];  //    得分
                    
                        $score  +=  $temp[$v]['checkscore'];
                    }
                    
                    // 异常项
                    if ( is_array($temp) && isset($temp[$v]['checkstate']) && $temp[$v]['checkstate'] == 2 ) {
                        $danger[$key][] =   $temp[$v]['checkdis'];
                        $danger[$key][] =   $temp[$v]['checksugest'];
                    }
                }
            }
        }

        // 得分分析
        $map        =   array();
        $map['mins']=   array('elt',$score);
        $map['maxs']=   array('egt',$score);
        $map['did'] =   $id;
        $scinfo     =   D('Evaluation')->score($map);

        // 判断是否含有异常项,如果没有异常项，获取整体参考建议
        $rinfo      =   array();
        if ( !$danger ) {
            $rinfo  =   D('Evaluation')->reference($id);
        }

        // 计算打败多少人
        $beat           =   intval($score * C('BEATSCORE'));

        $data           =   array();
        $data['mobile'] =   $mobile;
        $data['did']    =   $id;
        $data['score']  =   $score;
        $data['beat']   =   $beat;
        $data['addtime']=   time();
        $data['danger'] =   $danger ? json_encode($danger) : '';
        $data['result'] =   $result ? json_encode($result) : '';
        $data['results']=   $results ? json_encode($results) : '';
        $data['suggest']=   $scinfo ? $scinfo['data'] : '';
        $data['state']  =   $scinfo ? $scinfo['state'] : '2';
        $data['reference']  =   $rinfo ? $rinfo['data'] : '';
       
        // 记录来源信息
        $data['from']       =   'oauthai';
        $data['fromid']     =   $this->client_info['id'];

        $data['connectid']  =   $connectid ? $connectid : '';

        $resultsid          =   D('Evaluation')->addresults($data);

        if ( !$resultsid ) {
            oauthjson(30002,'分析失败');
            exit;
        }

        $dangers    =   array();
        //  结果展示包含：疾病信息，分值，状态，总体参考建议，危险因素
        if ( is_array($danger) && $danger ) {
            $nums   =   0;
            foreach ($danger as $key => $value) {
                $dangers[$nums]['title'] =   $value['0'];
                $dangers[$nums]['result']=   $value['1'];

                $nums ++;
            }
        }

        $disinfo    =   D('Diseases')->show($id);
        
        $api_data   =   array();
        $api_data['id']         =   $id;                                //  疾病ID
        $api_data['title']      =   $disinfo ? $disinfo['title'] : '';  //  疾病名称
        $api_data['score']      =   $score;                             //  分值
        $api_data['state']      =   $data['state'];                     //  状态
        $api_data['suggest']    =   $data['suggest'] ;                  //  整体参考建议
        $api_data['danger']     =   $dangers ? $dangers : array();      //  异常项目
        $api_data['beat']       =   $beat;                              //  击败多少用户
        oauthjson(200,'获取成功',$api_data);
        exit;
    }

    /**
     * 健康评测列表
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function infolists(){
        // 关联ID
        $id =   I('post.id','','intval');

        if ( !$id ) {
            $this->noparam();
            exit;
        }

        $p          =   I('post.p',1,'intval');
        $pagesize   =   I('post.pagesize',20,'intval');
        $p          =   max(1,$p);
        $pagesize   =   $pagesize && $pagesize > 20 ? 20 : $pagesize;

        $info   =   D('Evaluation')->infolists( $id , $p , $pagesize , $this->client_info['id']) ;


        
        if ( !$info ) {
            oauthjson(30004,'暂无信息');
            exit;
        }

        oauthjson(200,'获取成功',$info);
        exit;
    }

    /**
     * 健康评测列表详情
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function infoshow(){
        // 关联ID
        $id =   I('post.id','','intval');

        $listid     =   I('post.listid','','intval');

        if ( !$id || !$listid ) {
            $this->noparam();
            exit;
        }

        $info   =   D('Evaluation')->infoshow( $id , $listid , $this->client_info['id']) ;

        if ( !$info ) {
            oauthjson(30005,'暂无详情');
            exit;
        }

        // 处理数据格式
        $temp_danger    =   $info['danger'] ? json_decode($info['danger'],true) : array();

        if ( $temp_danger ) {
            sort($temp_danger);

            foreach ($temp_danger as $key => $value) {
                $temp_danger[$key]['title']     =   $value[0];
                $temp_danger[$key]['result']    =   $value[1];
                unset($temp_danger[$key][0],$temp_danger[$key][1]);
            }
        }


        $api_data   =   array();
        $api_data['id']         =   $listid;                        //  列表ID
        $api_data['disid']      =   $info['did'];                   //  疾病ID
        $api_data['title']      =   $info['title'];                 //  疾病名称
        $api_data['score']      =   $info['score'];                 //  分值
        $api_data['state']      =   $info['state'];                 //  状态
        $api_data['suggest']    =   $info['suggest'] ;              //  整体参考建议
        $api_data['danger']     =   $temp_danger ? $temp_danger : array();  //  异常项目
        $api_data['beat']       =   $info['beat'];                  //  击败多少用户
        oauthjson(200,'获取成功',$api_data);
        exit;
    }
}

