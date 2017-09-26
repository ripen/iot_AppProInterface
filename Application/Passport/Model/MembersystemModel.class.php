<?php
namespace Passport\Model;
use Think\Model;

/**
 * 用户池用户信息查询
 * 
 * @author wangyangyang
 * @version V1.0
 */
class MembersystemModel extends Model {  

	protected $autoCheckFields = false;



	/**
	 * 用户详情
	 * 
	 * 
	 * @param  string $username 用户名
	 * @return array
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function getuserinfo( $username ){
		$data	=	array();
		$data['status']	=	0;
		$data['message']=	'';

		// 缺少参数
		if ( !$username ) {
			$data['message']=	'缺少参数';
			return $data;
		}

		$map	=	array();
		$map['username']	=	$username;

        $info	=	M('member_system','kmdb_','DB_CONFIG_PASSPORT')
            ->where( $map )
            ->field('userid,username,addtime,regtime,regtype,systype,modelid,islock')
            ->find();

        // 未查询到用户信息
        if ( !$info ) {
        	$data['status']	=	1;
        	$data['message']=	'未查询到用户信息';
        	return $data;
        }

        // 账号被锁定
        if ( $info['islock'] == 1 ) {
        	$data['status']	=	2;
        	$data['message']=	'账号被锁定';
        	return $data;
        }

        // 判断用户ID状态
        $check 		=	M('member','kmdb_','DB_CONFIG_PASSPORT')->where( array('userid'=>$info['userid']))->find();

        // 未查询到用户信息
        if ( !$check ) {
        	$data['status']	=	1;
        	$data['message']=	'未查询到用户信息';
        	return $data;
        }

        // 账号被锁定
        if ( $check['islock'] == 1) {
        	$data['status']	=	2;
        	$data['message']=	'账号被锁定';
        	return $data;
        }

        $info['kuserid']	=	$check['kuserid'];
        $info['muserid']	=	$check['muserid'];

        //   `modelid` tinyint(1) DEFAULT '0' COMMENT '用户类型（根据用户类型区分不同的附属表） 0：普通用户 1：怡成专家 2：丰拓医生 3：丰拓院长 4：丰拓商务 5：丰拓健康管理师 6：丰拓站长 7：怡成CRM 8：怡成商业定制',
        $modeTab	=	array('0'=>'member_detail','1'=>'member_ycdoctor','2'=>'member_mndoctor','3'=>'member_mndean','4'=>'member_mnbusiness','5'=>'member_mnhealthmanager','6'=>'member_mnstation','7'=>'member_ycstore','8'=>'member_ycbstore');

        $moreinfo	=	array();

        // 查询附表信息
        if (isset($modeTab[$info['modelid']])) {
        	$moreinfo	=	M($modeTab[$info['modelid']],'kmdb_','DB_CONFIG_PASSPORT')->where( array('userid'=>$info['userid']) )->find();
        }

        
        $info	=	array_merge($info,$moreinfo);
        $info['password']	=	$check['password'];
        $info['encrypt']	=	$check['encrypt'];

        // 返回用户信息
        $data['status']	=	3;
        $data['message']=	'获取成功';
        $data['info']	=	$info ? $info : array();

		return $data;
	}



	/**
	 * 查询登录账号是否已经存在
	 * 
	 * 
	 * @param  string $username 登录用户名
	 * @return bool
	 */
	public function getmembersystem( $username ){
		if ( !$username ) {
			return false;
		}

		$info	=	M('member_system','kmdb_','DB_CONFIG_PASSPORT')->where( array('username'=>$username))->find();

		return $info ? true : false;
	}

	/**
	 * 判断账户ID是否已经注册在系统同
	 * 
	 * 
	 * @param  integer $kuserid 怡成网络医院用户ID
	 * @param  integer $muserid 丰拓用户ID
	 * @return 
	 */
	public function checkmember( $kuserid , $muserid ){
		$kuserid	=	$kuserid ? intval($kuserid) : 0;
		$muserid	=	$muserid ? intval($muserid) : 0;
		
		if (!$kuserid && !$muserid ) {
			return false;
		}

		// 只要有一个ID存在，就直接返回当前用户的ID
		$info 	=	array();
		if ( $kuserid ) {
			$info	=	M('member','kmdb_','DB_CONFIG_PASSPORT')->where( array('kuserid'=>$kuserid) )->find();
		}

		if ( $muserid && !$info ) {
			$info	=	M('member','kmdb_','DB_CONFIG_PASSPORT')->where( array('muserid'=>$muserid) )->find();
		}

		return $info ? $info['userid'] : false;
	}

	/**
	 * 注册用户信息到member表中
	 * 
	 * 
	 * @param array $data 
	 */
	public function add_member($data ){
		if ( !$data || !is_array($data)) {
			return false;
		}

		// 生成随机用户名
		$username 	=	$this->create_username();

		$info 				=	array();
		$info['username']	=	$username;
		$info['kuserid']	=	$data['kuserid'];
		$info['muserid']	=	$data['muserid'];
		$info['password']	=	$data['password'];
		$info['encrypt']	=	$data['encrypt'];
		$info['addtime']	=	$data['addtime'];
		$info['updatetime']	=	$data['updatetime'];
		$info['islock']		=	0;

		$db 	=	M('member','kmdb_','DB_CONFIG_PASSPORT');

		$userid	=	$db->add($info);
		return $userid ? $userid : false;
	}
	

	/**
	 * 注册用户信息到member_system 表中
	 * 
	 * 
	 * @param array $data 
	 */
	public function add_member_system($data ){
		if (!$data || !is_array($data) || !$data['userid'] ) {
			return false;
		}

		$info['userid']		=	$data['userid'];
		$info['username']	=	$data['username'];
		$info['addtime']	=	$data['addtime'];
		$info['regtime']	=	$data['regtime'];
		$info['regtype']	=	$data['regtype'];
		$info['systype']	=	$data['systype'];
		$info['acctype']	=	$data['acctype'];
		$info['modelid']	=	$data['modelid'];
		$info['islock']		=	0;

		$id	=	M('member_system','kmdb_','DB_CONFIG_PASSPORT')->add($info);

		return $id ? $id : false;
	}

	/**
	 * 根据用户类型，注册到不同的附属表中
	 * 
	 * 
	 * @param array $data 数据格式
	 * @param integer $types 判断是更新还是保存操作( 0:insert 1:更新 )
	 */
	public function save_member_detail( $data , $types = 0 ){
		if (!$data || !is_array($data) || !$data['userid'] ) {
			return false;
		}

		$moinfo=	array('0'=>'member_detail');

		$id    =	array();

		$tablename	=	'';

		switch ( $data['modelid'] ) {
			case 0:
				$tablename	=	'member_detail';
				break;
			case 1:
				$tablename	=	'member_ycdoctor';
				break;
		}

		if ( $types ) {
			M($tablename,'kmdb_','DB_CONFIG_PASSPORT')->where( array('userid'=>$data['userid']) )->save($data);
			$id    =    true;
		}else{
			$id    =    M($tablename,'kmdb_','DB_CONFIG_PASSPORT')->add($data);
		}

		return $id ? true : false;
	}

	/**
	 * 随机生成用户名
	 * @param  string  $pre 随机用户名前缀
	 * @param  integer $length 随机长度
	 * @return string  返回用户名
	 */
	public function create_username( $pre = 'kmdb_', $length = 12 ) {
	    $chars		=	'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
	    $username	=	'';
	    for ( $i = 0; $i < $length; $i++ ){
	        $username .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	    }

	    // 判断用户名是否已经注册，如有注册，重新生成
	    while ( true ) {
	    	$check 	=	M('member','kmdb_','DB_CONFIG_PASSPORT')->where(array('username'=>$username))->field('userid')->find();
	    	if ( !$check ) {
	    		$username 	=	$pre.$username;
	    		break;
	    	}else{
	    		generate_username( $pre , $length ); 
	    	}
	    }

	    return $username;
	}

	/**
	 * 通过用户名查询用户加密密码
	 * 
	 * 
	 * @param  int $userid 用户ID
	 * @param int $type 用户类型 0：用户池ID 1：网络医院用户ID ，2：丰拓用户ID
	 * @return array
	 */
	public function getuserbyid( $userid,$type=0 ){
		if ( !$userid ) {
			return false;
		}

		$pre 	=	C('DB_CONFIG_PASSPORT.db_prefix');

		switch ($type) {
			case 0:
				$info	=	M('member','kmdb_','DB_CONFIG_PASSPORT')->where( array('userid'=>$userid))->field('password,encrypt,userid,kuserid,muserid')->find();
				break;
			
			case 1:
				$info	=	M('','kmdb_','DB_CONFIG_PASSPORT')
		            ->table(array(
		                        $pre.'member'	=>	'm',
		                        $pre.'member_detail'	=>	'md'
		                    )
		              )
		            ->where('m.userid = md.userid and m.kuserid = "'.$userid .'"' )
		            ->field('m.password,m.encrypt,m.userid,m.kuserid,m.muserid')
		            ->find();
				break;
			case 2:
				$info	=	M('','kmdb_','DB_CONFIG_PASSPORT')
		            ->table(array(
		                        $pre.'member'	=>	'm',
		                        $pre.'member_detail'	=>	'md'
		                    )
		              )
		            ->where('m.userid = md.userid and m.muserid = "'.$userid .'"' )
		            ->field('m.password,m.encrypt,m.userid,m.kuserid,m.muserid')
		            ->find();
				break;
		}

		return $info ? $info : array();
	}

	/**
	 * 更新密码
	 * 
	 * @param  integer $userid 用户ID
	 * @param  string $pass   新密码
	 * @param  string  $encrypt 密码加密串
	 * @return 
	 */
	public function updatepass( $userid , $pass , $encrypt ){
		if (!$userid || !$pass ) {
			return false;
		}

		$data    =    array();
		$data['password']    =    $pass;
		$data['encrypt']     =    $encrypt;
		$data['updatetime']  =    time();
		$info    =    M('member','kmdb_','DB_CONFIG_PASSPORT')->where( array('userid'=>$userid) )->save($data);

		return $info ? true : false;
	}

	/**
	 * 更新 muserid / kuserid
	 * 
	 * @param  integer $userid 用户ID
	 * @param  array $data   更新信息
	 * @return 
	 */
	public function updatekmuserid( $userid , $data ){
		if (! $userid || !$data || !is_array($data) ) {
			return false;
		}

		$info    =    M('member','kmdb_','DB_CONFIG_PASSPORT')->where( array('userid'=>$userid) )->save($data);

		return $info ? true : false;
	}
}


