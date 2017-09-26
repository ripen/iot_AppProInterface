<?php
/**
 * @author tangchengqi
 *康宝8项检测结果输出
 */
namespace Common\Common;
use Think\Controller;
class kangbaoresult extends Controller{
	private $url="http://api.yicheng120.com/kbox/mindex";
	private $client_id = 'user1';
	private $secret = '7MxCxF';
	private $token ='';
	public function __construct($classname = __CLASS__){
		parent::__construct();
	}
	public  function result($data = array(),$name='') {
		switch ($name) {
			//每个分项正常的报告
			case 'resources':
				return $this->totalreport($data,$name);
				break;
			//体温
			case 'tm' :
				return $this->tm($data,$name);
				break;
		   //血脂 
			case 'bloodfat' :
				return $this->bloodfat($data,$name);
				break;
		   //血压		
			case 'bloodp' :
				return $this->bloodp($data,$name);
				break;
		  //心电		
			case 'electrocardio' :
				return $this->electrocardio($data,$name);
				break;
		 //人体		
			case 'humanbody' :
				return $this->humanbody($data,$name);
				break;
	    //血氧			
			case 'oxygen' :
				return $this->oxygen($data,$name);
				break;
	    //尿11项			
			case 'urine' :
				return $this->urine($data,$name);
				break;
		// 血糖
			default:
				return $this->bbsugar($data);
				break;
		}
	}
	/**
	 * 康宝8项整体对应每个正常项的结果分析
	 * @param unknown $data
	 * @param string $name
	 * @return string|Ambigous <string, unknown>  */
	public function totalreport($data = array(), $name='resources'){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['types']  = $data['all'];//设备类型 设备类型 (一次获取多个，中间用英文逗号拆分) 1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata,1);
		return $result;
	}
	
	
	
	/**
	 * 本次恒温报告
	 * @param unknown $data  */
	public function tm($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data']  = $data['tmv'];//体温
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata);
		return $result;
	}
	/**
	 * 本次血脂报告
	 * @param unknown $data  */
	public function bloodfat($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data1'] = $data['tc'];//总胆固醇
		$postdata['data2'] = $data['tg'];//甘油三脂
		$postdata['data3'] = $data['ltc'];//低密度脂蛋白
		$postdata['data4'] = $data['htc'];//高密度脂蛋白
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata);
		return $result;
	}
	/**
	 * 本次血压报告
	 * @param unknown $data  */
	public function bloodp($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['hdata'] = $data['hboodp'];//高压
		$postdata['ldata'] = $data['lboodp'];//低压
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata);
		return $result;
	}
	/**
	 * 本次心电报告
	 * @param unknown $data  */
	public function electrocardio($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data']  = $data['bpm'];//心率
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata);
		return $result;
	}
	/**
	 * 本次人体报告
	 * @param unknown $data  */
	public function humanbody($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data2'] = $data['bmi'];//BMI值
		$postdata['data3'] = $data['bf'];//体脂百分比
		$postdata['data4'] = $data['fatweight'];//去脂体重
		$postdata['data9'] = $data['fat'];//基础代谢率
		$postdata['data7'] = $data['muscle'];//肌肉量
		$postdata['data5'] = $data['protein'];//内脏脂肪指数
		$postdata['data6'] = $data['water'];//身体总水分
		$postdata['data8'] = $data['mineralsalts'];//骨量
		$postdata['data1'] = $data['weight'];//体重
		$postdata['sex']   = $data['sex'];   //性别
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata);
		return $result;
	}
	/**
	 * 本次血氧报告
	 * @param unknown $data  */
	public function oxygen($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data']  = $data['saturation'];//血氧饱和度
		$postdata['token'] = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl($name),$postdata);
		return $result;
	}
	
	/**
	 * 本次尿11项报告
	 * @param unknown $data  */
	public function urine($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data2 '] = $data['urobilinogen'];   //尿胆原
		$postdata['data1']  = $data['nitrite'];//亚硝酸盐
		$postdata['data3']  = $data['whitecells'];   //白细胞
		$postdata['data4']  = $data['redcells'];   //红细胞
		$postdata['data5']  = $data['urineprotein'];   //尿蛋白
		$postdata['data6']  = $data['ph'];   //酸碱度
		$postdata['data7']  = $data['urine'];   //尿比重
		$postdata['data8']  = $data['urineketone'];   //尿酮
		$postdata['data9']  = $data['bili'];   //胆红素
		$postdata['data10'] = $data['sugar'];   //尿糖
		$postdata['data11'] = $data['vc'];   //维生素c
		$postdata['token']  = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata($this->geturl($name),$postdata);
		return $result;
	}
	/**
	 * 本次血糖报告
	 * @param unknown $data  */
	public function bbsugar($data=array(),$name=''){
		if(!$data){
			return '';
		}
		$postdata = array();
		$postdata['data']    = $data['bloodsugar'];//血糖
		$postdata['times']   = $data['attr'];//检测状态
		$postdata['history'] = 2;   //有无病史
		$postdata['token']   = $this->getkboxtoken($this->client_id,$this->secret,$this->url);
		$result = $this->getkboxdata(self::geturl('bsugar'),$postdata);
		return $result;
	}
	/**
	 * 获取服务器的url
	 * @param string $name
	 * @return string  */
	public function geturl($name=''){
		if($name){
			return $this->url.'/'.$name;
		}else{
			return '';
		}
	}
	
	/**
	 *  调用box接口，获取token值
	 *
	 **/
	
	public function getkboxtoken($client_id,$secret,$url){
		$data = array('username'=>$client_id,'encrypt'=>$secret);
		$tokdata = Spost($url,$data);
		$tokdata = json_decode($tokdata,true);
		return $tokdata['token'] ? $tokdata['token'] :'';
	}
	
	/**
	 * 调用box接口，获取数据
	 * @param string $url kbox地址
	 * @param unknown $type为0时,单个分项报告,1 时,整体报告
	 * @param unknown $data 要post的数据  */
	
	public function getkboxdata($url='',$data =array(),$type=0){
		$data = Spost($url,$data);
		$data = json_decode($data,true);
		$data = self::getdata($data,$type);
		return $data;
	}
	/**
	 * 调用box接口，获取数据,并进行格式转换
	 * @param unknown $type为0时,单个分项报告,1 时,整体报告
	 * @param unknown $data 数组  */
	public function getdata($data = array(),$type=0){
		if(empty($type)){
			foreach($data as $k=>$v){
				if(is_array($v)){
					$data[$k]['clinical'] = nl2br(htmlspecialchars_decode($v['clinical']));
					$data[$k]['result'] = nl2br(htmlspecialchars_decode($v['result']));
					$data[$k]['danger'] = nl2br(htmlspecialchars_decode($v['danger']));
					$data[$k]['nutrition'] = nl2br(htmlspecialchars_decode($v['nutrition']));
					$data[$k]['recovery'] = nl2br(htmlspecialchars_decode($v['recovery']));
				}else{
					$data['clinical'] = nl2br(htmlspecialchars_decode($data['clinical']));
					$data['result'] = nl2br(htmlspecialchars_decode($data['result']));
					$data['danger'] = nl2br(htmlspecialchars_decode($data['danger']));
					$data['nutrition'] = nl2br(htmlspecialchars_decode($data['nutrition']));
					$data['recovery'] = nl2br(htmlspecialchars_decode($data['recovery']));
					return $data;
				}
			}
			return $data;
		}else{
			$data	=	$this->formatreport($data);
			return $data;
		}
	}
	
	/**
	 * 整体报告格式化
	 * @param unknown $data 整体报告
	 * @return string
	 */
	public function formatreport($data=array()){
	
		if(!$data){
			return '';
		}
		foreach($data as $k=>$v){
			$data[$k]	=	nl2br(htmlspecialchars_decode($v));
		}
		return $data;
	
	}
	
	
}