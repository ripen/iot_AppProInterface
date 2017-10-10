<?php

namespace Pad\Controller;
use Pad\Controller\BaseController;
use Think\Controller;

class InController extends BaseController{

	private $sign;
	private $infos	= array();

 	public function __construct(){
		parent::__construct();

        // 验证token，用在模块功能的验证，不能放在Base里
        $this->client_id    =   $this->oauth->verify_access_token();
        // 检测应用访问权限
        $this->client_info  =   $this->checkpriv( $this->client_id );
        // 记录日志
        $this->visitlog( $this->client_id );


		//$this->sign				= I('post.sign','','htmlspecialchars,trim');		// 标识检验码

		$this->infos			= array(
									'iccardnumber'	=>I('post.iccardnumber','','htmlspecialchars,trim'),	//卡号/身份证
									'deviceid'		=>I('post.deviceid','','htmlspecialchars,trim'),//检测设备序列号，也即UUID
									'mac'			=>I('post.mac','','htmlspecialchars,trim'),//检测设备MAC地址
									'hdgatedeviceid'=>I('post.hdgatedeviceid','','htmlspecialchars,trim'),//平板网关用户ID
									'sysinfos'		=>I('post.sysinfos','','htmlspecialchars,trim'),//平板网关系统版本、APP版本
									'measuretime'	=>I('post.measuretime','','htmlspecialchars,trim'),// 测量时间
								);

	}

	/**
	* 心电数据传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function addHeart(){

		$heartrate	=	I('post.heartrate','','htmlspecialchars,trim');		// 心率值
		$datasize	=	I('post.datasize','','htmlspecialchars,trim');		// 心电数据长度
		$data	=	I('post.data','','htmlspecialchars,trim');		// 心电数据[二进制数据流]

		if ( !$heartrate || !$datasize) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['heartrate']	= $heartrate;
		$this->infos['datasize']	= $datasize;
		$this->infos['data']		= $data;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 血糖传输传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function addGluce(){

		$measuretype=	I('post.measuretype','','htmlspecialchars,trim');	// 测量类型
		$glucevalue	=	I('post.glucevalue','','htmlspecialchars,trim');		// 血糖值


		if ( !$measuretype || !$glucevalue) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['measuretype']	= $measuretype;
		$this->infos['glucevalue']	= $glucevalue;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 血压传输传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function addBloodpressure(){
		$infos		= array();

		$highpressure	=	I('post.highpressure','','htmlspecialchars,trim');		// 高压
		$lowpressure	=	I('post.lowpressure','','htmlspecialchars,trim');		// 低压
		$averagepressure=	I('post.averagepressure','','htmlspecialchars,trim');	// 平均压
		$pulserate		=	I('post.pulserate','','htmlspecialchars,trim');			// 脉率

		if ( !$highpressure || !$lowpressure) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['highpressure']	= $highpressure;
		$this->infos['lowpressure']		= $lowpressure;
		$this->infos['averagepressure']	= $averagepressure;
		$this->infos['pulserate']		= $pulserate;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 血氧传输传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function addOxygen(){
		$infos		= array();

		$spo2		=	I('post.spo2','','htmlspecialchars,trim');		// 血氧值
		$pulserate	=	I('post.pulserate','','htmlspecialchars,trim');		// 脉率
		$datasize	=	I('post.datasize','','htmlspecialchars,trim');	// 脉率数据长度
		$data		=	I('post.data','','htmlspecialchars,trim');			// 脉率数据

		if ( !$spo2 || !$pulserate) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['spo2']		= $spo2;
		$this->infos['pulserate']	= $pulserate;
		$this->infos['datasize']	= $datasize;
		$this->infos['data']		= $data;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 身体成份数据传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function addBodycomposition(){
		$infos		= array();

		$impedance	=	I('post.impedance','','htmlspecialchars,trim');		// 阻抗
		$fat		=	I('post.fat','','htmlspecialchars,trim');		// 体脂
		$muscle		=	I('post.muscle','','htmlspecialchars,trim');		// 肌肉含量
		$water		=	I('post.water','','htmlspecialchars,trim');			// 水含量
		$skeleton	=	I('post.skeleton','','htmlspecialchars,trim');		// 骨骼
		$visceralfat=	I('post.visceralfat','','htmlspecialchars,trim');		// 内脂
		$bmi		=	I('post.bmi','','htmlspecialchars,trim');		// 体质指数
		$bmr		=	I('post.bmr','','htmlspecialchars,trim');		// 基础代谢率

		if ( !$impedance || !$fat) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['impedance']	= $impedance;
		$this->infos['fat']			= $fat;
		$this->infos['muscle']		= $muscle;
		$this->infos['water']		= $water;
		$this->infos['skeleton']	= $skeleton;
		$this->infos['visceralfat']	= $visceralfat;
		$this->infos['bmi']			= $bmi;
		$this->infos['bmr']			= $bmr;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 尿11项数据传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function addUrine(){
		$infos		= array();

		$sg			=	I('post.sg','','htmlspecialchars,trim');			// 尿比重
		$ph			=	I('post.ph','','htmlspecialchars,trim');			// PH值
		$prolevel	=	I('post.prolevel','','htmlspecialchars,trim');	// 尿蛋白符号
		$pro		=	I('post.pro','','htmlspecialchars,trim');		// 尿蛋白
		$glulevel	=	I('post.glulevel','','htmlspecialchars,trim');	// 尿糖符号
		$glu		=	I('post.glu','','htmlspecialchars,trim');		// 尿糖
		$ketlevel	=	I('post.ketlevel','','htmlspecialchars,trim');	// 尿酮体符号
		$ket		=	I('post.ket','','htmlspecialchars,trim');		// 尿酮体
		$ubillevel	=	I('post.ubillevel','','htmlspecialchars,trim');	// 胆红素符号
		$ubil		=	I('post.ubil','','htmlspecialchars,trim');		// 胆红素
		$ubclevel	=	I('post.ubclevel','','htmlspecialchars,trim');	// 尿胆原符号
		$ubc		=	I('post.ubc','','htmlspecialchars,trim');		// 尿胆原
		$nitlevel	=	I('post.nitlevel','','htmlspecialchars,trim');	// 亚硝酸盐符号
		$nit		=	I('post.nit','','htmlspecialchars,trim');		// 亚硝酸盐
		$leulevel	=	I('post.leulevel','','htmlspecialchars,trim');	// 白细胞符号
		$leu		=	I('post.leu','','htmlspecialchars,trim');		// 白细胞
		$erylevel	=	I('post.erylevel','','htmlspecialchars,trim');	// 红细胞符号
		$ery		=	I('post.ery','','htmlspecialchars,trim');		// 红细胞
		$vclevel	=	I('post.vclevel','','htmlspecialchars,trim');		// 维生素符号
		$vc			=	I('post.vc','','htmlspecialchars,trim');		// 维生素

		if ( !$sg || !$ph) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['sg']			= $sg;
		$this->infos['ph']			= $ph;
		$this->infos['prolevel']	= $prolevel;
		$this->infos['pro']			= $pro;
		$this->infos['glulevel']	= $glulevel;
		$this->infos['glu']			= $glu;
		$this->infos['ketlevel']	= $ketlevel;
		$this->infos['ket']			= $ket;
		$this->infos['ubillevel']	= $ubillevel;
		$this->infos['ubil']		= $ubil;
		$this->infos['ubclevel']	= $ubclevel;
		$this->infos['ubc']			= $ubc;
		$this->infos['nitlevel']	= $nitlevel;
		$this->infos['nit']			= $nit;
		$this->infos['leulevel']	= $leulevel;
		$this->infos['leu']			= $leu;
		$this->infos['erylevel']	= $erylevel;
		$this->infos['ery']			= $ery;
		$this->infos['vclevel']		= $vclevel;
		$this->infos['vc']			= $vc;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 血脂数据传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function CholTransmit(){
		$infos		= array();

		$chol			=	I('post.chol','','htmlspecialchars,trim');			// 总胆固醇
		$cholSign		=	I('post.cholSign','','htmlspecialchars,trim');			// 总胆固醇符号
		$trig			=	I('post.trig','','htmlspecialchars,trim');	// 甘油三脂
		$trigSign		=	I('post.trigSign','','htmlspecialchars,trim');		// 甘油三脂符号
		$hdlc			=	I('post.hdlc','','htmlspecialchars,trim');	// 高密度脂蛋白固醇
		$hdlcSign		=	I('post.hdlcSign','','htmlspecialchars,trim');		// 高密度脂蛋白固醇符号
		$ldl			=	I('post.ldl','','htmlspecialchars,trim');	// 低密度脂蛋白固醇
		$ldlSign		=	I('post.ldlSign','','htmlspecialchars,trim');		// 低密度脂蛋白固醇符号
		$ratio			=	I('post.ratio','','htmlspecialchars,trim');	// 胆固醇高度密度比
		$ratioSign		=	I('post.ratioSign','','htmlspecialchars,trim');		// 胆固醇高度密度比符号
		
		if ( !$chol || !$trig) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['chol']		= $chol;
		$this->infos['cholSign']	= $cholSign;
		$this->infos['trig']		= $trig;
		$this->infos['trigSign']	= $trigSign;
		$this->infos['hdlc']		= $hdlc;
		$this->infos['hdlcSign']	= $hdlcSign;
		$this->infos['ldl']			= $ldl;
		$this->infos['ldlSign']		= $ldlSign;
		$this->infos['ratio']		= $ratio;
		$this->infos['ratioSign']	= $ratioSign;

		oauthjson(200,'success',$this->infos);
    }

	/**
	* 体温数据传输API
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 2017/9/27
	*/
	public function GluTransmit(){
		$infos		= array();

		$temp			=	I('post.temp','','htmlspecialchars,trim');			// 温度值
		
		if ( !$temp) {
    		oauthjson(10001,'检测数据出错');
		}

		$this->infos['temp']	= $temp;

		oauthjson(200,'success',$this->infos);
    }

}