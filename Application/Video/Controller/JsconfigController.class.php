<?php
namespace Video\Controller;
use Think\Controller;
class JsconfigController extends Controller {
	private	$serverAddr		= "v.yicheng120.com";
	private	$serverPort		= "8906";
	private	$serverAds		= "http://api.yicheng120.com/Public/Video/images/bg.jpg";

	
 	public function __construct(){
		parent::__construct();
	}

	/**
	* 
	* 
	* @param 
	* @author ripen_wang@163.com
	* @data 
	*/
	public function index(){
		$r	= I('r') ? substr(I('r'),4) : mt_rand(100000, 999999);
		if (I('t')===$this->get_token()) {
			$this->show("var mDefaultServerAddr = '".$this->serverAddr."';var mDefaultServerPort =  '".$this->serverPort."'; var mDefaultAdsAddr='".$this->serverAds."';var roomnum='".$r."'");
		}
	}

	private function get_token() {
		return S('token');
	}

}