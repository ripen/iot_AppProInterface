<?php
namespace Common\Analysisclass;

/**
 * 调用分析结果
 * 	分析结果从两方面获取到，如有其他存储类型的，遵照此类进行延伸
 * 	1：redis
 * 	2: mysql
 *
 * 	
 * @author      wangyangyang
 * @copyright   2016-06-07
 * @version     V1.0
 */
interface ifactory{

	/**
	 * 血糖
	 * 
	 * @param  string $gl       血糖测量值
	 * @param  string  $times   进食状态
	 * @param  string  $history 病史
	 * @return 
	 */
	public function bsugar( $gl = '' , $times = '' , $history = '' ) ;
	
	/**
	 * 血脂
	 * 
	 * @param  string $tch 总胆固醇
	 * @param  string $tg  甘油三酯（三酰甘油）
	 * @param  string $ldl 低密度脂蛋白胆固醇
	 * @param  string $hdl 高密度脂蛋白胆固醇
	 * 
	 */
	public function bloodfat( $tch = '' , $tg = '', $ldl = '', $hdl = '' ) ;

	/**
	 * 血压
	 * @param  string $hdata  高压
	 * @param  string $ldata  低压
	 * 
	 */
	public function bloodp( $hdata = '' , $ldata = '' ) ;
	
	
	/**
	 * 血氧
	 * 
	 * @param  string $ox  血氧测量值
	 * @param  string $bpm 脉率
	 * 
	 */
	public function oxygen( $ox = '' , $bpm = '' ) ;
	

	/**
	 * 恒温
	 * 
	 * @param  string $tm  恒温测量值
	 * 
	 */
	public function tm( $tm = '' ) ;
	
	/**
	 * 心电
	 * 
	 * @param  string $bpm  心电仪器中心率测量值
	 * 
	 */
	public function el( $bpm = '' ) ;
	

	/**
	 * 尿常规
	 * 
	 * @param  string $urobilinogen 尿胆原
	 * @param  string $nitrite      亚硝酸盐
	 * @param  string $whitecells   白细胞
	 * @param  string $redcells     潜血、红细胞
	 * @param  string $urineprotein 尿蛋白
	 * @param  string $ph           酸碱度
	 * @param  string $urine        尿比重
	 * @param  string $urineketone  尿酮
	 * @param  string $bili         胆红素
	 * @param  string $sugar        尿糖
	 * @param  string $vc           维生素C
	 * 
	 */
	public function urine( $urobilinogen = '' , $nitrite = '' , $whitecells = '' , $redcells = '' , $urineprotein = '' , $ph = '' , $urine = '' , 
		$urineketone = '' , $bili = '' , $sugar = '' , $vc = '' ) ;
	

	/**
	 * 体成分
	 * 
	 * @param  string $weight           体重
	 * @param  string $bmi              BMI 
	 * @param  string $bf               体脂率（％）
	 * @param  string $fatweight        去脂体重（kg）
	 * @param  string $protein          内脏脂肪指数（kg）
	 * @param  string $watercontentrate 身体总水分率（％）
	 * @param  string $muscle           肌肉量（kg）
	 * @param  string $mineralsalts     骨量
	 * @param  string $fat              基础代谢率
	 * @param  string $sex 				性别( 0 ：男 1：女)
	 * 
	 */
	public function humanbody( $weight = '', $bmi = '' , $bf = '', $fatweight = '', $protein = '', $watercontentrate = '', $muscle = '', $mineralsalts = '', $fat= '' , $sex = '' ) ;

	/**
	 * 整体大项正常返回结果
	 * @param  string $datas 设备类型 (一次获取多个，中间用英文逗号拆分)
	 *          1：血糖 2：血压 3：血氧 4：血脂 5：体成分 6：尿11项 7：恒温	
	 * @return [type]        [description]
	 */
	public function resources( $datas = '' ) ;
	

	/**
	 * 血酮
	 * 
	 * @param  string $bk  血酮
	 * 
	 */
	public function bloodketone( $bk = '' ) ;
	

	/**
	 * 血尿酸
	 * 
	 * @param  string $suricacid  血尿酸
	 * @param  string $sex  性别
	 * 
	 */
	public function renal( $suricacid = '' , $sex = '' ) ;
	

	/**
	 * 血尿酸（μmol/L）
	 * 
	 * @param  string $suricacid  血尿酸
	 * @param  string $sex  性别
	 * 
	 */
	public function renalnew( $suricacid = '' , $sex = '' ) ;
	

	/**
	 * 尿微量白蛋白
	 * 
	 * @param  string $umprotein  尿微量蛋白
	 * 
	 */
	public function umprotein( $umprotein = '') ;
	
}
