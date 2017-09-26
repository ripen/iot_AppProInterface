<?php

/**
 * @author Administrator
 *	用户信息管理
 */
namespace Mobileastronautic\Model;
use Think\Model;
use Mobileastronautic\Model\BaseModel;
class MemberModel extends Model {
	//表名
	protected  $TableName = 'member';
	
	public  function __construct(){
		parent::__construct();
	}
	
	/**
	 *获取用户信息 
	 * @param $userid 用户id 
	 */
	public function userinfo($userid=''){
		if(!$userid){
			return '';
		}
		$data = array();
		$Model = new Model();
		$data = $Model->table('pf_member as m,pf_member_detail as d')->where("m.userid=d.userid AND m.userid={$userid}")->find();
		return $data;
	}
	/**
	 * 编辑用户信息
	 * @param $data 用户信息
	 * @param $userid 用户id
	 */
	public function edit($data = array(), $userid=''){	
		if(!$userid){
			return '';
		}
		if(!$data){
			return '';
		}
		$username = array();
		$username['username'] = array_shift($data);
		M($this->TableName)->where("userid={$userid}")->data($username)->save();
		return M('member_detail')->where("userid={$userid}")->data($data)->save();	
	}
	
}