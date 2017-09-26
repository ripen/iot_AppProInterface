<?php
namespace Updatearticle\Controller;
use Think\Controller;
use Updatearticle\Common\MyClass\bmi;
use Updatearticle\Common\MyClass\classFactory;

class IndexController extends Controller {
	private $returnOK	= 'resultOK';
	private $returnError= 'resultError';
	private $resultOKmsg	= '获取Token成功!';
	private $resultErrormsg	= '获取Token失败!请检查服务地址、访问帐号和访问口令是否正确!';
	private $tokenlifetime	= '10';	//TOKEN生命值 ，单位秒；
	

	private $apptype	= '';
	
 	public function __construct(){
		parent::__construct();
	}
	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2015/8/26
	* @return JSON
	*/
	private function index(){
		set_time_limit(0);
		$news		= M('ecms_news');
		$newsData	= M('ecms_news_data_1');
		$sitenews		= M('website_news');
		$sitenewsData	= M('website_news_data');

		$infos	= $news->where('classid=9')->order('newstime ASC ')->select();

		for ($i = 0, $_max = count($infos);$i < $_max; $i++){
			$data	= array(
							'catid'		=> 19,
							'typeid'	=> 0,
							'url'		=> '',
							'title'		=> $infos[$i]['title'],
							'keywords'	=> $infos[$i]['keyboard'],
							'description'	=> $infos[$i]['smalltext'],
							'status'	=> 99,
							'username'	=> 'yicheng',
							'inputtime'	=> $infos[$i]['newstime'],
							'updatetime'=> $infos[$i]['newstime'],
						);
			$insertid = $sitenews->add($data);

			$infoss	= $newsData->where('id='.$infos[$i]['id'])->find();
			
			$cdata	= array(
							'id'				=>$insertid,
							'groupids_view'		=>'',
							'template'			=>'',
							'paginationtype'	=>'0',
							'maxcharperpage'	=>'0',
							'content'			=>$infoss['newstext'] ? $infoss['newstext'] : '',
						);
			$sitenewsData->add($cdata);
		}

		echo 'OK';
		
    }

	/**
	* 获取当前企业用户可操作的模块
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	public function getPowers(){
		$infoArr	= I();
		if ($infoArr['token'] === $this->get_token()) {
			$levels	= $this->userdb->where('id='.$infoArr['userid'])->getField('level');
			echo json_encode($this->getCheckLists($levels));
		}else{
			echo json_encode(array('status'=>'0'));
		}
	}

	/**
	* 获得所有接口检测模块
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	private function getCheckLists($levelid='all'){
		
		if ($levelid	=='all') {
			return M('list')->select();
		}else{
			$condition['id']  = array('exp',' IN ('.$levelid.') ');
			return M('list')->where($condition)->getField('url,name');			
		}
	}

	/**
	* 根据结果值，获取对应的接口参数
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2015/8/26
	*/
	public function getdata(){
		$infoArr	= I();

		if ($infoArr['token'] === $this->get_token()) {
			$apptype		= $infoArr['apptype'];
			$classApptype	= classFactory::createFactory($apptype);

			echo "<PRE>";
			print_r($classApptype->getDatas());
			exit();
			//$analyseData		= $this->executeAnalyse($infoArr['values'],$infoArr['attrs']);
			$returnData = array(
									'apptype'	=>$infoArr['type'],
									'bloodsugar'=>$infoArr['values'],
									'sn'		=>'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
									'dates'		=>date("Y-m-d H:i:s"),
									'attrs'		=>$infoArr['attrs'],
									'analysedata'=>$analyseData,
									'otherinfos'=>$infoArr['others'],
									'status'	=>'1',
								);
			echo json_encode($returnData);
		}else{
			echo json_encode(array('status'=>'0'));
		}

	}



	private function set_token() {
		$token	= md5(microtime(true));
		S('token',$token,$this->tokenlifetime);
	}

	private function get_token() {
		return S('token');
	}

	/**
	* 添加采集到的信息
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2015/8/23
	*/
	private function addCollectinfos(){
	
	}

     /**
	 * 血糖指标分析结果
	 * @param [type] $value 数值
	 * @param [type] $type_id 类别
	 * @param [type] $attr_id 属性
	 * @return array
	* @author ripen_wang@163.com
	 */
   private function executeAnalyse($value = '',$attr_id = '4', $type_id = '1'){
		$value = str_replace('%','',$value);
		$analyse = M('health_data_analyse')->where("type_id = {$type_id} AND attr_id = {$attr_id}")->select();

		foreach ($analyse as $key=>$val ){
			$formula = str_replace('%','',$val['formula']);
			$formula = str_replace('#val#',$value,$val['formula']);
			if(@eval("return $formula;")) { 
				$infos	= array(
							'conclusion'=>$val['analysis'],
							'reason'	=>$val['reason'],
							'suggest'	=>$val['suggest'],
						);
				return $infos;
			}//嵌套公式
		}
		return false;
	}
}