<?php
namespace Oauth\Model;
use Think\Model;

/**
 * 用户体系相关操作
 * 
 * @author wangyangyang
 * @version V1.0
 */
class MemberModel extends Model {  

	Protected $autoCheckFields = false;


	/**
	 * 通过卡号进行虚拟用户注册
	 * @param integer $sex      性别（ 0：男 1：女）
	 * @param integer $height   身高（单位：cm）
	 * @param date $birthday 出生日期（ Y-m-d ）
	 */
	public function add_bycard( $sex , $height , $birthday ){
		$encrypt 	= 	random ( 6 );
        $password 	= 	password ( 'yc000000', $encrypt );
        $username   =   generate_username();
        
        // 注册流程，先往sso表添加数据
        $sso_members = array (
                'username' 	=> $username,
                'password' 	=> $password,
                'random' 	=> $encrypt,
                'regdate' 	=> time (),
                'regip' 	=> get_client_ip () 
        );
        $phpssouid 	= 	M ( 'sso_members' )->add ( $sso_members );
        
        if ( !$phpssouid ) {
        	return false;
        }

        $user = array (
                'username' 	=> 	$username,
                'password' 	=> 	$password,
                'encrypt' 	=> 	$encrypt,
                'phpssouid' => 	$phpssouid,
                'regdate' 	=> 	time (),
                'regip' 	=> 	get_client_ip (),
                'mobile' 	=> 	'',
                'modelid'	=>	10,
                'groupid' 	=> 	'7' 
        );
        $userid 	=	M ( 'member' )->add ( $user );
        if ( !$userid ) {
        	return false;
        }
        
        // 用户细节插入member_detail表
        $userdetail = array (
                'userid' 	=> 	$userid,
                'sex'		=> 	$sex,
                'height'	=>	$height,
                'birthday'	=>	$birthday
        );
        M ( "member_detail" )->add ( $userdetail );
        
        return $userid;
    }

    /**
     * 更新用户基本信息
     * 
     * @param  array   $data   更新数据
     * @param  integer $userid 用户id
     */
    public function updates( $data = array() , $userid = 0 ){
        if (!$data || !$userid ) {
            return false;
        }

        $result     =   M('member_detail')->where( array('userid'=>$userid) )->save($data);
        
        return $result ? true : false;
    }

}







