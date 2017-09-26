<?php
namespace Oauth\Controller;
use Oauth\Controller\BaseController;


/**
 * 心电数据传输
 * 
 * @author wangyangyang
 * @version V1.0
 */
class ElController extends BaseController {  

    // client_id
    private $client_id   =   '';

    public function __construct(){
    	parent::__construct();  
        $this->client_id     =   $this->oauth->verify_access_token();
        
        // 记录日志
        $this->visitlog( $this->client_id );

    }

    /**
     * 心电数据列表信息
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

        $info   =   D('El')->lists($drugsid,$p,$pagesize);

        if ( !$info ) {
            oauthjson(90001,'未获取到数据');
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
     * 血氧数据列表信息
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
        $info   =   D('El')->listsbycard($cardinfo['id'],$p,$pagesize);
        if ( !$info ) {
            oauthjson(90001,'未获取到数据');
            exit;
        }

        oauthjson(200,'获取成功',$info);
        exit;
    }

    /**
     * 血氧数据分析
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
        $info   =   D('El')->getinfo($id);

        if ( !$info ) {
            oauthjson(90002,'未查询到数据');
            exit;
        }

        // 判断检测数据是否可展示
        $this->checkdatainfo( $info['drugid'] ,$this->client_id );
        
        // 读取接口，进行数据分析
        $postdata               =   array();
        $postdata['bpm']         =   $info['bpm'];

        $analysis   =   new \Oauth\Common\analysis();
        $result     =   $analysis->result($postdata,'el');
        if ( !$result || $result['status'] == 'resultError' ) {
            oauthjson(90003,'未分析出结果');
            exit;
        }

        $data   =   array();
        $data['bpm']['title']       =   $result['title'];
        $data['bpm']['state']       =   $result['state'];
        $data['bpm']['clinical']    =   $result['clinical'];
        $data['bpm']['result']      =   $result['result'];
        $data['bpm']['danger']      =   $result['danger'];
        $data['bpm']['tests']       =   $result['data']['tests'];

        $ainfo  =   array();
        // 查看心电数据是否有解读
        if ( $info['isanswer'] && $info['isanswer'] == 1 ) {
            $ainfo  =   D('El')->getanswer($id);
        }

        $data['hr']['data']   =   $info['hr'];
        $data['hr']['title']  =   $ainfo ? $ainfo['title'] : '';
        $data['hr']['thumb']  =   $ainfo ? $ainfo['thumb'] : '';
        $data['hr']['addtime']=   $ainfo ? $ainfo['addtime'] : '';
        $data['hr']['content']=   $ainfo ? $ainfo['content'] : '';

        oauthjson(200,'获取成功',$data);
        exit;
    }
}

