<?php 
namespace Apiyc\Model;
use Think\Model;
class Member_Model extends Model{
	
	/**
	 * 添加会员
	 * 
	 * @param array $data [description]
	 */
	public function add( $data = array() ){
		if ( !isset($data['username']) || !$data['username'] ) {
			return false;
		}
		if ( !isset($data['password']) || !$data['password'] ) {
			return false;
		}


		$encrypt = random ( 6 );
		$password = password ( $data ['password'], $encrypt );
		// 注册流程，先往sso表添加数据
		$sso_members = array (
				'username' => $data ['username'],
				'password' => $password,
				'random' => $encrypt,
				'regdate' => time (),
				'regip' => get_client_ip () 
		);
		$phpssouid = M ( 'sso_members' )->add ( $sso_members );
		if (!$phpssouid) {
			return false;
		}

		$user = array (
				'username' 	=> 	$data ['username'],
				'password' 	=> 	$password,
				'encrypt' 	=> 	$encrypt,
				'phpssouid' => 	$phpssouid,
				'regdate' 	=> 	time (),
				'regip' 	=> 	get_client_ip (),
				'mobile' 	=> 	'',
				'modelid'	=>	10,
				'groupid' 	=> 	'7'
		);
		$uid = M ( 'member' )->add ( $user );

		if ( !$uid ) {
			return false;
		}
		
		// 用户细节插入member_detail表
		$userdetail = array (
				'userid' 	=> $uid ,
				'birthday'	=>	$data['birthday'],
				'sex'		=>	$data['sex'],
				'height'	=>	$data['height']

		);
		M ( "member_detail" )->add ( $userdetail );

		return $uid;
	}

}