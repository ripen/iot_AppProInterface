<?php
namespace Kmv\Model;
use Think\Model;
class MemberModel extends Model {
	
	//页数
	private static  $pagesize = 10;
	private static $headimg ="./Uploads/face/";
	public   function __construct($classname=__CLASS__){
		return self::$classname;
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'member';
	}
	/**
	 * 更新用户信息
	 * @param number $userid 用户id
	 * @param unknown $data 用户数据 */
	public function update($userid=0, $data=array()){
		return M(self::tablename())->where('userid="'.$userid.'"')->save($data);
	}
	/**
	 * 获取用户信息
	 * @param number $userid  */
	public function getuserinfo($userid=0){
		return M(self::tablename())->where('userid="'.$userid.'"')->find();
	}
	
	
	/**
	 *获取用户信息关联用户详情表
	 * @param $userid 用户id
	 */
	public function userinfo($userid=''){
		if(!$userid){
			return '';
		}
		$pre = C('DB_PREFIX');
		$data = array();
		$data = M(self::tablename())
				  ->join($pre.'member_detail ON '.$pre.self::tablename().'.userid='.$pre.'member_detail.userid ')
				  ->where($pre.self::tablename().'.userid='.$userid.'')
				  ->find();
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
		M(self::tablename())->where("userid={$userid}")->data($username)->save();
		return M('member_detail')->where("userid={$userid}")->data($data)->save();
	}
	/**
	 * 获取当前值班医生信息
	 * @param number $week 星期几 
	 * @param number $doctorid 医生id 
	 * */
	public function getdoctorinfo($week =0){
		$data = array();
		$pre = C('DB_PREFIX');
		$data = M(self::tablename())
		->join($pre.'member_kdoctor as mk ON '.$pre.self::tablename().'.userid=mk.userid ')
		->field('mk.userid,nickname,username,sex,hospital,subject,job,goodat,content,work')
		->where('mk.work='.$week.'')
		->select();
		foreach($data as $k=>$v){
			//  获取医生头像信息
			$data[$k]['avatar'] = MemberModel::get_userimg($v['userid'],'big');
			$data[$k]['sex'] = $v['sex'] ? '男':'女';
		}
		return $data;
	}
	/**
	 * 根据医生id 获取医生信息
	 * @param number $doctorid
	 * @return $data */
	public function getdocotr($doctorid=0){
		$data = array();
		$pre = C('DB_PREFIX');
		$data = M(self::tablename())
		->join($pre.'member_kdoctor as mk ON '.$pre.self::tablename().'.userid=mk.userid ')
		->field('mk.userid,nickname,username,sex,hospital,subject,job,goodat,content,work')
		->where('mk.userid='.$doctorid.'')
		->find();
			//  获取医生头像信息
		$data['avatar'] = MemberModel::get_userimg($v['userid'],'big');
		$data['sex'] = $data['sex'] ? '男':'女';
		return $data;
	}
	
	
	/**
	 * 获取用户头像
	 * @param unknown $uid 用户id
	 * @return string  */
	public function get_userimg($uid,$size='middel') {
		$imgext = array('.gif','.png','.jpg','.jpge');
		$size = in_array($size, array('big', 'middle', 'small','user')) ? $size : 'middle';
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		for($i=0;$i<count($imgext);$i++){
			$userheadimg = self::$headimg.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'/'.$size.$imgext[$i];
			if(file_exists($userheadimg)){
				return $userheadimg;
				break;
			}
		}
		return '/Public/img/doc_01.jpg';
	}
	
	
	
	/**
	 * 根据手机号获取一条用户信息
	 * @param unknown $mobile 用户手机号 */
	public function getonemember($mobile){
		$info = array();
		$info = M('member')
		->field('password,encrypt,userid,mobile,username,groupid')
		->where('mobile="'.$mobile.'"')
		->select();
		return $info ? $info['0'] :'';
	}
	
	/**
	 * 计算用户年龄  
	 * @param string $birthday 生日*/
	public function userage($birthday='1911-1-1'){
		if(!$birthday){
			return '';
		}
		$year = date('Y');
		$old  = substr($birthday,0,stripos($birthday,'-'));
		$age = $year-$old;
		if($age<0 || $age>150)
		{
			return '';
		}	
		return $age+1;
	}
	
	
	
	
	/**
	 * 判定用户密码是否正确
	 * @param number $userid 用户id
	 * @param unknown $pwd 用户密码 */
	public function checkpwd($userid=0,$pwd){
		$info = M('member')->where('userid='.$userid.'')
				->getField('password,encrypt');
		if($info['password']==password($pwd,$info['encrypt'])){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 检测手机号是否存在
	 * @param unknown $mobile 手机号
	 * @return \Think\mixed  */
	public function checkmobile($mobile){
		$condition['mobile'] = $mobile;
		return M(self::tablename())->where($condition)->find();
	}
}	