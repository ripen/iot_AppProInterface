<?php
namespace Oauthai\Model;
use Think\Model;

/**
 * 健康评测
 * 
 * @author wangyangyang
 * @version V1.0
 */
class EvaluationModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 健康测评表单详情
	 * @param  integer $id 疾病ID
	 * @return array
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function show( $id = 0 ){
		if ( !$id ) {
			return false;
		}

		$info 	=	array();

		$phpredis 	= 	new \Common\Common\phpredis();
    	$rkey		=	$phpredis->createkeyname('ai_evaluation',$id);

    	// 查询自我评测表单信息
    	if ( $id ) {
    		$info 	=	$phpredis->formatdataget($rkey);
    	}

    	if ( $info ) {
    		return $info;
    	}

		$info 	=	M('evaluation_contents','pf_','DB_CONFIG_AI')->where( array('did'=>$id) )->order('listorder ASC,id ASC')->select();

		if ( $info ) {
			$phpredis->formatdataset($rkey,$info,86400);
			return $info;
		}
		
    	return false;
	}


	/**
	 * 计算得分
	 * 
	 * @param  array  $map [description]
	 * @return [type]      [description]
	 */
	public function score( $map = array() ){
		if (!is_array($map) || !$map ) {
			return false;
		}

		$info 	=	M('evaluation_score','pf_','DB_CONFIG_AI')->where($map)->find();
		return $info ? $info : false;

	}


	/**
	 * 整体分析建议
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function reference( $id = 0 ){
		if ( !$id ) {
			return false;
		}
		$info 	=	M('evaluation_reference','pf_','DB_CONFIG_AI')->where( array('did'=>$id))->find();
		return $info ? $info : false;
	}

	/**
	 * 保存分析结果
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function addresults($data = array() ){
		if (!$data || !is_array($data) ) {
			return false;
		}

		$id 	=	M('evaluation_result','pf_','DB_CONFIG_AI')->add($data);
		return $id ? $id : false;
	}

	/**
	 * 获取关联用户评测列表信息
	 * @param  integer $id        关联ID
	 * @param  integer $p         当前页
	 * @param  integer $pagesize  每页条数
	 * @param  integer $client_id 应用ID
	 * @param  integer $types 区分读取疾病类型 1：读取糖尿病并发症类型
	 * @return array
	 */
	public function infolists( $id = 0 , $p = 1 , $pagesize = 20 , $client_id = 0 , $types = 1 ){
		if ( !$id || !$client_id ) {
			return false;
		}

		$dis_id	=	'';
		$data	=	array();
		if ( $types ) {
			$data = M("type_dis",'pf_','DB_CONFIG_AI')->where(" type_id = 1 ")->field('dis_id')->select();
		}

		$where 	=	'';
		if ( $data ) {
			$dis_id	=	extractArray($data,'dis_id');
			$where	=	' AND d.id in('.implode(',',$dis_id).')';
		}

		$page 	=	max(1,$p);
		$limit 	=	( $page - 1 ) * $pagesize;

		$info	=	M('','pf_','DB_CONFIG_AI')
            ->table(array(
                        C('DB_CONFIG_AI.db_prefix').'evaluation_result'	=>	'r',
                        C('DB_CONFIG_AI.db_prefix').'diseases'	=>	'd'
                    )
              )
            ->where('d.id = r.did AND r.connectid = '.$id.' AND r.fromid = "'.$client_id.'"'.$where )
            ->field('r.id,r.addtime,d.title')
            ->order('r.id DESC')
            ->limit($limit.','.$pagesize)
            ->select();

        return $info ? $info : array();
	}

	/**
	 * 获取关联用户评测列表详情
	 * @param  integer $id        关联ID
	 * @param  integer $listsid   列表ID
	 * @param  integer $client_id 应用ID
	 * @return array
	 */
	public function infoshow( $id = 0 , $listsid = 0 , $client_id = 0 ){
		if ( !$id || !$client_id || !$listsid ) {
			return false;
		}

		$info	=	M('','pf_','DB_CONFIG_AI')
            ->table(array(
                        C('DB_CONFIG_AI.db_prefix').'evaluation_result'	=>	'r',
                        C('DB_CONFIG_AI.db_prefix').'diseases'	=>	'd'
                    )
              )
            ->where('d.id = r.did AND r.connectid = '.$id.' AND r.fromid = "'.$client_id.'" AND r.id = '.$listsid )
            ->field('r.*,d.title')
            ->limit(1)
            ->find();

        return $info ? $info : array();
	}
}