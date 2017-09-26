<?php
namespace Common\Common;
/**
 * @author tangchengqi
 * 2016.1.8
 * 检测报告 redis使用;
 */
class reportredis extends phpredis {
	//设备检测项目简写：血糖：gl 血氧：ox 体温：tm 体成分：we 血压：bp 血脂：bf 尿11项：ur 心电：el 血胴：bk 血尿酸：re 尿微量白蛋白：um
	private 	$report	   =	'report';
	private 	$arr	   =	array('gl','ox','tm','we','bp','bf','ur','el','bk','re','um');//网络医院
	protected  $array	   =	array('1'=>'gl','2'=>'bp','3'=>'ox','4'=>'bf','5'=>'we','6'=>'ur','7'=>'tm','8'=>'el','9'=>'bk','10'=>'re','11'=>'um');

	// 单项编码
	protected $singbm		=	array('gl'=>'02','ox'=>'04','tm'=>'09','we'=>'07','bp'=>'03','bf'=>'05','ur'=>'08','el'=>'06','bk'=>'10','re'=>'11','um'=>'12');

	public function __construct(){
		parent::__construct();
	}

	
	
	/**
	 * 读取队列表,生成每天的报告,存入redis 
	 * @param unknown $data 读取的检测数据队列
	 */
	public function addreportredis($data=array()){
		if(!$data){
			return '';
		}
		//获取redis数据是否存在
		$keyname	=	$this->createkeyname($this->report,$data['userid']);
		$tmpdata	=	$this->formatdataget($keyname);
		$checkarr	=	\Kbox\Model\BaseModel::getdata($data['type'],$data['userid'],$data['insertid']);
		//药店id
		$drugid		=	$data['drugid'];
		// 处理 drugid
		$drugid		=	$drugid ? $drugid : 0;
		//卡号id
		$cardid		=	$data['cardid'];
		

		if($checkarr){
			// 查询卡号信息，判断该卡是否为体验卡
			$cardinfo 	=	M('drug_card_user')->where(array('id'=>$data['cardid'],'cardtype'=>'204'))->find();

			// 如果为体验卡，并且检测项为血糖时候，重新查询下血糖进食状态
			if ( $cardinfo && $data['type'] == 'gl' ) {
				$btime 		=	strtotime(date('Y-m-d 00:00:00'));
				$etime 		=	strtotime(date('Y-m-d 23:59:59'));
				$exwhere 	=	array();
				$exwhere['cardid']	=	$data['cardid'];
				$exwhere['userid']	=	$data['userid'];
				$exwhere['addtime']	=	array('between',array($btime,$etime));

				$attrinfo 	=	M('drug_examattr')->where( $exwhere )->field('id,attr')->order('id DESC')->find();


				// 默认为随机血糖 
				$checkarr['attr']	=	$attrinfo && $attrinfo['attr'] ? $attrinfo['attr'] : 3;

				// 更新血糖记录表中血糖状态
				M('kangbao_bbsugar')->where(array('id'=>$data['insertid']))->save( array('attr'=>$checkarr['attr']));
			}

			$report		=	\Common\Common\factorykangbao::create($data['type'],$checkarr);
			
			$this->singleredis($data['type'],$data['userid'],$report);

			if(in_array($data['type'],$this->arr)){ 
			 	
				// 康宝
				if(!$this->comparetime(time(),$data['extime'])){
					//超过一天重新生成
					$this->rm($keyname);
					$tmpdata	=	array();
				}

				// 如果为体验卡检测数据，判断是否为同一次的检测数据，如果不是，清空！
				if ( $cardinfo && $tmpdata &&  isset($tmpdata['examstatusid']) && $tmpdata['examstatusid'] != $data['examstatusid'] ) {
					$this->rm($keyname);
					$tmpdata	=	array();
				}


				$formatdataget	=	$this->formatdataget($keyname);

				if( $formatdataget ){
					// 判断redis中存储的时间，保证存储的报告为最新的报告
					if ($formatdataget && isset($formatdataget['extime']) && $formatdataget['extime'] && !$this->comparetime(time(),$formatdataget['extime'])) {
						$this->rm($keyname);
						$tmpdata	=	array();
					}
					//键值存在
					$tmpdata['extime']		=	$data['extime'];
					$report['extime']		=	$data['extime'];
					$report['cardid']		=	$data['cardid'];
					$report['insertid']		=	$data['insertid'];
					$tmpdata['reportcode']	=	$this->createcode($drugid,$data['userid']);
					$tmpdata['examstatusid']=	$data['examstatusid'];
					$tmpdata[$data['type']]	=	$report;
				}else{
					// 键值不存在
					if( !$this->comparetime(time(),$data['extime']) ){
						\Kbox\Model\QueueModel::update($data['id']);
					}

					$tmpdata				=	array();
					$tmpdata['extime']		=	$data['extime'];
					$tmpdata['reportcode']	=	$this->createcode($drugid,$data['userid']);
					$tmpdata['examstatusid']=	$data['examstatusid'];
					$report['extime']		=	$data['extime'];
					$report['cardid']		=	$data['cardid'];
					$report['insertid']		=	$data['insertid'];
					$tmpdata[$data['type']]	=	$report;
				}
				
				$this->updatequeue($tmpdata,$data['id'],$data['userid']);
				$reporttabletype 	=	0;

			}else{
				//单一血糖仪

				$tmpdata				=	array();
				$tmpdata['extime']		=	$data['extime'];
				//报告编码暂未空
				$tmpdata['reportcode']	=	'';
				$tmpdata['gl']			=	$report;
				$this->updatequeue($tmpdata,$data['id'],$data['userid'],1);
				
				$reporttabletype 	=	1;
			} 

			// 等待 1秒，预防Redis未写入成功
			sleep(1);

			$reportdata 	=	$this->createallreport($data['userid']);

			// 将用户信息同时写入到redis中
			$this->createuserinfo($data['userid']);


			// 报告写入表
			$reportid	=	'';

			if ( $cardinfo && $reportdata ) {
				$reportid 	=	\Kbox\Model\Kangbao_reportModel::add($reportdata,$cardid,$drugid,$data['userid'],$reporttabletype,$data['gate'],$data['examstatusid']);
			}elseif ( $reportdata ) {
				$reportid 	=	\Kbox\Model\Kangbao_reportModel::add($reportdata,$cardid,$drugid,$data['userid'],$reporttabletype,$data['gate']);
			}

			// 心电解读特殊处理
			if ($reportid && $data['type'] == 'el') {
				M('kangbao_electrocardio')->where(array('id'=>$data['insertid']))->save(array('reportid'=>$reportid));
			}

			// 按照体检次数生成报告，做备份使用
			if ( $reporttabletype == 0 && $reportdata ) {
				\Kbox\Model\Kangbao_reportModel::addback($reportdata,$cardid,$drugid,$data['userid'],$data['gate'],$data['examstatusid']);
			}
			
			//健康数据redis
			$health	=	new \Common\Common\healthredis();
			$health->addhealth($data['type'],$report,$data['userid'],$data['extime']);

		}else{
			\Kbox\Model\QueueModel::update($data['id']);
		} 
	}
	
	
	/**
	 * 单个小项redis
	 * @param string $prvname 例如 血糖：gl 血氧：ox 体温：tm 体成分：we 血压：bp
	 * @param number $userid 用户id
	 *  @param number $data  报告数组
	 */
	private function singleredis($prvname='',$userid=0,$data=array()){
		if(!$userid){
			return '';
		}
		
		if($prvname=='1'){
			//单一血糖检测
			$prvname	=	'gl';
		}
		$keyname	=	$this->createkeyname($prvname,$userid);
		//报告码
		$this->formatdataset($keyname,$data);
	}
	
	
	
	
	/**
	 * 两个时间戳转换成天比较
	 * @param number $time1
	 * @param number $time2
	 */
	public function comparetime($time1=0,$time2=0){
		if(empty($time1) || empty($time2)){
			return false;
		}
		$day1	=	date('Y-m-d',$time1);
		$day2	=	date('Y-m-d',$time2);
		if($day1==$day2){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 更新队列状态，并生成报告
	 * @param unknown $data 报告数据
	 * @param number $queueid 队列id
	 * @param number $userid 用户id
	 * @param number $type 0:康宝设备,1:单一血糖仪
	 */
	private function updatequeue($data=array(),$queueid=0,$userid=0,$type=0){
		if(!$data){
			return '';
		}
		$keyname	=	$this->createkeyname($this->report,$userid);
		//更新队列状态
		if($this->formatdataset($keyname,$data)){
			\Kbox\Model\QueueModel::update($queueid);
		}
		
	}
	
	
	/**
	 * 所有正常项的报告
	 * @param $tmpdata redis 值是否存在
	 * @param number $userid 用户id
	 */
	public function createallreport(/* $tmpdata=array(), */$userid=0){
		if( !$userid ){
			return '';
		}
		//报告所有正常项
		 $keyname					=	$this->createkeyname($this->report,$userid);
		 $tmpdata					=	$this->formatdataget($keyname); 
		 $normals['all']			=	$this->gettotalreport($tmpdata);
		if( $normals['all'] ){
			//一天之内,正常项的整体报告
			$result					=	new \Common\Common\kangbaoresult();
			$normal['resources']	=	$result->result($normals,'resources');
			if($tmpdata){
				//redis值存在
				$tmpdata['resources']	=	$normal['resources'];
				$this->formatdataset($keyname,$tmpdata);
			}
			
		}
		return $tmpdata;
	}


	/**
	 * 用户信息保存
	 * @param number $userid 用户id
	 * 
	 * 
	 */
	public  function createuserinfo( $userid = 0) {
		if( !$userid ){
			return false;
		}
		// 判断键值是否存在
		$keyname	=	$this->createkeyname( $this->report , $userid );
		$data		=	$this->formatdataget( $keyname );

		if( $data && isset($data['userinfo']) ){
			return false;
		}

		$where 	=	array('userid'=>$userid);
		$info 	=	M('member')->field('userid,username,nickname')->where($where)->find();
		$info2 	=	M('member_detail')->field('sex,height,birthday,bsugar')->where($where)->find();

		if ( !$info && !$info2 ) {
			return false;
		}

		$result =	array_merge($info,$info2);
		$result['sex']	=	$result['sex'] == 0 ? '男' : '女';
		$result['age']	=	$result['birthday'] ? age($result['birthday']) : '';
		$result['bsugar']	=	$result['bsugar'] ? $result['bsugar'] : '';
		$data['userinfo']	=	$result;

		$this->formatdataset($keyname,$data);

		return true;
	}

	
	/**
	 * 康宝8项检测结果正常项的id字符串(一次获取多个，中间用英文逗号拆分) 1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温
	 * @param unknown $data 传入数组
	 * @param unknown $type 为 0,去查询8项中都正常的项的id(多个小项时,每一小项都正常),为1,返回第一个正常项的名称 */
	private function gettotalreport($data = array(),$type=0){
		if(!$data){
			return '';
		}
		$str 	 = '';
		$arr     = array();
		$tmptype	=	0;
		foreach($data as $k=>$v){
			if(is_array($v)){
				foreach($v as $key=>$val){
					if(is_array($val)){
						if($val['type']){
							$tmptype	=	1;
							break;
						}else{
							$tmptype	=	2;
						}
					}
					//break;
				}				
			
				 foreach($this->array as $ke=>$value){
					 if(($k==$value) && $tmptype==2){
						$str.=$ke.',';
					 }
				} 
			}
			$tmptype	=	0;
		}
		//$this->array中第一个正常项值
		if($type && $str){
			$tmparr = explode(',',$str);
			return $this->array[$tmparr['0']];
		}else{
			return rtrim($str,',');
		}
	
	}
	
	/**
	 * 生成报告编码
	 * @param number $drugid 药店id
	 * @param number $userid 用户id
	 *  @param number $pre 报告前缀
	 */
	public function createcode($drugid=0,$userid=0,$pre='01'){
		if(!$drugid){
			return '';
		}
		$middle	=	\Kbox\Model\Member_storeModel::getone($drugid);
		if($middle){
			$end	=	$this->createendcode($drugid);
			return $pre.$middle.$end;
		}else{
			return '';
		}
		
	}
	
	/**
	 *  生成报告编码 尾部编码
	 * @param number $drugid 用户id
	 * @param number $endcode 末尾报告编码最大6位
	 * @param number $endcode 末尾报告编码开始6位
	 */
	public function createendcode($drugid=0,$code=1000000,$endstart='000000'){
		if(!$drugid){
			return '';
		}
		$end	=	'';
		$reportcode	=	\Kbox\Model\Kangbao_reportModel::getallcount($drugid);
		//求余,超过1百万重新计算
		$tmpcode	=	$reportcode%$code;
		if($tmpcode){
			if($tmpcode<$code){
				$length	= (strlen($code)-1)-strlen($reportcode);
				$j	=	0;
				for($i=0;$i<$length;$i++){
					$end	.=	$j;
				}
				$end	.=	$tmpcode;
			}else{ 
				$end	=	$endstart;
			 } 
		}else{
			$end	=	$endstart;
		}
		return $end;	
	}
	

	/**
	 * 单项编码
	 * @param string $type 
	 */
	public function singlecode( $type='gl' ){
		$result 	=	$this->singbm;
		return $result[$type];
	}

	/**
	 * 生成单项报告编码
	 * @param number $drugid 药店id
	 * @param number $userid 用户id
	 *  @param number $pre 报告前缀
	 */
	public function createonecode($drugid=0,$userid=0,$pre='01'){
		if(!$drugid ){
			return '';
		}
		$middle	=	\Kbox\Model\Member_storeModel::getone($drugid);
		if($middle){
			$end	=	$this->createoneendcode($drugid);
			return $pre.$middle.$end;
		}else{
			return '';
		}
		
	}

	/**
	 *  生成单项报告编码 尾部编码
	 * @param number $drugid 用户id
	 * @param number $endcode 末尾报告编码最大6位
	 * @param number $endcode 末尾报告编码开始6位
	 */
	public function createoneendcode($drugid=0,$code=1000000,$endstart='000000'){
		if(!$drugid){
			return '';
		}
		$end	=	'';
		$reportcode	=	\Kbox\Model\QueueModel::getallcount($drugid);
		
		//求余,超过1百万重新计算		
		$tmpcode	=	$reportcode%$code;
		if($tmpcode){
			if($tmpcode<$code){
				$length	= (strlen($code)-1)-strlen($reportcode);
				$j	=	0;
				for($i=0;$i<$length;$i++){
					$end	.=	$j;
				}
				$end	.=	$tmpcode;
			}else{ 
				$end	=	$endstart;
			 } 
		}else{
			$end	=	$endstart;
		}
		return $end;	
	}
}	