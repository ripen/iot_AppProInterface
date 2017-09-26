<?php
/**
 * @author tangchengqi
 * 康宝系统7项检测接口
 * 2015.10.10 v.1.1
 */
namespace Kbox\Controller;
use Think\Controller;

class MindexController extends Controller{

	private $returnOK		= 'resultOK';
	private $returnError	= 'resultError';
	private $resultOKmsg	= '获取Token成功!';
	private $resultErrormsg	= '获取Token失败!请检查服务地址、访问帐号和访问口令是否正确!';
	private $tokenErrormsg	= 'Token无效，请重新获取';
	private $tokenlifetime	= '3000';	//TOKEN生命值 ，单位秒；
	
	
	//不需要验证token的action
	private  $nologin = array('index');
	

 	public function __construct(){
		parent::__construct();
		$this->memberdb	= M('member');


		$this->checktoken();
	}

	public function index(){
		$username	=	I('post.username','','htmlspecialchars,trim');
		$encrypt	=	I('post.encrypt','','htmlspecialchars,trim');

		$where 		=	array();
		if ( !$username || !$encrypt) {
			echo json_encode(array(	'result'=>$this->returnError,
									'resultmsg'=>$this->resultErrormsg
							));
			exit;
		}

		$where['username']	=	$username;
		$where['encrypt']	=	$encrypt;

		$userid	= $this->memberdb->where($where)->getField('userid');

		
		if ( $userid ) {
			$this->set_token();
			echo json_encode(array(	'result'=>$this->returnOK,
									'resultmsg'=>$this->resultOKmsg,
									'token'=>$this->get_token(),
									'authid'=>$userid,
							));
			exit;
		}else{
			echo json_encode(array(	'result'=>$this->returnError,
									'resultmsg'=>$this->resultErrormsg
							));
		}
    }


    /**
	* 血糖指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		data	测试值( 单位：mmol/L )
	*				times	测试时间(默认为空腹)
	*					1：空腹 2：餐后两个小时 3：随机
	*				history 糖尿病史(默认为无)
	*     				2 ： 无 
	*         			4 ：1型糖尿病 
	*            		1 ：2型糖尿病 
	*              		3 ：妊娠糖尿病
	* @return		
	*/
	public function bsugar(){
		$data 		=	I('data','','htmlspecialchars,trim');
		$times		=	I('times','','intval');
		$history	=	I('history','','intval');
		
		$times		=	$times ? intval($times) : 3;
		$history	=	$history ? intval($history) : 2;
		if ( !$data || !is_numeric($data) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
		
		$result		=	\Common\Analysisclass\result::factory()->bsugar(
			$data,$times,$history);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
		$datas	=	array();
		$datas['bloodsugar']	=	$data;
		$datas['attr']			=	$times;
		$datas['history']		=	$history;
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bsugar($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
	    $this->returndata($result);
	}

	/**
	 * 血脂分析
	 *
	 * @param  data1 type1  TCH 总胆固醇
	 * @param  data2 type2  TG甘油三酯（三酰甘油）
	 * @param  data3 type3  LDL-C低密度脂蛋白胆固醇
	 * @param  data4 type4  HDL-C高密度脂蛋白胆固醇        
	 */
	public function bloodfat(){
		$data1 		=	I('data1','','htmlspecialchars,trim');
		$data2 		=	I('data2','','htmlspecialchars,trim');
		$data3 		=	I('data3','','htmlspecialchars,trim');
		$data4 		=	I('data4','','htmlspecialchars,trim');
		
		
		$result		=	\Common\Analysisclass\result::factory()->bloodfat(
			$data1,$data2,$data3,$data4);
	
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas['tc']	=	$data1;
		$datas['tg']	=	$data2;
		$datas['ltc']	=	$data3;
		$datas['htc']	=	$data4;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bloodfat($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
	    $this->returndata($result);
	}


	/**
	* 血压指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		hdata	测试值
	* @param  		ldata   测试值
	* @return		
	*/
	public function bloodp(){
		$hdata 		=	I('hdata','','htmlspecialchars,trim');
		$ldata		=	I('ldata','','htmlspecialchars,trim');
		
		if ( !$hdata || !is_numeric($hdata) || !$ldata || !is_numeric($ldata) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		// 高压值必须大于低压
		// 2017-09-19
		// 石华提出修改意见
		if ( $hdata < $ldata ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
		
		$result		=	\Common\Analysisclass\result::factory()->bloodp(
			$hdata,$ldata);

		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas['hboodp']	=	$hdata;
		$datas['lboodp']	=	$ldata;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bloodp($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$this->returndata($result);
	}


	/**
	* 体成分
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @return		
	*/
	public function humanbody(){
		$data1 		=	I('data1','','htmlspecialchars,trim');
		$data2 		=	I('data2','','htmlspecialchars,trim');
		$data3 		=	I('data3','','htmlspecialchars,trim');
		$data4 		=	I('data4','','htmlspecialchars,trim');
		$data5 		=	I('data5','','htmlspecialchars,trim');
		$data6 		=	I('data6','','htmlspecialchars,trim');
		$data7 		=	I('data7','','htmlspecialchars,trim');
		$data8 		=	I('data8','','htmlspecialchars,trim');
		$data9 		=	I('data9','','htmlspecialchars,trim');
		$sex 		=	I('sex','0','intval');
		
		
		$result		=	\Common\Analysisclass\result::factory()->humanbody($data1,$data2,$data3,
			$data4,$data5,$data6,$data7,$data8,$data9,$sex);

		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas['weight']			=	$data1;
		$datas['bmi']				=	$data2;
		$datas['bf']				=	$data3;
		$datas['fatweight']			=	$data4;
		$datas['protein']			=	$data5;
		$datas['watercontentrate']	=	$data6;
		$datas['muscle']			=	$data7;
		$datas['mineralsalts']		=	$data8;
		$datas['fat']				=	$data9;
		$datas['sex']				=	$sex;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::humanbody($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
		$this->returndata($result);
	}


	/**
	* 血氧指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		data	血氧饱和度
	* @param		data2	脉率
	* @return		
	*/
	public function oxygen(){
		$data 		=	I('data','','htmlspecialchars,trim');
		$data2		=	I('data2','','htmlspecialchars,trim');
		
		if ( !$data || !is_numeric($data)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		$result		=	\Common\Analysisclass\result::factory()->oxygen($data,$data2);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas	=	array();
		$datas['saturation']	=	$data;
		$datas['pr']			=	$data2;
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::oxygen($result,$datas);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

	    $this->returndata($result);
	}


	/**
	* 恒温指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		data	测试值
	* @return		
	*/
	public function tm(){
		$data 		=	I('data','','htmlspecialchars,trim');
		
		if ( !$data || !is_numeric($data)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
		
		$result		=	\Common\Analysisclass\result::factory()->tm($data);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas	=	array();
		$datas['tmv']	=	$data;
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::tm($result,$datas);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

	    $this->returndata($result);
	}


	/**
	* 心电指标分析接口（主要分析心率）
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		data	测试值
	* @return		
	*/
	public function el(){
		$data 		=	I('data','','htmlspecialchars,trim');
		
		if ( !$data || !is_numeric($data)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
		
		$result		=	\Common\Analysisclass\result::factory()->el($data);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas	=	array();
		$datas['bpm']	=	$data;
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::el($result,$datas);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

	    $this->returndata($result);
	}


	/**
	* 尿11项
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		hdata	测试值
	* @param  		ldata   测试值
	* @return		
	*/
	public function urine(){
		$data1 		=	I('data1','','htmlspecialchars,trim');
		$data2 		=	I('data2','','htmlspecialchars,trim');
		$data3 		=	I('data3','','htmlspecialchars,trim');
		$data4 		=	I('data4','','htmlspecialchars,trim');
		$data5 		=	I('data5','','htmlspecialchars,trim');
		$data6 		=	I('data6','','htmlspecialchars,trim');
		$data7 		=	I('data7','','htmlspecialchars,trim');
		$data8 		=	I('data8','','htmlspecialchars,trim');
		$data9 		=	I('data9','','htmlspecialchars,trim');
		$data10		=	I('data10','','htmlspecialchars,trim');
		$data11		=	I('data11','','htmlspecialchars,trim');
		
		
		$result		=	\Common\Analysisclass\result::factory()->urine($data2,$data1,
			$data3,$data4,$data5,$data6,$data7,$data8,$data9,$data10,$data11);
	
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas	=	array();
		$datas['nitrite']		=	$data1;
		$datas['urobilinogen']	=	$data2;
		$datas['whitecells']	=	$data3;
		$datas['redcells']		=	$data4;
		$datas['urineprotein']	=	$data5;
		$datas['urineketone']	=	$data8;
		$datas['bili']			=	$data9;
		$datas['sugar']			=	$data10;
		$datas['vc']			=	$data11;
		$datas['ph']			=	$data6;
		$datas['urine']			=	$data7;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::urine($result,$datas);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$this->returndata($result);
	}


	/**
	* 血酮指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		data	测试值
	* @return		
	*/
	public function bloodketone(){
		$data 		=	I('data','','htmlspecialchars,trim');
		
		if ( !$data || !is_numeric($data)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
		
		$result		=	\Common\Analysisclass\result::factory()->bloodketone($data);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas	=	array();
		$datas['bk']	=	$data;
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::bloodketone($result,$datas);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

	    $this->returndata($result);
	}


	/**
	 * 血尿酸分析
	 *
	 * @param  data1 type1  血尿酸
	 * @param  sex     性别 0：男 1：女 
	 *    
	 */
	public function renal(){
		$data1 		=	I('data1','','htmlspecialchars,trim');
		$sex 		=	I('sex','0','intval');
		
		$result		=	\Common\Analysisclass\result::factory()->renal(
			$data1,$sex);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas['suricacid']	=	$data1;
		$datas['sex']		=	$sex;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::renal($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
	    $this->returndata($result);
	}


	/**
	 * 血尿酸分析(μmol/L)
	 *
	 * @param  data1 type1  血尿酸
	 * @param  sex     性别 0：男 1：女 
	 *    
	 */
	public function renalnew(){
		$data1 		=	I('data1','','htmlspecialchars,trim');
		$sex 		=	I('sex','0','intval');
		
		$result		=	\Common\Analysisclass\result::factory()->renalnew(
			$data1,$sex);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas['suricacid']	=	$data1;
		$datas['sex']		=	$sex;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::renalnew($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
	    $this->returndata($result);
	}



	/**
	 * 尿微量白蛋白分析
	 *
	 * @param  data type1  尿微量白蛋白
	 *    
	 */
	public function umprotein(){
		$data 		=	I('data','','htmlspecialchars,trim');
		
		$result		=	\Common\Analysisclass\result::factory()->umprotein($data);

		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

		$datas['um']	=	$data;

		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::umprotein($result,$datas);
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}
	    $this->returndata($result);
	}

	/**
	* 整体大项正常返回结果
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		types	设备类型 (一次获取多个，中间用英文逗号拆分)
	*         			1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温
	* @return		
	*/
	public function resources(){
		$types 		=	I('types','','htmlspecialchars,trim');
		
		
		if ( !$types ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		$result		=	\Common\Analysisclass\result::factory()->resources($types);
		
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::resources($result);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

	    $this->returndata($result);
	}


	/**
	* 检测项目参考范围
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		types	
	* @return		
	*/
	public function rangedatas(){
		$types 		=	I('types','','htmlspecialchars,trim');
		
		//	处理获取到的数据
		$result		=	\Common\Analysisclass\handle::rangedata($types);
		
		if ( !$result ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未获取到结果'
						));
			exit;
		}

	    $this->returndata($result);
	}


    private function set_token() {
		$token	= md5(microtime(true));
		S('mindextoken',$token,$this->tokenlifetime);
	}

	private function get_token() {
		return S('mindextoken');
	}


	/**
	* 返回结果
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		$result 数据库查询出来的结果
	*         		$title  检测类型
	* @return		json 格式
	*/
	private function returndata( $result ){
		if( empty($result) ){
	    	echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未查到相应的结果！'
						));
			exit;
	    }

	    echo json_encode($result);
	    exit;
	}

    /**
	 * 检测token
	 * @author		wangyangyang
	 * @copyright	wangyang8839@163.com
	 * @version		1.0
	 * @param  string $token 
	 * @return 
	 */
	private function checktoken( ){

		if ( in_array(ACTION_NAME,$this->nologin) ) {
			return true;
		}

		$token		=	I('token','','htmlspecialchars,trim');
		if( !$token ){
			echo json_encode(array(	'result'=>$this->returnError,
								'resultmsg'=>$this->tokenErrormsg
						));
			exit;
		}

		$checktoken 	=	$this->get_token();
		if(!$checktoken || $checktoken != $token){
			echo json_encode(array(	'result'=>$this->returnError,
								'resultmsg'=>$this->tokenErrormsg
						));
			exit;
		}
	}




	/**
	 * 四海华城体支称分析
	 * @param  array $info 检测信息
	 * @return [type] [description]
	 */
	public function sshc( ){
		// 获取code
		$url 		=	'http://lshapi.scintakes.com/partner/author';
		$post 		=	array('appid'=>C('SHHC_APPID'),'appkey'=>C('SHHC_APPKEY'),'reptype'=>'token');

		$data 		=	$this->https_request($url,$post);
		
		$data 		=	$data ? json_decode($data,true) : '';

		F('sshc_time',date('Y-m-d H:i:s'));

		F('sshc_code',$data);

		if (!$data || !$data['data']) {
			echo json_encode(array(	'result'=>$this->returnError,
								'resultmsg'=>'获取code失败'
						));
			exit;
		}

		// 获取
		$url 	=	'http://lshapi.scintakes.com/partner/accesstoken';
		$post 	=	array('appid'=>C('SHHC_APPID'),'name'=>C('SHHC_NAME'),'pwd'=>C('SHHC_PWD'),'check'=>strtoupper(md5(strtoupper($data['data'].C('SHHC_APPID').C('SHHC_APPKEY')))) );

		$data 	=	$this->https_request($url,$post);

		$data 		=	$data ? json_decode($data,true) : '';

		F('sshc_token',$data);

		$result =	array();

		if (!$data || !$data['data']['token']) {
			echo json_encode(array(	'result'=>$this->returnError,
								'resultmsg'=>'获取token失败'
						));
			exit;
		}

		$url 	=	'http://lshapi.scintakes.com/partner/receive';
		$post 	=	array(
				'appid'		=>	C('SHHC_APPID'),
				'token'		=>	$data['data']['token'],
				'age'  		=>	I('post.age','','intval'),
				'gender'  	=> 	I('post.gender','','intval'),
				'height'  	=> 	I('post.height','','htmlspecialchars,trim'),
				'weight'  	=> 	I('post.weight','','htmlspecialchars,trim'),
				'valr10'  	=> 	I('post.valr10','','htmlspecialchars,trim'),
				'valr11'  	=> 	I('post.valr11','','htmlspecialchars,trim'),
				'valr12'  	=> 	I('post.valr12','','htmlspecialchars,trim'),
				'valr13'  	=> 	I('post.valr13','','htmlspecialchars,trim'),
				'valr14'  	=> 	I('post.valr14','','htmlspecialchars,trim'),
				'valr20'  	=> 	I('post.valr20','','htmlspecialchars,trim'),
				'valr21'  	=> 	I('post.valr21','','htmlspecialchars,trim'),
				'valr22'  	=> 	I('post.valr22','','htmlspecialchars,trim'),
				'valr23'  	=> 	I('post.valr23','','htmlspecialchars,trim'),
				'valr24'  	=> 	I('post.valr24','','htmlspecialchars,trim')
			);

		$result 	=	$this->https_request($url,$post);

		$result 	=	$result ? json_decode($result,true) : array();
		echo json_encode($result);
		exit;
		
		$result 	=	$result && isset($result['data']) ? $result['data'] : array();

		echo json_encode($result);
		exit;
	}

	function https_request($url,$data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

	    curl_setopt($ch, CURLOPT_TIMEOUT,10);

	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}

}