<?php
namespace Oauthai\Controller;
use Oauthai\Controller\BaseController;


/**
 * 解决方案 
 *     医生咨询
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ConsultationController extends BaseController {  

    public function __construct(){
    	parent::__construct();
    }

    /**
     * 医生咨询
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        $id     =   I('post.id','','intval');
        $id     =   $id ? $id : '';

        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 查询疾病信息
        $info  =   D('Diseases')->show($id);
        if ( !$info ) {
            oauthjson(80001,'暂无信息');
            exit;
        }

        $reuslt     =   array();

        $result['title']    =   '视频咨询服务';

        $result['explain']  =   '1. 解答用户的健康方面的问题。<br/>2. 怡成网络医院特聘医师均为副主任医师及主任医师级别的北京各大医院医师。<br/>3. 购买服务后，请根据医师值班表选择医师，进行视频咨询。';

        $result['worth']    =   '1. 解决健康疑问。<br/>2. 提高疾病筛查能力。<br/>3. 防止慢病加重。<br/>4. 大大节省时间，提高您的生活质量。';

        $result['content']  =   '商品详情收费标准：15元/次​<br/>咨询时间：每次5分钟，每次不超过5分钟，按5分钟计算。'; 

        $result['operation']=   '第一步：注册并登录怡成网络医院。网址：<a href="http://www.yicheng120.com">www.yicheng120.com</a><br/>第二步：在线购买视频咨询服务。<br />第三步：点击【视频咨询】咨询医师。首次视频根据提示下载并安装视频插件。';

        $data       =   array();
        $data['info']   =   $info;
        $data['result'] =   $result;

        oauthjson(200,'获取成功',$data);
        exit;
    }
}

