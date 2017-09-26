<?php

namespace Kbox\Controller;
use Think\Controller;

/**
 * 康宝系统设备数据展示页面
 * 
 * @author wangyangyang
 */
class DrugsController extends Controller{

	
	
 	public function __construct(){
		parent::__construct();

	}

	/**
	 * 查看设备提交过来的原始数据信息
	 * 
	 * @author wangyangyang
	 * @copyright V1.0
	 */
	public function index(){
		$id		=	I('get.id','','intval');
		
		// 标识检验码
		$sign	=	I('get.sign','','htmlspecialchars,trim');

		if ( !$id || !$sign) {
			echo "访问出错";
			exit;
		}

		$where 				=	array();
		$where['id']	=	$id;
		$info 	=	M('equipment')->where($where)->getField('gateUUID');

		if ( !$info ) {
			echo "访问出错";
			exit;
		}
		
		$check	=	md5(md5($info).'kboxhgt');
		
		if ($sign != $check ) {
			echo "访问出错";
			exit;
		}

		$result	=	array();
		$where	=	array();
		$where['gateuuid']	=	$info;

		$result	=	M('equipment_log')->where($where)->order('id desc')->limit(50)->select();


		$this->assign('id',$id);
		$this->assign('sign',$sign);
		$this->assign('result',$result);
		$this->display();
    }


    /**
     * 当前药店体检状态
     *
     * @author wangyangyang
     * @version V1.0
     */
    public function examstate(){
    	$id		=	I('get.id','','intval');
		
		// 标识检验码
		$sign	=	I('get.sign','','htmlspecialchars,trim');

		if ( !$id || !$sign) {
			echo "访问出错";
			exit;
		}

		$where			=	array();
		$where['id']	=	$id;
		$info 	=	M('equipment')->where($where)->getField('gateUUID');

		if ( !$info ) {
			echo "访问出错";
			exit;
		}
		
		$check	=	md5(md5($info).'kboxhgt');
		
		if ($sign != $check ) {
			echo "访问出错";
			exit;
		}

		$result	=	array();
		$where	=	array();
		$where['gateuuid']	=	$info;
		$result	=	M('examstate')->where($where)->order('id desc')->limit(50)->select();

		// 根据卡号查询用户信息
		$sql	=	array();
		if ( $result ) {
			foreach ($result as $key => $value) {
				$sql[]	=	$value['userid'];
			}
		}

		$userinfo	=	array();
		if ( $sql ) {
			$where 	=	array();
			$where['userid']	=	array('in',implode(',',$sql));
			$userinfo	=	M('member')->field('userid,username,nickname,mobile')->where($where)->select();
		}

		$tempuserinfo	=	array();
		if ( $userinfo ) {
			foreach ($userinfo as $key => $value) {
				$tempuserinfo[$value['userid']]	=	$value['nickname'] ? $value['nickname'] : $value['mobile'];
			}

			foreach ($result as $key => $value) {
				$result[$key]['nickname']	=	$tempuserinfo[$value['userid']];
			}
		}

		// 查询药店绑定的设备信息
		$where 	=	array();
		$where['gateuuid']	=	$info;
		$equipment	=	M('equipment')->where($where)->find();

		$this->assign('id',$id);
		$this->assign('sign',$sign);
		$this->assign('result',$result);
		$this->assign('equipment',$equipment);
		$this->display();
    }

    /**
     * 终止体检
     *
     * @author wangyangyang
     * @version V1.0
     */
    public function delexamstate(){

    	if(!IS_POST){
    		exit('99');
    	}
    	
    	$id		=	I('post.id','','intval');
		
		// 标识检验码
		$sign	=	I('post.sign','','htmlspecialchars,trim');

		if ( !$id || !$sign) {
			exit('101');
		}

		$where			=	array();
		$where['id']	=	$id;
		$info 	=	M('examstate')->where($where)->getField('gateuuid');

		if ( !$info ) {
			exit('102');
			exit;
		}
		
		$check	=	md5(md5($info).'kboxhgt');
		
		if ($sign != $check ) {
			exit('103');
			exit;
		}


		$result	=	array();
		$where	=	array();
		$where['gateuuid']	=	$info;
		$where['id']		=	$id;

		$result	=	M('examstate')->where($where)->delete();

		if (!$result) {
			exit('104');
		}
		exit('100');
    }



    /**
     * 当前药店体检状态
     *
     * @author wangyangyang
     * @version V1.0
     */
    public function examstateinfo(){
    	$id		=	I('get.id','','intval');
		
		// 标识检验码
		$sign	=	I('get.sign','','htmlspecialchars,trim');

		if ( !$id || !$sign) {
			echo "访问出错";
			exit;
		}

		$where			=	array();
		$where['userid']	=	$id;
		$info 	=	M('equipment')->where($where)->field('gateUUID')->select();

		if ( !$info ) {
			echo "访问出错";
			exit;
		}
		

		$check	=	md5(md5($id).'kboxhgt'.date('Y-m-d'));
		
		if ($sign != $check ) {
			echo "访问出错";
			exit;
		}
		$in 	=	'';
		foreach ($info as $key => $value) {
			$in .=	$value['gateuuid'].',';
		}

		$in 	=	rtrim($in,',');


		$result	=	array();
		$where	=	array();
		$where['gateuuid']	=	array('in',$in);
		$result	=	M('examstate')->where($where)->order('id desc')->limit(50)->select();

		// 根据卡号查询用户信息
		$sql	=	array();
		if ( $result ) {
			foreach ($result as $key => $value) {
				$sql[]	=	$value['userid'];
			}
		}

		$userinfo	=	array();
		if ( $sql ) {
			$where 	=	array();
			$where['userid']	=	array('in',implode(',',$sql));
			$userinfo	=	M('member')->field('userid,username,nickname,mobile')->where($where)->select();
		}

		$tempuserinfo	=	array();
		if ( $userinfo ) {
			foreach ($userinfo as $key => $value) {
				$tempuserinfo[$value['userid']]	=	$value['nickname'] ? $value['nickname'] : $value['mobile'];
			}

			foreach ($result as $key => $value) {
				$result[$key]['nickname']	=	$tempuserinfo[$value['userid']];
			}
		}

		// 查询药店绑定的设备信息
		$where 	=	array();
		$where['gateuuid']	=	$info;
		$equipment	=	M('equipment')->where($where)->find();

		$this->assign('id',$id);
		$this->assign('sign',$sign);
		$this->assign('result',$result);
		$this->assign('equipment',$equipment);
		$this->display();
    }

    /**
     * 终止体检
     *
     * @author wangyangyang
     * @version V1.0
     */
    public function delexamstateinfo(){
    	if(!IS_POST){
    		exit('99');
    	}
    	
    	$id		=	I('post.id','','intval');
		
		// 标识检验码
		$sign	=	I('post.sign','','htmlspecialchars,trim');

		if ( !$id || !$sign) {
			exit('101');
		}

		$where			=	array();
		$where['id']	=	$id;
		$info 	=	M('examstate')->where($where)->field('gateuuid,drugid')->find();

		if ( !$info ) {
			exit('102');
		}

		// 判断是否有权限删除
		$where			=	array();
		$where['gateUUID']	=	$info['gateuuid'];
		$where['userid']	=	$info['drugid'];
		$checkprev 	=	M('equipment')->where($where)->getField('userid');

		if ( !$checkprev ) {
			exit('105');
		}
		$check	=	md5(md5($checkprev).'kboxhgt'.date('Y-m-d'));
		if ($sign != $check ) {
			exit('103');
			exit;
		}

		$result	=	array();
		$where	=	array();
		$where['id']		=	$id;
		$result	=	M('examstate')->where($where)->delete();

		if (!$result) {
			exit('104');
		}
		exit('100');
    }
}