<?php
namespace Data2\Controller;
use Think\Controller;
class IndexController extends Controller {
	private $returnOK		= 'resultOK';
	private $returnError	= 'resultError';
	private $resultOKmsg	= '获取Token成功!';
	private $resultErrormsg	= '获取Token失败!请检查服务地址、访问帐号和访问口令是否正确!';
	private $tokenErrormsg	= 'Token无效，请重新获取';
	private $tokenlifetime	= '1800';	//TOKEN生命值 ，单位秒；
	

	//不需要验证token的action
	private  $nologin = array('index');
	

 	public function __construct(){
		parent::__construct();
		$this->api_clients	= M('api_clients');

		$this->checktoken();
	}

	/**
	* 获取token
	*	提交方式 POST
	*
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		client_id 服务器分配的 client_id 信息
	*				secret    服务器分配的 密钥 信息
	* @return		json 类型数据
	*/
	public function index(){

		$client_id	=	I('post.client_id','','htmlspecialchars,trim');
		$secret		=	I('post.secret','','htmlspecialchars,trim');
		

		if (!$client_id || !$secret) {
			echo json_encode(array(	'status'=>$this->returnError,
									'resultmsg'=>$this->resultErrormsg
							));
			exit;
		}

		$where		=	array();
		$where['client_id']		=	$client_id;
		$where['client_secret']	=	$secret;
		$clientinfo	=	$this->api_clients->where($where)->getField('client_id');

		if (!empty($clientinfo)) {
			$this->set_token();

			echo json_encode(array(	'status'=>$this->returnOK,
									'resultmsg'=>$this->resultOKmsg,
									'token'=>$this->get_token(),
									'authid'=>$client_id,
									'expire'=>$this->tokenlifetime,
							));
			exit;
		}
		echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>$this->resultErrormsg
						));
		exit;
		
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
	*					1:空腹 、2:餐前 、3:餐后两个小时、4:睡前、5:随机
	*				history 糖尿病史(默认为无)
	*					4:无、1:1型糖尿病、2:2型糖尿病、3:3妊娠糖尿病
	* @return		
	*/
	public function bsugar(){
		$data 		=	I('data','','htmlspecialchars,trim');
		$times		=	I('times','1','htmlspecialchars,trim');
		$history	=	I('history','4','htmlspecialchars,trim');
		
		$data 		=	$data * 100;

		if ( !$data || !is_numeric($data) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
		
		$where	=	array();
	    $where['max'] 		= 	array('egt',$data);
	    $where['min'] 		=	array('lt',$data);
	    $where['first'] 	= 	array('eq',$times);
	    $where['second'] 	= 	array('eq',$history);

	    $result = M('bsugar')->where($where)->find();

	    $this->returndata($result,'血糖分析管理');
	}

	/**
	* BMI指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		height 身高（M）	
	*				weight 体重（Kg）
	*				condition 糖尿病史(默认为无)
	*					0：无 1：有
	* @return		json 格式
	*/
	public function bmi(){
		$weight		=	I('weight','','htmlspecialchars,trim');
		$height		=	I('height','','htmlspecialchars,trim');
		$condition	=	I('condition','0','intval');
		
		if ( !$weight || !is_numeric($weight) || !$height || !is_numeric($height) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		$jg		=	$weight/($height*$height);
		$res 	= 	round( $jg, 2)*100;
		$arr['max_bmi']		=	array('EGT',$res);
		$arr['min_bmi'] 	=	array('LT' , $res);
		$arr['condition'] 	=	array('EQ',$condition);
		$result = M('bmi')->where($arr)->find();

		$this->returndata($result,'BMI指数分析管理');
	}

	/**
	* 骨含量指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（Kg）	
	*				weight 体重（Kg）
	*			 	sex 性别（默认 女）
	*					0：女 1：男
	* @return		json 格式
	*/
	public function bone(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$weight		=	I('weight','','htmlspecialchars,trim');
		$sex		=	I('sex','0','intval');
		
		$zhibiao	=	floatval($zhibiao) * 100;
		$weight 	= 	floatval($weight) * 100;

		if ( !$weight || !is_numeric($weight) || !$zhibiao || !is_numeric($zhibiao) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		$arr['z_min'] 	= 	array('LT',$zhibiao);
		$arr['z_max'] 	= 	array('EGT',$zhibiao);
		$arr['sex'] 	= 	array('EQ',$sex);
		$arr['s_max'] 	= 	array('EGT',$weight);
		$arr['s_min'] 	= 	array('LT',$weight);

		$result 	=  M('bone')->where($arr)->find();

		$this->returndata($result,'骨含量分析管理');
	}
	

	/**
	* 肌肉含量指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（%）	
	*				sex 性别（默认 女）
	*					0：女 1：男
	* @return		json 格式
	*/
	public function muscle(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$sex		=	I('sex','0','intval');
		
		if ( !$zhibiao || !is_numeric($zhibiao) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    $data['max']	= 	array('EGT',$zhibiao);
	    $data['min'] 	= 	array('LT',$zhibiao);
	    $data['sex'] 	= 	array('EQ',$sex);

		$result 	=  M('muscle')->where($arr)->find();

		$this->returndata($result,'肌肉含量分析管理');
	}

	/**
	* 内脏脂肪指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值
	* @return		json 格式
	*/
	public function viscera(){
		$zhibiao	=	I('zhibiao','','intval');
		$zhibiao 	= 	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		$data['max'] = array('EGT',$zhibiao);
		$data['min'] = array('ELT',$zhibiao);

		$result 	=  M('viscera')->where($data)->find();

		$this->returndata($result,'内脏脂肪分析管理');
	}


	/**
	* 水分含量指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		water 检测值（%） 
	*         		age 年龄
	*         		sex 性别（默认 女）
	*					0：女 1：男
	* @return		json 格式
	*/
	public function water(){
		$water	=	I('water','','htmlspecialchars,trim');
		$age	=	I('age','','intval');
		$sex	=	I('sex','0','intval');

		$water 	= 	$water * 100;

		if ( !$water || !is_numeric($water) || !$age || !is_numeric($age)) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

   		$where 	=	array();
		$where['sex'] 	= array('EQ',$sex);
	    $where['max'] 	= array('EGT',$water);
	    $where['min'] 	= array('ELT',$water);
	    $where['age_max'] = array('EGT',$age);
	    $where['age_min'] = array('ELT',$age);

		$result 	=  M('water')->where($where)->find();

		$this->returndata($result,'水分含量分析管理');
	}


	/**
	* 基础代谢率指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ Kcal/d ） 
	*               weight	体重（Kg）
	*         		age 年龄
	*         		sex 性别（默认 女）
	*					0：女 1：男
	* @return		json 格式
	*/
	public function base(){
		$zhibiao	=	I('zhibiao','','intval');
		$weight		=	I('weight','','intval');
		$age		=	I('age','','intval');
		$sex		=	I('sex','0','intval');

		if( $age < 4 ){
	    	if($sex == 0){
	    		$res = $weight * 61.0 - 51;
	    	}else{
	    		$res = $weight * 60.9 - 54;
	    	}
	    }else if ( $age < 11){
	    	if($sex == 0){
	    		$res = $weight * 22.5 + 499;
	    	}else{
	    		$res = $weight * 22.7 + 495;
	    	}
	    }else if ( $age < 18){
	    	if($sex == 0){
	    		$res = $weight * 12.2 + 746;
	    	}else{
	    		$res = $weight * 17.5 + 651;
	    	}
	    }else if ( $age < 31 ){
	    	if($sex == 0){
	    		$res = $weight * 14.7 + 496;
	    	}else{
	    		$res = $weight * 15.3 + 679;
	    	}
	    }else if ( $age < 61 ){
	    	if($sex == 0){
	    		$res = $weight * 8.7 + 829;
	    	}else{
	    		$res = $weight * 11.6 + 879;
	    	}
	    }else{
	    	if( $sex == 0 ){
	    		$res = $weight * 10.5 + 596;
	    	}else{
	    		$res = $weight * 13.5 + 487;
	    	}
	    }
	    $res 	=	$zhibiao - $res; 
	    $where	=	array();
	    $where['max'] 	= 	array('EGT',$res);
	    $where['min'] 	= 	array('LT',$res);
	    $where['sex'] 	= 	array('EQ',$sex);
	    $where['mage'] 	= 	array('EGT',$age);
	    $where['nage'] 	= 	array('ELT',$age);

		$result 	=  M('base')->where($where)->find();

		$this->returndata($result,'基础代谢率分析管理');
	}


	/**
	* 体脂率指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		bmi BMI指数
	*         		age 年龄
	*         		sex 性别（默认 女）
	*					0：女 1：男
	* @return		json 格式
	*/
	public function body_fat(){
		$bmi	=	I('bmi','','htmlspecialchars,trim');
		$age	=	I('age','','intval');
		$sex	=	I('sex','0','intval');

		if ( !$bmi || !is_numeric($bmi) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
	    if(	$age <=30 ){
	        $tmp = 0;
	    }else{
	        $tmp = 1;
	    }
	    $res 	=	1.2 * $bmi + 0.23 * $age - 5.4 - 10.8 * $sex;
	    $where['age']	= array('eq',$tmp);
	    $where['sex'] 	= array('eq',$sex);
	    $where['max'] 	= array('egt',$res);
	    $where['min'] 	= array('lt',$res);

		$result 	=  M('body_fat')->where($where)->find();

		$this->returndata($result,'体脂率分析管理');
	}


	/**
	* 体重指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		height 身高（CM）	
	*				weight 体重（Kg）
	*         		sex 性别（默认 女）
	*					0：女 1：男
	* @return		json 格式
	*/
	public function weight(){
		$height	=	I('height','','htmlspecialchars,trim');
		$weight	=	I('weight','','htmlspecialchars,trim');
		$sex	=	I('sex','0','intval');

		if ( !$height || !is_numeric($height) || !$weight || !is_numeric($weight) ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    if($sex == 1){
	        $res = $height - 105;
	    }else{
	        $res = $height - 100;
	    }
	    $res = ( $weight - $res )/ $res * 100;

	    $where['hhight'] 	= array('egt',$height);
	    $where['lhight'] 	= array('lt',$height);
	    $where['sex'] 		= array('eq',$sex);
	    $where['max'] 		= array('egt',$res);
	    $where['min']		= array('lt',$res);

		$result 	=  M('weight')->where($where)->find();

		$this->returndata($result,'体重分析管理');
	}

	/**
	* 尿白细胞指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 定性 （默认为阴性）
	*         			0：阴性 1：阳性
	* @return		json 格式
	*/
	public function leu(){
		$zhibiao	=	I('zhibiao','0','intval');
		
	    $where['leu'] 		= array('eq',$zhibiao);

		$result 	=  M('leu')->where($where)->find();

		$this->returndata($result,'尿白细胞分析管理');
	}


	/**
	* 尿比重指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值
	*         		history 糖尿病病史（默认为无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function sg(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$history	=	I('history','0','intval');

		$zhibiao	=	$zhibiao * 100;
    	if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    $where['max'] 	= array('egt',$zhibiao);
	    $where['min'] 	= array('lt',$zhibiao);
	    $where['tn'] 	= array('eq',$history);
	 
		$result	=  M('sg')->where($where)->find();

		$this->returndata($result,'尿比重分析管理');
	}


	/**
	* 尿蛋白指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 默认为阴性(-) ）
	*         			0:阴性(-) 1:阳性(+) 2：阳性(++) 3：阳性(+++) 4：阳性(++++)
	*         		history 糖尿病病史（默认为无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function prc(){
		$zhibiao	=	I('zhibiao','0','intval');
		$history	=	I('history','0','intval');

	    $where['zb'] 	= array('eq',$zhibiao);
	    $where['tn'] 	= array('eq',$history);
	   
		$result	=  M('pro')->where($where)->find();
	
		$this->returndata($result,'尿蛋白分析管理');
	}


	/**
	* 尿胆原指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ μmol/L ）
	*         		age 年龄
	*         		sex 性别 （默认为女）
	*         			0：女 1：男
	* @return		json 格式
	*/
	public function ubg(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','','intval');
		$sex		=	I('sex','0','intval');

		$zhibiao 	= 	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		if( $age < 18 ){
	        $agea = 0;
	    }else{
	        $agea = 1;
	    }

	    $where['max']	= 	array('egt',$zhibiao);
	    $where['min'] 	= 	array('lt',$zhibiao);
	    $where['sex']	= 	array('eq',$sex);
	    $where['age'] 	= 	array('eq',$agea);
	  	
		$result	=  M('ubg')->where($where)->find();
		
		$this->returndata($result,'尿胆原分析管理');
	}


	/**
	* 尿胆红素指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 默认为阴性 ）
	*         			0:阴性 1:阳性
	* @return		json 格式
	*/
	public function bil(){
		$zhibiao	=	I('zhibiao','0','intval');

	    $where['jc'] 	= array('eq',$zhibiao);
	   
		$result	=  M('pro')->where($where)->find();
	
		$this->returndata($result,'尿胆红素分析管理');
	}


	/**
	* 尿糖指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 默认为阴性(-) ）
	*         			0:阴性(-) 1:阳性(+) 2：阳性(++) 3：阳性(+++) 4：阳性(++++)
	*         		history 糖尿病病史（默认为无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function glu(){
		$zhibiao	=	I('zhibiao','0','intval');
		$history	=	I('history','0','intval');

	    $where['dx'] 	= array('eq',$zhibiao);
	    $where['tn'] 	= array('eq',$history);
	   	
		$result	=  M('glu')->where($where)->find();
		$this->returndata($result,'尿糖分析管理');
	}


	/**
	* 尿酮体指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 默认为阴性 ）
	*         			0:阴性 1:阳性
	*         		history 糖尿病病史（默认为无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function ket(){
		$zhibiao	=	I('zhibiao','0','intval');
		$history	=	I('history','0','intval');

	    $where['dx'] 	= array('eq',$zhibiao);
	    $where['tn'] 	= array('eq',$history);

		$result	=  M('ket')->where($where)->find();
	
		$this->returndata($result,'尿酮体分析管理');
	}


	/**
	* 尿酸碱度指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值
	*         		history 糖尿病病史（默认为无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function ph(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$history	=	I('history','0','intval');

		$zhibiao 	= 	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    $where['max'] 	= array('egt',$zhibiao);
	    $where['min'] 	= array('lt',$zhibiao);
	    $where['tn'] 	= array('eq',$history);

		$result	=  M('ph')->where($where)->find();
	
		$this->returndata($result,'尿酸碱度分析管理');
	}


	/**
	* 尿亚硝酸盐指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 默认为阴性 ）
	*         			0:阴性 1:阳性
	* @return		json 格式
	*/
	public function nit(){
		$zhibiao	=	I('zhibiao','0','intval');

	    $where['dx'] 	= array('eq',$zhibiao);

		$result	=  M('nit')->where($where)->find();
	
		$this->returndata($result,'尿亚硝酸盐分析管理');
	}

	/**
	* 尿维生素C指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 默认为阴性 ）
	*         			0:阴性 1:阳性
	* @return		json 格式
	*/
	public function vitc(){
		$zhibiao	=	I('zhibiao','0','intval');

	    $where['dx'] 	= array('eq',$zhibiao);

		$result	=  M('vitc')->where($where)->find();
	
		$this->returndata($result,'尿维生素C分析管理');
	}


	/**
	* 尿微量白蛋白指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ μmol/L ）
	*               history 糖尿病病史（默认为无）
	*               	0：无 1：有 2：糖尿病和并发症 3：高血压
	* @return		json 格式
	*/
	public function malb(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$history	=	I('history','0','intval');

		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
	    $where['max'] 	= array('egt',$zhibiao);
	    $where['min'] 	= array('elt',$zhibiao);
	    $where['tn'] 	= array('eq',$history);


		$result	=  M('malb')->where($where)->find();
	
		$this->returndata($result,'尿微量白蛋白分析管理');
	}


	/**
	* 心率指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ 次/分 ）
	*               age 年龄
	*               sex 性别（默认为女）
	*               	0：女 1：男
	* @return		json 格式
	*/
	public function hr(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','','intval');
		$sex		=	I('sex','0','intval');

		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		if( $age < 18 ){
	    	echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'18岁以上才能分析管理！'
						));
			exit;
	    }else if( $age <= 60 ){
	        $age = '0';
	    }else{
	        $age = '1';
	    }

		$where['max'] = array('egt',$zhibiao);
		$where['min'] = array('lt',$zhibiao);
    	$where['age'] = array('eq',$age);
    	$where['sex'] = array('eq',$sex);

		$result	=  M('hr')->where($where)->find();
	
		$this->returndata($result,'心率分析管理');
	}


	/**
	* 血氧饱和度指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（ μmol/L ）
	*               history 糖尿病病史（默认为无）
	*               	0：无 1：有 
	* @return		json 格式
	*/
	public function spo2(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$history	=	I('history','0','intval');

		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
	    $where['max'] 		= 	array('egt',$zhibiao);
	    $where['min'] 		= 	array('lt',$zhibiao);
	    $where['condition']	= 	array('eq',$history);

		$result	=  M('spo2')->where($where)->find();
	
		$this->returndata($result,'血氧饱和度分析管理');
	}


	/**
	* 血酮体指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 指标
	*               history 糖尿病病史（默认为无）
	*               	0：无 1：有 2：妊娠糖尿病 3：采用生酮疗法的癫痫患者
	* @return		json 格式
	*/
	public function xtt(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$history	=	I('history','0','intval');
		$zhibiao	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}
		
	    $where['max']	= 	array('egt',$zhibiao);
	    $where['min']	= 	array('lt',$zhibiao);
	    $where['hb']	= 	array('eq',$history);

		$result	=  M('xtt')->where($where)->find();
	
		$this->returndata($result,'血酮体分析管理');
	}

	
	/**
	* 血尿酸指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（μmol/L）
	*               age 年龄
	*               sex 性别（默认为女）
	*               	0：女 1：男
	* @return		json 格式
	*/
	public function ua(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$sex		=	I('sex','0','intval');
		
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		if( $age <= 60 ){
	        $age = 0;
	    }else{
	        $age = 1;
	    }

	    $where['max']	= 	array('egt',$zhibiao);
	    $where['min']	= 	array('lt',$zhibiao);
	    $where['age']	= 	array('eq',$age);
	    $where['sex']	= 	array('eq',$sex);

		$result	=  M('ua')->where($where)->find();
	
		$this->returndata($result,'血尿酸分析管理');
	}


	/**
	* 血清总胆固醇指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（mmol/L）
	*               age 年龄
	*               history 糖尿病病史 （默认为无）
	*               	4：无 1：有 2：糖尿病伴心血管疾病 3：心血管疾病或有心血管家族史者
	* @return		json 格式
	*/
	public function tc(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$history	=	I('history','4','intval');
		$zhibiao	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		if( $age < 18 ){
	        $age = 0;
	    }else{
	        $age = 1;
	    }

	    $where['max']	= 	array('egt',$zhibiao);
	    $where['min']	= 	array('lt',$zhibiao);
	    $where['age']	= 	array('eq',$age);
	    $where['tj']	= 	array('eq',$history);

		$result	=  M('tc')->where($where)->find();
	
		$this->returndata($result,'血清总胆固醇分析管理');
	}


	/**
	* 甘油三酯指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（mmol/L）
	*               age 年龄
	*               history 糖尿病病史 （默认为无）
	*               	4：无 1：有 2：糖尿病伴心血管疾病 3：心血管疾病或有心血管家族史者
	* @return		json 格式
	*/
	public function tg(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$history	=	I('history','4','intval');
		$zhibiao	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    $where['max']		= 	array('egt',$zhibiao);
	    $where['min']		= 	array('lt',$zhibiao);
	    $where['maxage']	= 	array('egt',$age);
	    $where['minage']	= 	array('lt',$age);
	    $where['dhistory']	= 	array('eq',$history);

		$result	=  M('tg')->where($where)->find();
	
		$this->returndata($result,'甘油三酯分析管理');
	}



	/**
	* 低密度脂蛋白胆固醇指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（mmol/L）
	*               age 年龄
	*               history 糖尿病病史 （默认为无）
	*               	4：无 1：有 2：糖尿病伴心血管疾病 3：心血管疾病或有心血管家族史者
	* @return		json 格式
	*/
	public function ldlc(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$history	=	I('history','4','intval');
		$zhibiao	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    $where['max']		= 	array('egt',$zhibiao);
	    $where['min']		= 	array('lt',$zhibiao);
	    $where['maxage']	= 	array('egt',$age);
	    $where['minage']	= 	array('lt',$age);
	    $where['dhistory']	= 	array('eq',$history);

		$result	=  M('ldlc')->where($where)->find();
	
		$this->returndata($result,'低密度脂蛋白胆固醇分析管理');
	}

	/**
	* 高密度脂蛋白胆固醇指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（mmol/L）
	*               age 年龄
	*               history 糖尿病病史 （默认为无）
	*               	4：无 1：有 2：糖尿病伴心血管疾病 3：心血管疾病或有心血管家族史者
	* @return		json 格式
	*/
	public function hdlc(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$history	=	I('history','4','intval');
		$zhibiao	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	    $where['max']		= 	array('egt',$zhibiao);
	    $where['min']		= 	array('lt',$zhibiao);
	    $where['maxage']	= 	array('egt',$age);
	    $where['minage']	= 	array('lt',$age);
	    $where['dhistory']	= 	array('eq',$history);

		$result	=  M('hdlc')->where($where)->find();
	
		$this->returndata($result,'高密度脂蛋白胆固醇分析管理');
	}


	/**
	* 血压指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		sbp 收缩压--高压（mmHg）
	*         		dbp 舒张压--低压（mmHg）
	*         		age 年龄
	*         		sex 性别（默认女）
	*         			0：女 1：男
	*         		history 糖尿病史（默认无）
	*         			4：无 1：有 2：糖尿病合并高血压 3：高血压
	* @return		json 格式
	*/
	public function bloodp(){
		$sbp		=	I('sbp','','htmlspecialchars,trim');
		$dbp		=	I('dbp','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$sex		=	I('sex','0','intval');
		$history	=	I('history','4','intval');


	 	$where['ms'] 	= array('egt',$sbp);
	    $where['ns'] 	= array('lt',$sbp);
	    $where['md'] 	= array('egt',$dbp);
	    $where['nd'] 	= array('lt',$dbp);
	    $where['maxage'] = array('egt',$age);
	    $where['minage'] = array('lt',$age);
	    $where['sex'] 	= array('eq',$sex);
	    $where['dhistory'] = array('eq',$history);

		$result	=  M('bloodp')->where($where)->find();
	
		$this->returndata($result,'血压分析管理');
	}



	/**
	* 糖化血红蛋白指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（%）
	*         		history 糖尿病史（默认无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function hbac(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$history	=	I('history','0','intval');
		$zhibiao	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	 	$where['max'] 	= array('egt',$zhibiao);
	    $where['min'] 	= array('lt',$zhibiao);
	    $where['diseases'] = array('eq',$history);

		$result	=  M('hbac')->where($where)->find();
	
		$this->returndata($result,'糖化血红蛋白分析管理');
	}


	/**
	* 血肌酐指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（μmol/L）
	*         		sex 性别（默认女）
	*         			0：女 1：男
	*         		history 糖尿病史（默认无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function scr(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$sex		=	I('sex','0','intval');
		$history	=	I('history','0','intval');
		
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

	 	$where['max'] 		= array('egt',$zhibiao);
	    $where['min'] 		= array('lt',$zhibiao);
	    $where['dhistory'] 	= array('eq',$history);
	    $where['sex'] 		= array('eq',$sex);

		$result	=  M('scr')->where($where)->find();
	
		$this->returndata($result,'血肌酐分析管理');
	}


	/**
	* 血尿素氮指标分析接口
	* 	提交方式：post
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		zhibiao 检测值（mmol/L）
	*         		age 年龄
	*         		history 糖尿病史（默认无）
	*         			0：无 1：有
	* @return		json 格式
	*/
	public function bun(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');
		$age		=	I('age','0','intval');
		$history	=	I('history','0','intval');
		
		$zhibiao 	=	$zhibiao * 100;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'参数有误！'
						));
			exit;
		}

		if( $age < 18 ){
        	$age = 0;
	    }else{
	        $age = 1;
	    }

	 	$where['max'] 		= array('egt',$zhibiao);
	    $where['min'] 		= array('lt',$zhibiao);
	    $where['dhistory'] 	= array('eq',$history);
	    $where['age'] 		= array('eq',$age);

		$result	=  M('bun')->where($where)->find();
	
		$this->returndata($result,'血尿素氮分析管理');
	}


	
	/**
	 * 体温(恒温)指标分析接口
	 * 	提交方式：post
	 *
	 * @author		wangyangyang
	 * @copyright	wangyang8839@163.com
	 * @version		1.0
	 * @param		zhibiao 检测值（℃）体温值
	 * @return		json 格式
	 */
	public function temperature(){
		$zhibiao	=	I('zhibiao','','htmlspecialchars,trim');	
		//echo $zhibiao;
		if ( !$zhibiao || !is_numeric($zhibiao)  ) {
			echo json_encode(array(	'status'=>$this->returnError,
					'resultmsg'=>'参数有误！'
			));
			exit;
		}
		$where['max'] 		= 	array('egt',$zhibiao);
	    $where['min'] 		=	array('lt',$zhibiao);	
		$result	=  M('temperature')->where($where)->find();
		$this->returndata($result,'恒温分析管理','meaning');
	}
	
	


 	/**
	* 返回结果
	* 
	* @author		wangyangyang
	* @copyright	wangyang8839@163.com
	* @version		1.0
	* @param		$result 数据库查询出来的结果
	*         		$title  检测类型
	*         		$fields 自定义返回字段
	* @return		json 格式
	*/
	private function returndata( $result , $title , $fields = '' ){
		if( empty($result) ){
	    	echo json_encode(array(	'status'=>$this->returnError,
								'resultmsg'=>'未查到相应的结果！'
						));
			exit;
	    }

	    $field 	=	$fields ? $fields :  'result';

	   	echo json_encode(array(	'status'=>$this->returnOK,
	   							'title' =>$title,
								'result'=>$result[$field]
						));
		exit;
	}
	

	private function set_token() {
		$token	= md5(microtime(true));
		S('token',$token,$this->tokenlifetime);
	}

	private function get_token() {
		return S('token');
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

}