<?php
/**
 * @author tangchengqi
 * 康宝系统7项检测接口
 * 2016.1.8 v.1.0
 */
namespace Kbox\Controller;
use Think\Controller;

/**
 * @author 唐成启
 * 2016.1.8
 * 队列读取
 *
 */
class QueueController extends Controller{
	//设备检测项目简写：血糖：gl 血氧：ox 体温：tm 体成分：we 血压：bp 血脂：bf 尿11项：ur 心电：el 血胴：bk 血尿酸：re 尿微量白蛋白：um
	//private $arr	=	array('ur');//尿11项暂不处理
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 *读取队列列表
	 */
	public function readqueue(){
		set_time_limit(0);
		 while(true){ 
			$data	=	array();
			$data	=	\Kbox\Model\QueueModel::getone();
			if($data){
				$report	=	new \Common\Common\reportredis();
				$report->addreportredis($data);
			}else{
				sleep(30);
			}
		 } 
	}
	
	
	/**
	 * 判断血糖状态异常情况
	 * 	0：正常 1：下降 2：上升
	 *
	 * @author wangyangyang
	 */
	public function rangegl(){
		set_time_limit(0);

		// 获取血糖正常参考范围
		$range 	=	\Common\Analysisclass\handle::rangedata();

		// 糖尿病类型 gl2:无 gl1:二型糖尿病 gl3:妊娠糖尿病 gl4:一型糖尿病
		$r 		=	array( 'gl2'=>$range['gl'] , 'gl1'=>$range['gl1'], 
						   'gl3'=>$range['gl3'], 'gl4'=>$range['gl4']
						);

		while(true){
			$info 	=	array();

			$mddb	=	M('kangbao_bbsugar');

			$where['status']	=	array('EXP','IS NULL');
			$info 	=	$mddb->field('id,userid,bloodsugar,attr')->where( $where )->order('id ASC')->limit(50)->select();

			if( !$info ){
				// 等待1小时执行
				sleep(3600);
			}else{
				$userinfo 	=	$this->getuserinfo($info);

				// 糖尿病类型：1、II型糖尿病 2、无 3、妊娠糖尿病 4、I型糖尿病
				foreach ($info as $key => $value) {
					// 获取糖尿病类型默认为 无
					$btype	=	isset($userinfo[$value['userid']]) && $userinfo[$value['userid']]['bsugar'] ? $userinfo[$value['userid']]['bsugar'] : 2;

					// 无进食状态的，默认为随机
					$attr 	=	$value['attr'] ? $value['attr'] : 3;

					$status =	0;

					$bloodsugar	=	$value['bloodsugar'] ? $value['bloodsugar'] : 0;

					if ( $bloodsugar < $r['gl'.$btype][$attr][0] ) {
						$status	=	1;
					}elseif ( $bloodsugar > $r['gl'.$btype][$attr][1] ) {
						$status	=	2;
					}else{
						$status	=	0;
					}
					$mddb->where(array('id'=>$value['id']))->save( array('status'=>$status) );
				}
			}
		}
	}

	/**
	 * 用户正常、异常总次数，异常率计算
	 * 
	 * @author wangyangyang
	 */
	public function statistics(){
		set_time_limit(0);

		$page 	=	1;

		while( true ){
			$info 	=	array();

			$mddb	=	M('kangbao_bbsugar');

			$curpage=	( $page - 1 ) * 20;

			$info 	=	$mddb->distinct(true)->field('userid')->limit($curpage.',20')->order('userid ASC')->select();

			if( !$info ){
				$page 	=	1;

				// 等待1小时十分钟执行
				sleep(3660);
			}else{

				$udb 	=	M('user_bbsugar');

				foreach ($info as $key => $value) {
					// 统计次数
					$sql 	=	'SELECT SUM( IF( `status` != 0, 1, 0)) AS count_yc,SUM( IF( `status` = 0, 1, 0)) AS count_zc,COUNT( id ) AS total FROM pf_kangbao_bbsugar WHERE userid =  '.$value['userid'].' LIMIT 1';

					$query 	=	$mddb->query($sql);

					$result	=	$query ? $query['0'] : '';

					if ( $result ) {
						$data 	=	array();
						$data['userid']		=	$value['userid'];
						$data['total']		=	$result['total'];
						$data['abnormal']	=	$result['count_yc'];
						$data['normal']		=	$result['count_zc'];
						$data['times']		=	date('Y-m-d H:i:s');
						$yc					=	sprintf("%.2f",$result['count_yc']);
						$rate				=	sprintf("%.2f",$yc/$result['total']);
						$data['rate']		=	intval( $rate * 100 );

						// 判断是否已经添加，如已经添加进行更新操作
						$check 	=	$udb->where( array('userid'=>$value['userid'] ))->find();
						if ( $check ) {
							$udb->where(array('userid'=>$value['userid'] ) )->save($data);
						}else{
							$udb->add($data);
						}
					}
				}
				$page++;
			}
		}
	}

	/**
	 * 获取用户糖尿病信息
	 * 
	 * @param  array  $info 
	 * @author wangyangyang
	 */
	private function getuserinfo( $info = array() ){
		if (!$info ) {
			return false;
		}

		$userid 	=	extractArray($info,'userid');

		if ( !$userid ) {
			return false;
		}

		$userid 	=	array_unique(array_filter($userid));
		sort($userid);
		$where['userid']	=	array('in',$userid);

		$userinfo 	=	M('member_detail')->where( $where )->field('userid,bsugar')->select();

		return $userinfo ? handleArrayKey($userinfo,'userid') : false;
	}
}