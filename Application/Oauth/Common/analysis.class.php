<?php
namespace Oauth\Common;

/**
 * 获取检测结果
 *
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
class analysis {

	private $url 		=	"http://api.yicheng120.com/kbox/mindex";
	private $client_id 	= 	'user1';
	private $secret 	= 	'7MxCxF';
	private $token 		=	'';

	public function __construct( $classname = __CLASS__ ){
		
	}

	/**
	 * 获取分析结果
	 * 
	 * @param  array  $data 检测数据
	 * @param  string $name 检测项目
	 * @return array  返回检测结果
	 */
	public function result( $data = array(),$name='' ) {
		if ( !$data ) {
			return false;
		}

		switch ( $name ) {
			//每个分项正常的报告
			case 'resources':
				return $this->totalreport($data);
				break;
			//体温
			case 'tm' :
				return $this->tm($data);
				break;
		   	//血脂 
			case 'bf' :
				return $this->bloodfat($data);
				break;
		   	//血压		
			case 'bp' :
				return $this->bloodp($data);
				break;
		  	//心电		
			case 'el' :
				return $this->electrocardio($data);
				break;
		 	//人体		
			case 'we' :
				return $this->humanbody($data);
				break;
	    	//血氧			
			case 'ox' :
				return $this->oxygen($data);
				break;

	    	//尿常规		
			case 'ur' :
				return $this->urine($data);
				break;

			// 血糖
			case 'gl':
				return $this->bbsugar($data);
				break;
			// 血酮
			case 'bk':
				return $this->bloodketone($data);
				break;

			// 肾功
			case 're':
				return $this->renal($data);
				break;
			// 尿微量白蛋白
			case 'um':
				return $this->umprotein($data);
				break;

			// 血糖
			default:
				return $this->bbsugar($data);
				break;
		}
	}


	/**
	 * 血糖分析结果
	 *
	 * 
	 * @param  array  $data 血糖检测数据信息
	 * @return array 	返回血糖测量值分析结果
	 */
	public function bbsugar( $data=array() ){
		if( !$data || !isset($data['bloodsugar']) || !$data['bloodsugar'] ){
			return false;
		}

		$postdata 	= 	array();
		//	血糖测量值
		$postdata['data']    = 	$data['bloodsugar'];
		
		// 进食状态
		$postdata['times']   = 	$data['attr'];

		// 病史
		$postdata['history'] =	$data['history'] ? $data['history'] : 2;
		$postdata['token']   = 	$this->getkboxtoken();

		$result 	= 	self::getkboxdata(self::geturl('bsugar'),$postdata);
		return $result;
	}

	
	/**
	 * 血脂分析结果
	 *
	 * 
	 * @param  array  $data 血脂检测数据信息
	 * @return array 	返回血脂测量值分析结果
	 */
	public function bloodfat( $data=array() ){
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data1'] = $data['tc'];//总胆固醇
		$postdata['data2'] = $data['tg'];//甘油三脂
		$postdata['data3'] = $data['ltc'];//低密度脂蛋白
		$postdata['data4'] = $data['htc'];//高密度脂蛋白
		$postdata['token'] = $this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('bloodfat'),$postdata);
		return $result;
	}


	/**
	 * 分析结果 -- 血压
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 血压
	 * @return array 	返回测量值分析结果
	 */
	public function bloodp( $data=array() ){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['hdata'] = $data['hboodp'];//高压
		$postdata['ldata'] = $data['lboodp'];//低压
		$postdata['token'] = $this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('bloodp'),$postdata);
		return $result;
	}



	/**
	 * 分析结果 -- 血氧
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 血氧
	 * @return array 	返回测量值分析结果
	 */
	public function oxygen( $data=array()){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data']  	= 	$data['saturation'];	//血氧饱和度
		$postdata['data2']	=	$data['pr']; //脉率
		$postdata['token'] 	= 	$this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('oxygen'),$postdata);
		return $result;
	}


	/**
	 * 分析结果 -- 心电
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 心电
	 * @return array 	返回测量值分析结果
	 */
	public function electrocardio( $data=array() ){
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data']  = $data['bpm'];//心率
		$postdata['token'] = $this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('el'),$postdata);
		return $result;
	}


	/**
	 * 分析结果 -- 体成分
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 体成分
	 * @return array 	返回测量值分析结果
	 */
	public function humanbody( $data=array() ){
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data1'] = $data['weight'];//体重
		$postdata['data2'] = $data['bmi'];//BMI值
		$postdata['data3'] = $data['bf'];//体脂百分比
		$postdata['data4'] = $data['fatweight'];//去脂体重
		$postdata['data5'] = $data['protein'];//内脏脂肪指数
		$postdata['data6'] = $data['watercontentrate'];//身体总水分
		$postdata['data7'] = $data['muscle'];//肌肉量
		$postdata['data8'] = $data['mineralsalts'];//骨量
		$postdata['data9'] = $data['fat'];//基础代谢率
		$postdata['sex']   = $data['sex'];   //性别

		$postdata['token'] = $this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('humanbody'),$postdata);
		
		
		// 隐藏掉去脂体重 BEGIN
		if (isset($result['4'])) {
			unset($result['4']);
		}
		// 隐藏掉去脂体重 END

		return $result;

	}


	/**
	 * 分析结果 -- 尿常规
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 尿常规
	 * @return array 	返回测量值分析结果
	 */
	public function urine( $data=array() ){
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data1']  = $data['nitrite'];			//亚硝酸盐
		$postdata['data2'] 	= $data['urobilinogen'];   	//尿胆原
		$postdata['data3']  = $data['whitecells'];   	//白细胞
		$postdata['data4']  = $data['redcells'];   		//红细胞
		$postdata['data5']  = $data['urineprotein'];   	//尿蛋白
		$postdata['data6']  = $data['ph'];   			//酸碱度
		$postdata['data7']  = $data['urine'];   		//尿比重
		$postdata['data8']  = $data['urineketone'];   	//尿酮
		$postdata['data9']  = $data['bili'];   			//胆红素
		$postdata['data10'] = $data['sugar'];   		//尿糖
		$postdata['data11'] = $data['vc'];   			//维生素c
		$postdata['token']  = $this->getkboxtoken();

		$result = $this->getkboxdata($this->geturl('urine'),$postdata);

		return $result;
	}

	/**
	 * 分析结果 -- 恒温
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 恒温
	 * @return array 	返回测量值分析结果
	 */
	public function tm( $data=array() ) {
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data']  = 	$data['tmv'];//体温
		$postdata['token'] = 	$this->getkboxtoken();

		$result = $this->getkboxdata(self::geturl('tm'),$postdata);
		return $result;
	}


	/**
	 * 分析结果 -- 血酮
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 血酮
	 * @return array 	返回测量值分析结果
	 */
	public function bloodketone( $data=array() ) {
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data']  = 	$data['bk'];//血酮
		$postdata['token'] = 	$this->getkboxtoken();

		$result = $this->getkboxdata(self::geturl('bloodketone'),$postdata);

		return $result;
	}


	/**
	 * 分析结果 -- 肾功
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 肾功
	 * @return array 	返回测量值分析结果
	 */
	public function renal( $data=array() ){
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data1'] = $data['suricacid'];	// 血尿酸
		$postdata['sex']   = $data['sex'];   //性别

		$postdata['token'] = $this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('renal'),$postdata);
		
		return $result;
	}


	/**
	 * 分析结果 -- 尿微量白蛋白
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 尿微量白蛋白
	 * @return array 	返回测量值分析结果
	 */
	public function umprotein( $data=array() ) {
		if( !$data ){
			return false;
		}
		$postdata = array();
		$postdata['data']  = 	$data['um'];//血酮
		$postdata['token'] = 	$this->getkboxtoken();

		$result = $this->getkboxdata(self::geturl('umprotein'),$postdata);

		return $result;
	}

	/**
	 * 康宝8项整体对应每个正常项的结果分析
	 *
	 * 
	 * @param  array  $data 检测数据信息 -- 恒温
	 * @return array 	返回测量值分析结果
	 */
	public function totalreport( $data = array() ){
		if( !$data ){
			return '';
		}
		$postdata = array();
		$postdata['types']  = $data['all'];//设备类型 设备类型 (一次获取多个，中间用英文逗号拆分) 1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温
		$postdata['token'] = $this->getkboxtoken();
		$result = $this->getkboxdata(self::geturl('resources'),$postdata,1);
		return $result;
	}


	/**
	 * 康宝检测设备体检范围
	 *
	 * 
	 * @param  array  	$data 检测设备
	 *                       血糖：gl 血氧：ox 额温：tm 体成分：we
	 *                       血压：bp 血脂：bf 尿常规：ur
	 *                       心电：el
	 * @return array 	返回测量值分析结果
	 */
	public function rangedata( $data = array() ){
		
		$postdata = array();
		$postdata['types']  = $data['types'];
		$postdata['token'] 	= $this->getkboxtoken();
	
		$result = Spost( self::geturl('rangedatas') , $postdata );
		$result = json_decode($result,true);

		return $result;
	}

	
	
	/**
	 * 获取服务器的url
	 * @param  string $name API接口地址
	 * @return 
	 */
	private function geturl($name=''){
		if( $name ){
			return $this->url.'/'.$name;
		}else{
			return '';
		}
	}
	
	/**
	 * 调用box接口，获取token值
	 * 
	 * @return [type]            [description]
	 */
	private function getkboxtoken(  ){
		$data 		= array(
			'username'	=>	$this->client_id,
			'encrypt'	=>	$this->secret
		);

		$tokdata 	= Spost($this->url,$data);
		$tokdata 	= json_decode($tokdata,true);
		return $tokdata['token'] ? $tokdata['token'] :'';
	}

	/**
	 * 调用box接口，获取分析数据
	 * @param  string  $url  kbox地址
	 * @param  array   $data post参数
	 * @param  integer $type 为0时,单个分项报告,1 时,整体报告 
	 * @return array
	 */
	private function getkboxdata($url='',$data =array(),$type=0){
		$data = Spost( $url , $data );
		$data = json_decode($data,true);
		$data = self::getdata($data,$type);
		return $data;
	}


	/**
	 * 调用box接口，获取分析数据,进行格式转换
	 * @param  array   $data 分析结果数据
	 * @param  integer $type 为0时,单个分项报告,1 时,整体报告 
	 * @return array
	 */
	private function getdata($data = array(),$type=0){
		if ( !$data ) {
			return false;
		}
		if( empty($type) ){
			foreach( $data as $k => $v ){
				if( is_array($v) ){
					$data[$k]['clinical'] 	= nl2br(htmlspecialchars_decode($v['clinical']));
					$data[$k]['result'] 	= nl2br(htmlspecialchars_decode($v['result']));
					$data[$k]['danger'] 	= nl2br(htmlspecialchars_decode($v['danger']));
				}else{
					$data['clinical'] 		= nl2br(htmlspecialchars_decode($data['clinical']));
					$data['result'] 		= nl2br(htmlspecialchars_decode($data['result']));
					$data['danger'] 		= nl2br(htmlspecialchars_decode($data['danger']));
					return $data;
				}
			}
		   return $data;	
		}else{
			$data	=	self::formatreport( $data );
			return $data;
		}
	}
	
	/**
	 * 整体报告格式化
	 * 
	 * @param array $data 整体报告
	 * @return string
	 */
	private function formatreport($data=array()){
		if(!$data){
			return '';
		}
		foreach($data as $k=>$v){
			$data[$k]	=	nl2br(htmlspecialchars_decode($v));
		}
		return $data;
	}

}
