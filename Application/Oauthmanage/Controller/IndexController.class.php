<?php
namespace Oauthmanage\Controller;

use Oauthmanage\Controller\BaseController;


/**
 * 首页
 */
class IndexController extends BaseController{


    public function __construct(){
		parent::__construct();
	}

	/**
	 * 登录首页
     * 
	 */
    public function index(){
    	
 


        $this->display();
    }

    
}