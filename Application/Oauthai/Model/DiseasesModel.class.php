<?php
namespace Oauthai\Model;
use Think\Model;

/**
 * 疾病管理
 * 
 * @author wangyangyang
 * @version V1.0
 */
class DiseasesModel extends Model {  

	protected $autoCheckFields = false;


	/**
	 * 疾病列表信息
	 * 
	 * 
	 * @param  integer $id 疾病大类
	 * @return array/bool
	 * @author wangyangyang
	 * @version V1.0
	 */
	public function lists( $id ){
		if ( !$id ) {
			return false;
		}

        $info	=	M('','pf_','DB_CONFIG_AI')
            ->table(array(
                        C('DB_CONFIG_AI.db_prefix').'type_dis'	=>	't',
                        C('DB_CONFIG_AI.db_prefix').'diseases'	=>	'd'
                    )
              )
            ->where('t.dis_id = d.id and t.type_id = '.$id .' AND d.is_show = 1' )
            ->field('d.id,d.title')
            ->order('d.id ASC')
            ->select();

		return $info ? $info : array();
	}

	/**
	 * 疾病详情
	 * 
	 * @param  integer $id 疾病ID
	 * @return [type]     [description]
	 */
	public function show( $id = 0 ){
		if (!$id ) {
			return false;
		}

		$info 	=	M('diseases','pf_','DB_CONFIG_AI')->where( array('id'=>$id))->field('id,title')->find();
		return $info ? $info : false;
	}


	/**
	 * 解决方案 疾病改善列表
	 * 
	 * @param  integer $id 疾病id
	 * @param  integer $page 当前页
	 * @param  integer $pagesize 每页显示条数
	 * @return array
	 */
	public function improvelists( $id = 0 , $page = 1 , $pagesize = 20 ){
		if ( !$id ) {
			return false;
		}

		$curpage 	=	( $page - 1 ) * $pagesize;


		$info		=	M('','pf_','DB_CONFIG_AI')
            ->table( array(
                        C('DB_CONFIG_AI.db_prefix').'impro_dis'		=>	'd',
                        C('DB_CONFIG_AI.db_prefix').'improvement'	=>	'i'
                    )
              )
            ->where('d.impro_id = i.id AND d.dis_id = '.$id )
            ->field('i.id,i.title,i.img,i.brief_content as description,i.addtime')
            ->limit($curpage.','.$pagesize)
            ->order('i.id ASC')
            ->select();

		return $info ? $info : false;
	}

	/**
	 * 检测改善详情与疾病是否有关联关系
	 * 
	 * @param  integer $id  疾病改善ID
	 * @param  integer $did 疾病id
	 * @return bool
	 */
	public function checkimprove( $id = 0 , $did = 0 ){
		if ( !$id || !$did ) {
			return false;
		}

		$check 	=	M('impro_dis','pf_','DB_CONFIG_AI')->where( array('dis_id'=>$did,'impro_id' => $id ))->find();

		return $check ? true : false;
	}

	/**
	 * 解决方案 疾病改善详情
	 * 
	 * @param  integer $id 疾病id
	 * @return array
	 */
	public function improveshow( $id = 0 ){
		if ( !$id ) {
			return false;
		}

		// 通过关联关系，查询疾病大类下的疾病信息
		$info 	=	M('improvement','pf_','DB_CONFIG_AI')->field('id,title,img,brief_content as description,content,addtime,link')->where( array( 'id' => $id) )->find();

		return $info ? $info : false;
	}
	

	/**
	 * 解决方案 药品改善、保健调理
	 * 
	 * @param  integer $id       疾病ID
	 * @param  integer $page     当前页
	 * @param  integer $pagesize 每页显示条数
	 * @param  integer $type     商品类型 1药品，2保健品，3医疗器械，4食品，5虚拟商品，6其他
	 * @return array
	 */
	public function shopslists( $id = 0 , $page = 1 , $pagesize = 20 , $type = 1 ){
		if ( !$id ) {
			return false;
		}

		$page 	=	max(1,$page);
		$limit 	=	( $page - 1 ) * $pagesize;

		$info	=	M('','pf_','DB_CONFIG_AI')
            ->table(array(
                        C('DB_CONFIG_AI.db_prefix').'shop_dis'	=>	'd',
                        C('DB_CONFIG_AI.db_prefix').'shops'	=>	's'
                    )
              )
            ->where('d.shop_id = s.id AND d.dis_id = '.$id.' AND s.type = "'.$type.'"' )
            ->field('s.id,s.title,s.brief_content as content,s.links,s.img,s.addtime')
            ->order('d.id ASC')
            ->limit($limit.','.$pagesize)
            ->select();

        return $info ? $info : array();
	}
}