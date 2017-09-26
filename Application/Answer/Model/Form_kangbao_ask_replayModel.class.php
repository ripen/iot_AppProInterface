<?php
namespace Answer\Model;
use Think\Model;
/**
 * 问答详情显示页
 * @author tangchengqi
 *
 */
class Form_kangbao_ask_replayModel extends Model {
	
	public   function __construct( ){
		
	}
	/**
	 *获取表名
	 *
	 */
	public  function tablename(){
		return 'form_kangbao_ask_replay';
	}
	/**
	 * 
	 * @param number $id 提问id */
	public function getshow($id=0){
		$list	=	$data 	=	array();
		$str 	=	'';

		$list 	= 	M(self::tablename())
				->where('pid=0 AND doctorid>0 AND contentid='.$id.'')
				->order('datetime  desc')
				->select();

		foreach( $list as $k => $v ){
			$list[$k]['datatime'] 	= date('Y-m-d H:i:s',$v['datetime']);
			//用户及医生的追问消息
			$list[$k]['replay'] = self::getuserreplay($v['contentid'],$v['dataid']);
		}

		return $list;
	}
	/**
	 * 用户对内容的回复信息
	 * 
	 * @param number $contentid  */
	public function getuserreplay($contentid=0, $replayid=0){
		$list = array();
		$list = M(self::tablename())
		->where('userid>0 AND contentid='.$contentid.' AND replayid='.$replayid.'')
		->order('datetime  desc')
		->select();
		foreach($list as $k=>$v){
			$list[$k]['doctorreplay'] = self::getdoctorreplay($v['dataid']);	
		}
		return $list;
	}


	/**
	 * 医生对用户追问信息的回复
	 * @param unknown $pid 对用户消息回复的对应关系 */
	public function getdoctorreplay($pid){
		$replay = array();
		$replay = M(self::tablename())
		->where("pid=$pid")
		->find();
		return $replay;
	}
	
	
}	
	