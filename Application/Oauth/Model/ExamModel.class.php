<?php
namespace Oauth\Model;
use Think\Model;


class ExamModel extends Model {

	protected $autoCheckFields = false;
	
	public function __construct( ){
		parent::__construct();

	}

	/**
     * 根据type获取不同的数据
     *
     * @author wangyangyang
     * @version V1.0
     *
     * @param integer $userid 用户ID
     * @param string $type 检测类型
     * @return [type] [description]
     */
    public function getinfo($userid = 0 ,$type = ''){
    	if (!$userid || !$type ) {
    		return false;
    	}

    	$types 	=	array('bp','gl','ox','ur','bf','el','we','tm');
    	if (!in_array($type, $types)) {
    		return false;
    	}

    	$tablename	=	self::getTable($type);
    	if ( !$tablename ) {
    		return false;
    	}

    	$where	=	array();
    	$where['userid']	=	$userid;
    	if ($type == 'el') {
    		$where['status']	=	1;
    	}

    	$info 	=	M($tablename)->where($where)->order('id desc')->limit(1)->find();

    	if ( !$info ) {
    		return false;
    	}

    	// 特殊处理体成分，区分男女
    	$userinfo 	=	array();
    	if ( $type == 'we' ) {
    		$userinfo 	=	M('member_detail')->where(array('userid'=>$userid))->find();
    	}
    	// 性别男
    	$sex 	=	'';
    	if ($userinfo && $userinfo['sex'] == '0') {
    		$sex 	=	1;
    	}else if ($userinfo && $userinfo['sex'] == '1') {
    		$sex 	=	2;
    	}

    	$result 	=	self::formatdata($info,$type,$sex);

    	return $result;
    }


    /**
     * 根据设备类型获取到对应的表
     *
     * @param string $type 检测类型
     * @author wangyangyang
     * @version V1.0
     * @return [type] [description]
     */
    public function getTable($type = ''){
    	if ( !$type ) {
    		return false;
    	}

    	$table 	=	'';
    	switch ( $type ) {
    		case 'bp':
    			$table 	=	'kangbao_bloodp';
    			break;
    		case 'gl':
    			$table 	=	'kangbao_bbsugar';
    			break;
    		case 'ox':
    			$table 	=	'kangbao_oxygen';
    			break;
    		case 'ur':
    			$table 	=	'kangbao_urine';
    			break;
    		case 'bf':
    			$table 	=	'kangbao_bloodfat';
    			break;
    		case 'el':
    			$table 	=	'kangbao_electrocardio';
    			break;
    		case 'we':
    			$table 	=	'kangbao_humanbody';
    			break;
    		case 'tm':
    			$table 	=	'kangbao_tm';
    			break;
    	}
    	return $table;
    }
	

    /**
     * 格式化数据处理
     * 	主要是判断数据为升高，降低等情况
     *
     * @param array $data 检测数据
     * @param string $types 检测类型
     * @author wangyangyang
     * @version V1.0
     * @return string
     */
    public function formatdata($data,$types,$sex=''){
    	if (!$data || !$types ) {
    		return false;
    	}

    	$result 	=	array();

    	switch ( $types ) {
    		case 'gl':
    			$result['bbsugar']	=	$data['bloodsugar'];	//	血糖
				$result['showimg']	=	self::bbsugar($data['bloodsugar'],$data['attr']);
				$result['examtime']	=	$data['examtime'];
				$result['ranges']	=	self::bbsugarRange($data['attr']);
				$result['bbsugartitle']	=	self::bbsugartitle($data['attr']);
    			break;
    		
    		case 'bf':
    			$result['tg']		=	$data['tg'];	//	甘油三脂
				$result['tgimg']	=	self::bloodfat($data['tg'],2);

				$result['ltc']		=	$data['ltc'];	//	低密度脂蛋白
				$result['ltcimg']	=	self::bloodfat($data['ltc'],4);

				$result['htc']		=	$data['htc'];	//	高密度脂蛋白
				$result['htcimg']	=	self::bloodfat($data['htc'],3);

				$result['tc']		=	$data['tc'];	//	总胆固醇
				$result['tcimg']	=	self::bloodfat($data['tc'],1);

				$result['examtime']	=	$data['examtime'];
    			break;

    		case 'bp':
    			$result['lboodp']	=	$data['lboodp'];	//	低压值
				$result['lshowimg']	=	self::bloodp($data['lboodp'],1);

				$result['hboodp']	=	$data['hboodp'];	//	高压值
				$result['hshowimg']	=	self::bloodp($data['hboodp'],2);

				$result['hbr']		=	$data['hbr'];		//	心率
				$result['examtime']	=	$data['examtime'];
    			break;

    		case 'we':
    			$result['weight']	=	$data['weight'];		//	体重
				
				$result['bmi']		=	$data['bmi'];			//	BMI值
				$result['bmiimg']	=	self::humanbody($data['bmi'],2,$sex);

				$result['bf']		=	$data['bf'];			//	体脂百分比
				$result['bfimg']	=	self::humanbody($data['bf'],3,$sex);

				$result['fatweight']	=	$data['fatweight'];	//	去脂体重
				$result['fatweightimg']	=	self::humanbody($data['fatweight'],4);

				$result['protein']		=	$data['protein'];		//	内脏脂肪指数
				$result['proteinimg']	=	self::humanbody($data['protein'],5);

				$result['water']		=	$data['watercontentrate'];		//	身体总水分
				$result['waterimg']		=	self::humanbody($data['watercontentrate'],6);

				$result['muscle']		=	$data['muscle'];		//	肌肉量
				$result['muscleimg']	=	self::humanbody($data['muscle'],7);

				$result['mineralsalts']	=	$data['mineralsalts'];		//	骨量
				$result['mineralsaltsimg']=	self::humanbody($data['mineralsalts'],8);

				$result['fat']		=	$data['fat'];			//	基础代谢率
				$result['fatimg']	=	self::humanbody($data['fat'],9);

				$result['examtime']	=	$data['examtime'];
    			break;

    		case 'ox':
    			$result['pr']			=	$data['pr'];			//	脉率
				$result['primg']		=	self::oxygen($data['pr'],1);

				$result['saturation']	=	$data['saturation'];	//	血氧饱和度
				$result['saturationimg']=	self::oxygen($data['saturation'],2);

				$result['examtime']		=	$data['examtime'];
    			break;

    		case 'ur':
    			$result['urobilinogen']		=	$data['urobilinogen'];	//	尿胆原
				$result['urobilinogenimg']	=	0;

				$result['nitrite']			=	$data['nitrite'];		//	亚硝酸盐
				$result['nitriteimg']		=	0;

				$result['whitecells']		=	$data['whitecells'];		//	白细胞
				$result['whitecellsmg']		=	0;

				$result['redcells']			=	$data['redcells'];		//	红细胞
				$result['redcellsimg']		=	0;

				$result['urineprotein']		=	$data['urineprotein'];		//	尿蛋白
				$result['urineproteinmg']	=	0;

				$result['ph']				=	$data['ph'];		//	酸碱度
				$result['phimg']			=	self::urine($data['ph'],6);

				$result['urine']			=	$data['urine'];		//	尿比重
				$result['urineimg']			=	self::urine($data['urine'],7);

				$result['urineketone']		=	$data['urineketone'];		//	尿酮
				$result['urineketoneimg']	=	0;

				$result['bili']				=	$data['bili'];		//	胆红素
				$result['biliimg']			=	0;

				$result['sugar']			=	$data['sugar'];		//	尿糖
				$result['sugarimg']			=	0;

				$result['vc']				=	$data['vc'];		//	维生素c
				$result['vcimg']			=	0;

				$result['examtime']			=	$data['examtime'];
    			break;

    		case 'tm':
    			$result['tm']		=	$data['tmv'];			//	体温
				
				$result['examtime']	=	$data['examtime'];
    			break;

    		case 'el':
    			$result['bpm']		=	$data['bpm'];			//	心率
				$result['examtime']	=	$data['examtime'];
    			$result['showimg']	=	self::elinfo($data['bpm']);
    			break;
    	}


    	return $result;
			
    }




    /**
	 * 血糖显示升高还是降低处理
	 *
	 * @param string $data 检测值
	 * @param string $attr 进食状态
	 * @return [type] [description]
	 */
	private function bbsugar( $data , $attr = ''){
		if ( !$data ) {
			return 0;
		}

		$attr 	=	$attr ? $attr : 0;

		$arr = array ( 
			'1' => array ( '0' => '2.8', '1' => '6.0' ), // 空腹血糖
			'2' => array ( '0' => '2.8', '1' => '7.7' ),  // 早餐后2小时血糖
			'3' => array ( '0' => '2.8', '1' => '7.7' ),  // 随机血糖
			'5' => array ( '0' => '2.8', '1' => '6.0' ),  // 午餐前血糖
			'6' => array ( '0' => '2.8', '1' => '7.7' ),  // 午餐后2小时血糖
			'7' => array ( '0' => '2.8', '1' => '6.0' ),  // 晚餐前血糖
			'8' => array ( '0' => '2.8', '1' => '7.7' ),  // 晚餐后2小时血糖 
			'9' => array ( '0' => '2.8', '1' => '6.0' ),  // 睡前血糖
		);

		if (!$attr || $attr == 0 ) {
			$attr 	=	3;
		}
		$min 	=	$arr[$attr]['0'];
		$max 	=	$arr[$attr]['1'];

		if ( $data >= $min && $data <= $max ) {
			return 0;
		}elseif( $data < $min ){
			return 1;
		}elseif( $data > $max ){
			return 2;
		}
		return 0;
	}

	/**
	 * 血糖参数
	 *
	 * @param string $data 检测值
	 * @param string $attr 进食状态
	 * @return [type] [description]
	 */
	private function bbsugartitle( $attr = '3'){
		if ( !$attr ) {
			return '随机血糖';
		}
		$attr 	=	$attr ? $attr : 3;

		$arr = array ( 
			'1' => '空腹血糖',
			'2' => '早餐后2小时血糖',
			'3' => '随机血糖',
			'5' => '午餐前血糖',
			'6' => '午餐后2小时血糖',
			'7' => '晚餐前血糖',
			'8' => '晚餐后2小时血糖 ',
			'9' => '睡前血糖',
		);
		if (!isset($arr[$attr])) {
			return '随机血糖';
		}
		return $arr[$attr];
		
	}

	/**
	 * 根据不同饮食状态获取不同的血糖参考范围
	 *
	 * @param  string $attr 进食状态
	 * @return [type] [description]
	 */
	private function bbsugarRange( $attr = '' ){
		$str 	=	'';
		switch ($attr) {
			case '0':	//	随机血糖
				$str 	=	'2.8 - 7.7';
				break;
			case '1':	//	空腹血糖
				$str 	=	'2.8 - 6.0';
				break;
			case '2':	//	早餐后2小时血糖
				$str 	=	'2.8 - 7.7';
				break;
			case '3':	//	随机血糖
				$str 	=	'2.8 - 7.7';
				break;
			case '5':	//	午餐前血糖
				$str 	=	'2.8 - 6.0';
				break;
			case '6':	//	午餐后2小时血糖
				$str 	=	'2.8 - 7.7';
				break;
			case '7':	//	晚餐前血糖
				$str 	=	'2.8 - 6.0';
				break;
			case '8':	//	晚餐后2小时血糖
				$str 	=	'2.8 - 7.7';
				break;
			case '9':	//	睡前血糖
				$str 	=	'2.8 - 6.0';
				break;
			default:
				$str 	=	'4.4 - 7.7';
				break;
		}

		
		return $str;
	}


	/**
	 * 心电显示升高还是降低处理
	 *
	 * @param string $data 检测值
	 * @param string $attr 进食状态
	 * @return [type] [description]
	 */
	private function elinfo( $data ){
		if ( !$data ) {
			return 0;
		}

		$attr 	=	$attr ? $attr : 0;

		if ( $data >= 60 && $data <= 100 ) {
			return 0;
		}elseif( $data < 60 ){
			return 1;
		}elseif( $data > 100 ){
			return 2;
		}

		return 0;
	}



	/**
	 * 血压显示升高还是降低处理
	 * 	包含高压和低压
	 * @return [type] [description]
	 */
	private function bloodp( $data ,$types = 1 ){
		if ( !$data ) {
			return 0;
		}
		if ($types == 1) {
			$min = 60;
			$max = 90;
		}else{
			$min = 90;
			$max = 140;
		}
		if ( $data > $min && $data < $max ) {
			return 0;
		}elseif($data < $min ){
			return 1;
		}elseif($data > $max ){
			return 2;
		}
		return 0;
	}

	/**
	 * 血脂
	 * @param integer $types 1:总胆固醇 2:甘油三酯 
	 *                       3：高密度脂蛋白胆固醇 4：低密度脂蛋白胆固醇
	 * @return [type] [description]
	 */
	private function bloodfat( $data , $types = '1'){
		if ( !$data ) {
			return 0;
		}
		
		if ( $types == 1) {
			if ($data < 5.18 ) {
				return 0;
			}elseif($data > 5.18 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 2) {
			if ($data < 1.70 ) {
				return 0;
			}elseif($data > 1.70 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 3) {
			if ($data > 1.04 ) {
				return 0;
			}elseif($data < 1.04 ){
				return 1;
			}else{
				return 0;
			}
		}

		if ( $types == 4) {
			if ($data < 3.37 ) {
				return 0;
			}elseif($data > 3.37 ){
				return 2;
			}else{
				return 0;
			}
		}
	}

	/**
	 * 血氧
	 *
	 * 	@param integer $data 检测值
	 * 	@param integer $types 类型 1：脉率 2：血氧饱和度
	 */
	private function oxygen($data ,$types){
		if ( !$data ) {
			return 0;
		}
		
		if ( $types == 1) {
			if ($data >= 60 && $data <= 100 ) {
				return 0;
			}elseif($data < 60 ){
				return 1;
			}elseif($data > 100 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 2) {
			if ($data >= 90 && $data <= 100 ) {
				return 0;
			}elseif($data < 90 ){
				return 1;
			}elseif($data > 100){
				return 2;
			}else{
				return 0;
			}
		}
	}



	/**
	 * 体成分
	 *
	 * 	@param integer $data 检测值
	 * 	@param integer $types 类型 1：体重 2：BMI(kg/m2) 3:体脂率（％） 4:去脂体重（kg）
	 * 	                      	   5:内脏脂肪指数（kg） 6:身体总水分率（％）
	 * 	                      	   7:肌肉量（kg） 8:骨量  9:基础代谢率（kcal/day）
	 * @param integer $sex 性别
	 */
	private function humanbody($data,$types,$sex = ''){
		if ( !$data ) {
			return 0;
		}

		if ( $types == 2 ) {
			if( $data >= 18.5 && $data < 24){
				return 0;
			}elseif($data < 18.5 ){
				return 1;
			}elseif($data >= 24 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 3 ) {
			//	性别男
			if( $data >= 10 && $data <= 20 && $sex == 1){
				return 0;
			}elseif( $data < 10 && $sex == 1 ){
				return 1;
			}elseif( $data > 20 && $sex == 1 ){
				return 0;
			}elseif($sex == 1){
				return 0;
			}

			//	性别女
			if( $data >= 18 && $data <= 28 && $sex == 2){
				return 0;
			}elseif( $data < 18 && $sex == 2 ){
				return 1;
			}elseif( $data > 28 && $sex == 2 ){
				return 0;
			}elseif($sex == 2){
				return 0;
			}
		}

		if ( $types == 4 ) {
			if( $data >= 41.6 && $data <= 50.8){
				return 0;
			}elseif($data < 41.6 ){
				return 1;
			}elseif($data > 50.8 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 5 ) {
			if( $data >= 0 && $data <= 9){
				return 0;
			}elseif($data < 0 ){
				return 1;
			}elseif($data > 9 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 6 ) {
			if( $data >= 48.1 && $data <= 57){
				return 0;
			}elseif($data < 48.1 ){
				return 1;
			}elseif($data > 57 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 7 ) {
			if( $data >= 39.2 && $data <= 48){
				return 0;
			}elseif($data < 39.2 ){
				return 1;
			}elseif($data > 48 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 8 ) {
			if( $data >= 2.1 && $data <= 2.4){
				return 0;
			}elseif($data < 2.1 ){
				return 1;
			}elseif($data > 2.4 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 9 ) {
			if( $data >= 1268 && $data <= 1467){
				return 0;
			}elseif($data < 1268 ){
				return 1;
			}elseif($data > 1467 ){
				return 2;
			}else{
				return 0;
			}
		}
	}



	/**
	 * 尿11项
	 * @param integer $types 1：尿胆原 2：亚硝酸盐 3：白细胞 4：潜血 5：尿蛋白
	 *                       6：酸碱度 7：尿比重 8：尿酮 9： 胆红素
	 *                       10：尿糖 11：维生素c
	 * @return [type] [description]
	 */
	private function urine( $data , $types = '1'){
		if ( !$data ) {
			return 0;
		}
		
		if ( $types == 6 ) {
			if ($data >= 5 && $data <= 7 ) {
				return 0;
			}elseif($data < 5 ){
				return 1;
			}elseif( $data > 7 ){
				return 2;
			}else{
				return 0;
			}
		}

		if ( $types == 7 ) {
			if ($data >= 1.000 && $data <= 1.005 ) {
				return 0;
			}elseif($data < 1.000 ){
				return 1;
			}elseif( $data > 1.005 ){
				return 2;
			}else{
				return 0;
			}
		}

	}
}