<?php
namespace Oauthmanage\Controller;

use Oauthmanage\Controller\BaseController;

/**
 * 应用管理===访问数据统计
 * 
 * 
 */
class ChartController extends BaseController{


    public function __construct(){
		parent::__construct();
	}

	/**
	 * 应用管理访问数据统计
     * 
     * @author wangyangyang
     * @version V1.0
	 */
    public function index(){
    	
        $p  =   I('p',1,'intval');
        $p  =   max($p,1);
        $pagesize   =   10;

        $curpage    =   ( $p - 1 ) * $pagesize;

        $db     =   M('client','oauth_','DB_CONFIG2');
        // 获取总数
        $total  =   $db->count();

        $list   =   array ();
        if ( $total ) {
            //获取用户基本信息
            $list   =   $db->order('create_time desc ,id desc') ->limit($curpage,$pagesize)->select();
        }

        $pages  =   getpage($total,$pagesize);

        $this->assign ('list', $list);
        $this->assign ('pages', $pages);

        $this->display();
    }

    /**
     * 各接口访问数据统计
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function category(){
        $id =   I('get.id',0,'intval');
        if ( !$id ) {
            redirect('/Oauthmanage/chart');
            exit;
        }

        // 查询client信息
        $client =   M('client','oauth_','DB_CONFIG2')->where( array('id'=>$id))->find();

        if (!$client ) {
            redirect('/Oauthmanage/chart');
            exit;
        }

        $type   =   I('get.type','','intval');
        
        $this->assign('client',$client);
    
        
        $where  =   array();
        $where['client_id'] =   $client['client_id'];
        if ( !$type ) {
            $where['visitime']  =   array('between',array( date('Y-m-d 00:00:00'),date('Y-m-d 23:59:59')));
        }

        $data   =   $this->getdatas($client['status'],$where);

      
        $title  =   '各接口当天访问统计';
        if ( $type ) {
            $title  =   '各接口全部访问统计';
        }

        $this->assign('title',$title);
        $this->assign('data',$data);

        $this->display('category'.$client['status']);
    }


    /**
     * 总访问数据统计
     *     最近30条记录
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function visitimes(){
        $id =   I('get.id',0,'intval');
        if ( !$id ) {
            redirect('/Oauthmanage/chart');
            exit;
        }

        // 查询client信息
        $client =   M('client','oauth_','DB_CONFIG2')->where( array('id'=>$id))->find();

        if (!$client ) {
            redirect('/Oauthmanage/chart');
            exit;
        }

        $type   =   I('get.type','','intval');
        
        $this->assign('client',$client);
    
        
        $where  =   array();
        $where['client_id'] =   $client['client_id'];
        
        $info   =   M('usertimes','oauth_','DB_CONFIG2')->field('times,visittime')->where($where)->order('id DESC')->limit(15)->select();

        $data   =   '';

        if ( $info ) {
            foreach ($info as $key => $value) {
                $data   .=  '["'.$value['visittime'].'",'.$value["times"].'],';
            }
        }

        $data   =   $data ? rtrim($data,',') : '';
        
        $title  =   '每天总访问数据统计';
        
        
        $this->assign('title',$title);
        $this->assign('data',$data);

        $this->display();
    }




    /**
     * 各接口访问数据统计
     * 
     * @author wangyangyang
     * @version V1.0
     */
    private function getdatas( $status = 1 , $where = array() ){
        if ( $status == 1 ) {
            $where['m']    =   'Oauth';
        }elseif ( $status == 2 ) {
            $where['m']    =   'Oauthai';
        }elseif ( $status == 3 ) {
            $where['m']    =   'Passport';
        }
        
        $info   =   M('visitlog','oauth_','DB_CONFIG2')->field('c,count(1) AS num')->where($where)->group('c')->select();

        $info   =   $info ? handleArrayKey($info,'c') : array();
        
        $data   =   array();

        if ( $status == 1 ) {

            $data['Bf']     =   isset($info['Bf']) ? $info['Bf']['num'] : 0;
            $data['Bp']     =   isset($info['Bp']) ? $info['Bp']['num'] : 0;
            $data['Card']   =   isset($info['Card']) ? $info['Card']['num'] : 0;
            $data['El']     =   isset($info['El']) ? $info['El']['num'] : 0;
            $data['Gl']     =   isset($info['Gl']) ? $info['Gl']['num'] : 0;
            $data['Ox']     =   isset($info['Ox']) ? $info['Ox']['num'] : 0;
            $data['Ur']     =   isset($info['Ur']) ? $info['Ur']['num'] : 0;
            $data['We']     =   isset($info['We']) ? $info['We']['num'] : 0;

            $data['Process']=   isset($info['Process']) ? $info['Process']['num'] : 0;
            $data['Report'] =   isset($info['Report']) ? $info['Report']['num'] : 0;
            
            $data['Equipment'] =   isset($info['Equipment']) ? $info['Equipment']['num'] : 0;
            // $data['Examstate'] =   isset($info['Examstate']) ? $info['Examstate']['num'] : 0;
            
            return $data;

        }elseif ( $status == 2 ) {
            $data['Index']      =   isset($info['Index']) ? $info['Index']['num'] : 0;
            $data['Evaluation'] =   isset($info['Evaluation']) ? $info['Evaluation']['num'] : 0;
            $data['Improve']    =   isset($info['Improve']) ? $info['Improve']['num'] : 0;
            $data['Drugs']      =   isset($info['Drugs']) ? $info['Drugs']['num'] : 0;
            $data['Healthcare'] =   isset($info['Healthcare']) ? $info['Healthcare']['num'] : 0;
            $data['Nursing']    =   isset($info['Nursing']) ? $info['Nursing']['num'] : 0;
            $data['Consultation']   =   isset($info['Consultation']) ? $info['Consultation']['num'] : 0;
            return $data;
        }elseif ( $status == 3 ) {
            $data['Index']      =   isset($info['Index']) ? $info['Index']['num'] : 0;
            $data['Yc']         =   isset($info['Yc']) ? $info['Yc']['num'] : 0;
            return $data;
        }

        return false;
    }
}



