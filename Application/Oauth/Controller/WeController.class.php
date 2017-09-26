<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 体成分数据传输
 * 
 * @author wangyangyang
 * @version V1.0
 */
class WeController extends BaseController {  

    // client_id
    private $client_id   =   '';

    public function __construct(){
    	parent::__construct();  
        $this->client_id    =   $this->oauth->verify_access_token();
        
        // 记录日志
        $this->visitlog( $this->client_id );

    }

    /**
     * 体成分数据列表信息
     * 
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function index(){
        // 当前页
        $p  =   I('post.p',1,'intval');
        $p  =   max(1,$p);
        
        // 每页展示条数
        $pagesize   =   I('post.pagesize','','intval');
        $pagesize   =   $pagesize ? intval($pagesize) : 10;
        if ($pagesize > 20 ) {
            $pagesize   =   20;
        }

        // 获取用户绑定的药店信息
        $drugs      =   $this->oauth->get_drug_id($this->client_id);

        // 未查询到信息 返回无权限
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        $drugsid=   extractArray($drugs,'drugsid');

        $info   =   D('We')->lists($drugsid,$p,$pagesize);

        if ( !$info ) {
            oauthjson(70001,'未获取到数据');
            exit;
        }

        // 查询卡号
        $data       =   $info['info'];
        $cardid     =   extractArray($data,'cardid');
        $cardinfo   =   D('Card')->getcardbyidarr($cardid);

        // 返回数据为展示卡号
        foreach ($data as $key => $value) {
            if (isset($cardinfo[$value['cardid']]) && $cardinfo[$value['cardid']] ) {
                $data[$key]['card'] =   $cardinfo[$value['cardid']]['wholecard'];
            }else{
                $data[$key]['card'] =   '';
            }

            unset($data[$key]['cardid']);
        }
        $info['info']   =   $data;

        oauthjson(200,'获取成功',$info);
        exit;
    }


    /**
     * 体成分数据列表信息
     *     具体某张卡号的所有数据
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function lists(){
        // 当前页
        $p  =   I('post.p',1,'intval');
        $p  =   max(1,$p);
        
        // 每页展示条数
        $pagesize   =   I('post.pagesize','','intval');
        $pagesize   =   $pagesize ? intval($pagesize) : 10;
        if ($pagesize > 20 ) {
            $pagesize   =   20;
        }

        // 获取用户绑定的药店信息
        $drugs      =   $this->oauth->get_drug_id($this->client_id);

        // 未查询到信息 返回无权限
        if ( !$drugs ) {
            $this->nopriv();
            exit;
        }

        $card   =   I('post.card','','htmlspecialchars,trim,strip_tags');
        
        if ( !$card ) {
            $this->noparam();
            exit;
        }
        $cardinfo   =   $this->checkcardinfo( $card , $this->client_id );
    
        // 查询用户数据列表信息 
        $info   =   D('We')->listsbycard($cardinfo['id'],$p,$pagesize);
        if ( !$info ) {
            oauthjson(70001,'未获取到数据');
            exit;
        }

        oauthjson(200,'获取成功',$info);
        exit;
    }

    /**
     * 体成分数据分析
     * 
     * @author wangyangyang
     * @version V1.0
     */
    public function show(){
        $id     =   I('post.id','','intval');
        
        if ( !$id ) {
            $this->noparam();
            exit;
        }

        // 查询id对应的卡号信息，判断其是否有操作权限
        $info   =   D('We')->getinfo($id);

        if ( !$info ) {
            oauthjson(70002,'未查询到数据');
            exit;
        }

        // 判断检测数据是否可展示
        $this->checkdatainfo( $info['drugid'] ,$this->client_id );

        // 读取接口，进行数据分析
        $postdata               =   array();
        $postdata['weight']             =   $info['weight'];
        $postdata['bmi']                =   $info['bmi'];
        $postdata['bf']                 =   $info['bf'];
        $postdata['fatweight']          =   $info['fatweight'];
        $postdata['protein']            =   $info['protein'];
        $postdata['watercontentrate']   =   $info['watercontentrate'];
        $postdata['muscle']             =   $info['muscle'];
        $postdata['mineralsalts']       =   $info['mineralsalts'];
        $postdata['fat']                =   $info['fat'];

        $sex    =   $info['gender'] && $info['gender'] == 2 ? 1 : 0; 
        $postdata['sex'] =  $sex;

        $analysis   =   new \Oauth\Common\analysis();
        $result     =   $analysis->result($postdata,'we');
        
        
        if ( !$result || $result['status'] == 'resultError' ) {
            oauthjson(70003,'未分析出结果');
            exit;
        }

        // 检测数据信息不进行返回
        $num    =   count($result);
        if ($num >= 1 ) {
            foreach ($result as $key => $value) {
                $result[$key]['tests']  =   $result[$key]['data']['tests'];
                unset($result[$key]['data']);
            }

        }

        oauthjson(200,'获取成功',$result);
        exit;

    }
}

