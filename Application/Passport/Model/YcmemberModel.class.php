<?php
namespace Passport\Model;
use Think\Model;

/**
 * 网络医院用户信息查询
 * 
 * @author wangyangyang
 * @version V1.0
 */
class YcmemberModel extends Model {  

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
		// 判断手机号、固话字段，然后判断卡号
		$map['username|mobile|phone']	=	$username;

        $info	=	M('member')
            ->where( $map )
            ->field('userid,username,nickname,modelid,phone,mobile,regdate,password,encrypt')
            ->find();

        $userid 	=	'';

        $cardinfo	=	array();
        if ( !$info ) {
        	$cardinfo	=	M('drug_card_user')->where( array('wholecard'=>$username))->field('userid')->find();
        }

        if ( $cardinfo ) {
        	$info 	=	M('member')->where( array('userid'=>$cardinfo['userid']) )->field('userid,username,nickname,modelid')->find();
        }

        if ( !$info ) {
        	return false;
        }

        $detail 	=	array();

        // 根据用户所属类型，查询不同的附属表信息
        switch ( $info['modelid'] ) {
        	case '10':
        		$detail	=	M('member_detail')->where( array('userid'=>$info['userid']))->field('birthday,sex,height,bsugar,bsugartime')->find();
        		$detail =	$detail ? $detail : array('birthday'=>'','sex'=>'','height'=>'','bsugar'=>'','bsugartime'=>'');
        		break;
        	case '28':
        		$detail	=	M('member_kdoctor')->where( array('userid'=>$info['userid']))->field('sex,hospital,subject,job,goodat,content,work')->find();
        		$detail =	$detail ? $detail : array('sex'=>'','hospital'=>'','subject'=>'','job'=>'','goodat'=>'','content'=>'','work'=>'');
        		break;
        	case '35':
        		$detail	=	M('member_store')->where( array('userid'=>$info['userid']))->field('introduction,mechanism,contactsname')->find();
        		$detail =	$detail ? $detail : array('introduction'=>'','mechanism'=>'','contactsname'=>'');
        		break;
        	case '41':
        		$detail	=	M('member_bstore')->where( array('userid'=>$info['userid']))->field('introduction,mechanism,contactsname')->find();

        		$detail =	$detail ? $detail : array('introduction'=>'','mechanism'=>'','contactsname'=>'');

        		break;
        }

        // unset($info['modelid']);
        $info 	=	array_merge($info,$detail);

       	return $info ? $info : false;
	}

	/**
	 * 通过用户名查询用户加密密码
	 * 
	 * 
	 * @param  int $userid 用户ID
	 * @return array
	 */
	public function getuserbyid( $userid ){
		if ( !$userid ) {
			return false;
		}

		$info	=	M('member')->where('userid = "'.$userid .'"' )->field('password,encrypt,userid')->find();

		return $info ? $info : array();
	}

	/**
	 * 查询登录账号是否已经存在
	 * 
	 * 
	 * @param  string $username 登录用户名
	 * @return bool
	 */
	public function getmember( $username ){
		if ( !$username ) {
			return false;
		}

		$map	=	array();
		// 判断手机号、固话字段，然后判断卡号
		$map['username|mobile|phone']	=	$username;

        $info	=	M('member')
            ->where( $map )
            ->field('userid,username,nickname,modelid')
            ->find();

        $userid 	=	'';

        $cardinfo	=	array();
        if ( !$info ) {
        	$cardinfo	=	M('drug_card_user')->where( array('wholecard'=>$username))->field('userid')->find();
        }

        if ( $cardinfo ) {
        	$info 	=	M('member')->where( array('userid'=>$cardinfo['userid']) )->field('userid,username,nickname,modelid')->find();
        }

        return $info ? $info : false;
	}
	

	/**
	 * 注册用户
	 * 
	 * @param array $data 数据格式
	 * @param array $detail 附表信息
	 */
	public function save_member( $data , $detail ){
		if ( !$data || !is_array($data) ) {
			return false;
		}

		$userinfo 	=	array();

    	$encrypt   	= 	random ( 6 );
    	$password  	= 	password ( '888888' , $encrypt );
        $username   =   generate_username('kd_');

    	// 注册流程，先往sso表添加数据
    	$sso_members = array (
    			'username' 	=> $username,
    			'password' 	=> $password,
    			'random' 	=> $encrypt,
    			'regdate' 	=> time (),
    			'regip' 	=> $_SERVER["REMOTE_ADDR"]
    	);
    	$phpssouid = M( 'sso_members' )->add($sso_members);

    	//写入member表
    	$user = array (
    			'username'  => $username,
    			'nickname'  => $data['nickname'],
    			'password'  => $password,
    			'encrypt'   => $encrypt,
    			'phpssouid' => $phpssouid,
    			'regdate'   => time (),
    			'regip'     => $_SERVER["REMOTE_ADDR"],
    			'modelid'   => 10,
    			'groupid'   => '7'
    	);

    	if ( checkphone($data['username']) ) {
    		$user['mobile'] = $data['username'];
    	}else{
    		$user['phone']  = $data['username'];
    	}

    	//插入member扩展表
    	$userid 	=	M('member')->add($user);

    	if ( !$userid ) {
    		return false;
    	}

		$userother['userid']	=	$userid;
		$userother['sex']		=	$detail['sex'];
		$userother['birthday']	=	$detail['birthday'];
		$userother['height']	=	$detail['height'];
		$userother['bsugar']	=	$detail['bsugartype'];
		$userother['bsugartime']=	$detail['bsugartime'];


		$result = M('member_detail')->add($userother);
    	
		return $userid;

	}

	/**
	 * 更新主表信息
	 * 
	 * 
	 * @param array $data 数据格式
	 * @param integer $userid 判断是更新还是保存操作( 0:insert 大于0更新 )
	 */
	public function save_member_sys( $data , $userid = 0 ){
		if ( !$data || !is_array($data) ) {
			return false;
		}

		$id    =	array();
		
		if ( $userid ) {
			M('member')->where( array('userid'=>$userid) )->save($data);
			$id    =    true;
		}

		return $id ? true : false;
	}

	/**
	 * 更新附表信息
	 * 
	 * 
	 * @param array $data 数据格式
	 * @param integer $userid 判断是更新还是保存操作( 0:insert 大于0更新 )
	 */
	public function save_member_detail( $data , $userid = 0 ){
		if (!$data || !is_array($data) ) {
			return false;
		}

		$id    =	array();

		if ( $userid ) {
			M('member_detail')->where( array('userid'=>$userid) )->save($data);
			$id    =    true;
		}else{
			$id    =    M('member_detail')->add($data);
		}

		return $id ? true : false;
	}
}


