<?php
namespace Oauthmanage\Controller;
use Think\Controller;

class PublicController extends Controller {


	/**
     * 登录页面
     * 
     */
    public function login(){

        // 判断是否已经登录
        $_userid    =   cookie('_userid');
        if ( $_userid ) {
            redirect('/Oauthmanage');
            exit;
        }

        $this->display();
    }

    /**
    * 
    * @Description:  checkUser
    */
    public function checkUser(){
        
        if ( !IS_POST ) {
            exit('201');
        }
        
        $username = I ( 'username', '', 'trim,htmlspecialchars,strip_tags' );
        $password = I ( 'password', '', 'trim,htmlspecialchars,strip_tags' );
        $remember = I ( 'remember', 0, 'intval' );
        
        if (!$username || !$password ) {
            exit('201');
        }

        $info   =  M('admin','oauth_','DB_CONFIG2')->where( array('username' => $username) )->find();
        
        if ( !$info ) {
            exit('201');
        }

        $cpass  =   md5(md5($password).$info['encrypt']);
        

        if ( $cpass != $info['password'] ) {
            exit('202');
        }

        if ($remember) {
            $time = 10 * 86400;
        } else {
            $time = 0;
        }

        $userid     =   $info['userid'];
        $username   =   $info['realname'];
        
        cookie('_userid',$userid,$time);
        cookie('_username',$username,$time);
       
        
        exit('200');
    }


    /**
     * 
     * @Description:  logout
     */
    public function logout(){
        cookie('_userid',null);
        cookie('_username',null);
        redirect('/Oauthmanage/Public/login');
    }

}