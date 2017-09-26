<?php
namespace Gprs\Controller;
use Think\Controller;
class GController extends Controller {
	private $returnOK		= 'resultOK';
	private $returnError	= 'resultError';
	private $resultOKmsg	= '获取Token成功!';
	private $resultErrormsg	= '获取Token失败!请检查服务地址、访问帐号和访问口令是否正确!';
	private $tokenlifetime	= '10';	//TOKEN生命值 ，单位秒；
	
 	public function __construct(){
		parent::__construct();
	}

	/**
	* GPRS血糖仪的数据传输接口
	* http://api.yicheng120.com/Gprs/g/d/i/XXXXXXXXXXXX
	*
	* @param i 就是传输的16进制数值
	* @author ripen_wang@163.com
	* @data 2015/12/3
	* @TODO 接下来需要把数据解读之后再插入到pf_kangbao_bbsugar表中
	*/
	public function d(){
		$data 	=	file_get_contents("php://input");
		
		$data	=	$data ? bin2hex($data) : '';
		
		$str 	=	substr($data, 0,4);
		if (!$data ||  $str == '6874' ) {
			F('gprs',date('Y-m-d H:i:s').'====data wrong');
			echo 'Ok';
			exit;
		}
		$formatData 	=	$this->formatData($data);
		if ( !$formatData ) {
			F('gprs',date('Y-m-d H:i:s').'====formatData wrong');
			echo 'Ok';
			exit;
		}
		$datas	= array(
						'addtime'	=> date('Y-m-d H:i:s'),
						'data'		=> json_encode($formatData),
						'sign'		=> 'gprs_1',
					);
		$insertID	= M("equipment_log")->add($datas);

		echo $insertID ? 'Ok' : 'Error';
	}

	/**
	* 当天插入的前十条数据展示，目前只是给工厂前期的测试用
	* http://api.yicheng120.com/Gprs/g/l
	* @param 
	* @author ripen_wang@163.com
	* @data 2015/12/3
	*/
	public function l(){
		$list	= M("equipment_log")->field('data,addtime')->where('sign="gprs_1" ')->order('id DESC')->limit(10)->select();
		echo '<font color="red" size="-1">*注 只读取前十条，自动七秒刷新一次!</font></br>';

		$result 	=	'';
		$result 	.=	'<table border="1" cellpadding="0" cellspacing="0">';
		if( $list ){
			$result 	.=	'<tr>';
			$result 	.=	'<td>协议版本号</td>';
			$result 	.=	'<td>数据类型</td>';
			$result 	.=	'<td>仪器ID</td>';
			$result 	.=	'<td>仪器型号</td>';
			$result 	.=	'<td>仪器SIM</td>';
			$result 	.=	'<td>测量数据时间</td>';
			$result 	.=	'<td>温度</td>';
			$result 	.=	'<td>校正码</td>';
			$result 	.=	'<td>结果(mmol/L)</td>';
			$result 	.=	'</tr>';
			foreach ($list as $key => $value) {
				$data 	=	json_decode($value['data'],true);
				$result 	.=	'<tr>';
				$result 	.=	'<td>V.'.$data[2].'</td>';

				$types	=	'';
				$jz 	=	'';
				switch ($data[3]) {
					case '00':
						$types	=	'血糖';
						$jz 	=	'A';
						break;
					case '01':
						$types	=	'血酮体';
						break;
					case '02':
						$types	=	'血尿酸';
						break;
					case '03':
						$types	=	'血乳酸';
						break;
					default:
						# code...
						break;
				}

				$result 	.=	'<td>'.$types.'</td>';
				$result 	.=	'<td>'.$data[4].'</td>';
				$result 	.=	'<td>'.$data[5].'</td>';
				$result 	.=	'<td>'.$data[6].'</td>';
				$result 	.=	'<td>'.$data[7].'</td>';
				$result 	.=	'<td>'.$data[8].'</td>';
				$result 	.=	'<td>'.$jz.$data[9].'</td>';
				$result 	.=	'<td>'.$data[11].' : '.$data[10].'</td>';
				$result 	.=	'</tr>';
			}	
		}

		$result 	.=	'</table>';
		
		echo $result;
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"7; URL=/Gprs/g/l\">"; 
	}


	/**
	 * 处理获取到的GPRS数据
	 *
	 * @author wangyangyang
	 * @version V1.0
	 * @param  string $data 设备传递过来的数据
	 * @return array 返回原始数据解析后的情况
	 */
	private function formatData($data = ''){
		if ( !$data ) {
			return false;
		}

		// 数据校验
		$length	=	strlen($data);
		if ( $length != 76) {
			F('gprs',date('Y-m-d H:i:s').'====formatData length wrong');
			return false;
		}

		$check 	=	array();
		for ($i=0; $i <= $length - 4 ; $i=$i+2 ) { 
			//	16进制转换10进制
			$check[]	=	intval(hexdec($data{$i}.$data{$i+1}));
		}
		
		// 开始检验
		$last		=	0;
		$center		=	0;
		foreach($check AS $key => $val){
			$center	=	$center ? $center ^ $val : $val;
		}
		// 原始数据校验码
		$checkcode	=	substr($data,-2);
		$checkcode	=	intval(hexdec($checkcode));
		if ( strtolower($center) != strtolower($checkcode)) {

			F('gprs',date('Y-m-d H:i:s').'====formatData check wrong');
			return false;
		}
		
	
		//	数据处理
		$gprs 	=	array();
		$gprs[1]	=	substr($data,0,4);			//	数据总长度	1~2
		$gprs[2]	=	substr($data,4,2);			//	协议版本号	3
		$gprs[3]	=	substr($data,6,2);			//	数据类型	4
		$gprs[4]	=	substr($data,8,16);			//	仪器ID		5~12
		$gprs[5]	=	substr($data,24,20);		//	仪器型号	13~22
		$gprs[6]	=	substr($data,44,12);		//	仪器SIM		23~28
		$gprs[7]	=	substr($data,56,8);			//	测量时间	29~32
		$gprs[8]	=	substr($data,64,4);			//	测量温度	33~34
		$gprs[9]	=	substr($data,68,2);			//	校正码		35
		$gprs[10]	=	substr($data,70,4);			//	测试结果	36~37
		$gprs[11]	=	substr($data,74,2);			//	校验和		38
		
		
		//	数据解析
		$result 	=	array();
		//数据总长度
		$result[1]	=	hexdec($gprs[1]);
		
		//	协议版本号
		$result[2]	=	hexdec($gprs[2]);
		
		//	数据类型
		$result[3]	=	$gprs[3];
		
		//	仪器ID
		$result[4]	=	$gprs[4];
		
		//	仪器型号
		$result[5]	=	pack("H*",$gprs[5]);
		
		//	仪器的SIM卡
		$result[6]	=	substr($gprs[6],1,strlen($gprs[6]));
		
		//	测量数据时间
		$result[7]	=	date('Y-m-d H:i:s',hexdec($gprs[7]));
		
		//	测量数据温度
		$result[8]	=	sprintf('%.1f',(float)hexdec($gprs[8]) / 10.0 );
		
		//	校正码
		$result[9]	=	hexdec($gprs[9]);
		
		//	测试结果
		$data		=	base_convert($gprs[10],16,2);
		$data		=	str_pad($data,16,0,STR_PAD_LEFT);

		$eat		=	substr($data,0,4);
		$gldata		=	substr($data,4);

		$eat		=	base_convert($eat,2,16);
		$gldata		=	base_convert($gldata,2,10);
		
		$result[10]	=	sprintf('%.1f',(float)$gldata / 10.0 );

		if( strtolower($eat) == 'a' ){
			$gltimes	=	'餐后';
		}elseif( strtolower($eat) == 'b' ){
			$gltimes	=	'餐前';
		}

		$result[11]		=	$gltimes;

		$result['original']	=	$data;

		return $result;
	}

}