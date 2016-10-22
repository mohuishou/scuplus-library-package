<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/8/21 0021
 * Time: 19:31
 */
namespace Mohuishou\Lib;
use Curl\Curl;
use QL\QueryList;

class Library{

    /**
     * 图书馆可用url
     * @var
     */
    protected $_url;

    /**
     * url参数-登录
     * @var array
     */
    protected $_param_login=[
        'func'=>'login-session',
        'login_source'=>'bor-info',
        'bor_id'=>'',//学号
        'bor_verification'=>'',//密码
        'bor_library'=>'SCU50',
    ];

    /**
     * url参数-当前借阅
     * @var array
     */
    protected $_param_loan_now=[
        'func'=>'bor-loan',
        'adm_library'=>'SCU50'
    ];

    /**
     * url参数-借阅历史
     * @var array
     */
    protected $_param_loan_histroy=[
        'func'=>'bor-history-loan',
        'adm_library'=>'SCU50'
    ];



    /**
     * curl对象
     * @var Curl
     */
    protected $_curl;

    /**
     * 初始化，获得已登录的图书馆链接，以便下一步操作
     * Library constructor.
     * @param $sid
     * @param $password
     */
    public function __construct($sid,$password)
    {
        $this->_curl=new Curl();

        //设置header伪造来源以及ip
        $ip=rand(1,233).'.'.rand(1,233).'.'.rand(1,233).'.'.rand(1,233);
        $this->_curl->setHeader("X-Forwarded-For",$ip);
        $this->_curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36)');
        $this->_curl->setHeader("Referer",'http://opac.scu.edu.cn:8080');

        //获取可用的地址，并重新设置header
        $this->getUrl();
        $this->_curl->setHeader("Referer",$this->_url);

        //登录
        $this->login($sid,$password);
    }

    //续借部分
    public function loanSome($id){
        $param_loan_some=[
            "func"=>"bor-renew-all",
            "renew_selected"=>"Y",
            "adm_library"=>"SCU50"
        ];
        $param_loan_some[$id]="Y";

        $this->_curl->get($this->_url,$param_loan_some);
        if($this->_curl->error){
            throw new \Exception('续借失败！');
        }

        $page=$this->_curl->response;
        $rule=[
            'result'=>['.title','text','',function($content){
                $res=preg_match_all("/不成功/",$content);
                if($res){
                    $res=0;
                }else{
                    $res=1;
                }
                return $res;
            }],
            "reason"=>['table:eq(1) tr:eq(1) td:eq(8)','text']
        ];
        $data=QueryList::Query($page,$rule,'center')->data;
        return $data[0];
    }

    //一键续借（续借全部）
    public function loanAll(){
        $param_loan_all=[
            "func"=>"bor-renew-all",
            "adm_library"=>"SCU50"
        ];
        $this->_curl->get($this->_url,$param_loan_all);
        if($this->_curl->error){
            throw new \Exception('续借失败！');
        }

        $page=$this->_curl->response;
        $rule=[
            'result'=>['.title','text','',function($content){
                $res=preg_match_all("/不成功/",$content);
                if($res){
                    $res=0;
                }else{
                    $res=1;
                }
                return $res;
            }],
            "reason"=>['table:eq(1) tr:eq(1) td:eq(8)','text']
        ];
        $data=QueryList::Query($page,$rule,'center')->data;

        return $data[0];
    }

    /**
     * 借阅历史
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     * @throws \Exception
     */
    public function loanHistory(){
        $this->_curl->get($this->_url,$this->_param_loan_histroy);
        if($this->_curl->error){
            throw new \Exception('获取当前借阅记录出错！');
        }

        $page=$this->_curl->response;

        $rule=[
            'author'=>['td:eq(1)','text'],
            'title'=>['td:eq(2)','text'],
            'start_day'=>['td:eq(4)','text'],
            'end_day'=>['td:eq(5)','text'],
            'end_time'=>['td:eq(6)','text'],
            'money'=>['td:eq(7)','text'],
            'address'=>['td:eq(8)','text'],
        ];

        $data=QueryList::Query($page,$rule,'center table:eq(1) tr')->data;

        if(strlen($data[0]['end_day'])!=8)
            unset($data[0]);
        return $data;
    }

    /**
     * 当前借阅
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     * @throws \Exception
     */
    public function loanNow(){
        $this->_curl->get($this->_url,$this->_param_loan_now);
        if($this->_curl->error){
            throw new \Exception('获取当前借阅记录出错！');
        }

        $page=$this->_curl->response;

        $rule=[
            'review_id'=>['td:eq(1) input','name'],
            'author'=>['td:eq(2)','text'],
            'title'=>['td:eq(3)','text'],
            'end_day'=>['td:eq(5)','text'],
            'money'=>['td:eq(6)','text'],
            'address'=>['td:eq(7)','text'],
            'book_id'=>['td:eq(8)','text'],
        ];

        $data=QueryList::Query($page,$rule,'center table:eq(2) tr')->data;
        if(strlen($data[0]['end_day'])!=8)
            unset($data[0]);
        return $data;
    }

    /**
     * 登录
     * @author mohuishou<1@lailin.xyz>
     * @param $sid
     * @param $password
     * @throws \Exception
     */
    protected function login($sid,$password){
        $this->_param_login['bor_id']=$sid;
        $this->_param_login['bor_verification']=$password;
        $this->_curl->post($this->_url,$this->_param_login);
        if($this->_curl->error){
            throw new \Exception('登录时错误！');
        }

        $page=$this->_curl->response;
        $rule=[
            'fb'=>['#feedbackbar','text']
        ];
        $data=QueryList::Query($page,$rule)->data;
        $res=$data[0]['fb'];
        $res=preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($res));
        if(!empty($res)&&$res!='　')
            throw new \Exception($res);
    }

    /**
     * 获取操作地址
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    protected function getUrl(){
        //构造一个相同类型的随机URL
        $str=$this->random_str(51);
        $url='http://opac.scu.edu.cn:8080/F/'.$str.'-052'.rand(0,9).rand(0,9);
        $this->_curl->get($url);
        if($this->_curl->error){
            throw new \Exception('获取图书馆登录地址错误！');
        }
        $page=$this->_curl->response;

        //从结果当中抓取真实地址
        $rule=[
            'url'=>['#header a:eq(0)','href']
        ];
        $data=QueryList::Query($page,$rule)->data;
        $real_url=$data[0]['url'];
        $real_url=explode('?',$real_url);
        if(empty($real_url[0])){
            throw new \Exception('获取图书馆登录地址错误！');
        }
        $this->_url=$real_url[0];
    }

    /**
     * 生成随机字符串
     * @author mohuishou<1@lailin.xyz>
     * @param $length
     * @return string
     */
    protected function random_str($length)
    {
        $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $str_len =strlen($str);
        while($length > $str_len){
            $str .= $str;
            $str_len *= 2;
        }
        $str = str_shuffle($str); //随机打乱字符串
        return substr($str, 0,$length);
    }
}