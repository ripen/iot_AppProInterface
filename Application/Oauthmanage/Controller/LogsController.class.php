<?php
namespace Oauthmanage\Controller;

use Oauthmanage\Controller\BaseController;
/**
 * 访问日志查看
 */
class LogsController extends BaseController{


    public function __construct(){
		parent::__construct();
	}

	/**
	 * 访问日志查看
     * 
     * @author wangyangyang
     * @version V1.0
	 */
    public function index(){
    	$p  =   I('p',1,'intval');
        $p  =   max($p,1);
        $pagesize   =   15;

        $curpage    =   ( $p - 1 ) * $pagesize;

        $db     =   M('visitlog','oauth_','DB_CONFIG2');
        // 获取总数
        $total  =   $db->count();

        $info   =   array ();
        $info   =   $db->order('id desc') ->limit($curpage,$pagesize)->select();

        $pages  =   getpage($total,$pagesize);

        $this->assign ('info', $info);
        $this->assign ('pages', $pages);

        $this->display();
    }

    
}