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

    protected $_url;

    protected $_url_code;

    protected $_param_login=[
        'func'=>'login-session',
        'login_source'=>'bor-info',
        'bor_id'=>'',//学号
        'bor_verification'=>'',//密码
        'bor_library'=>'SCU50',
    ];

    protected $_param_loan_now=[
        'func'=>'bor-loan',
        'adm_library'=>'SCU50'
    ];

    protected $_curl;

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
            'author'=>['td:eq(2)','text'],
            'title'=>['td:eq(3)','text'],
            'end_time'=>['td:eq(5)','text'],
            'money'=>['td:eq(6)','text'],
            'address'=>['td:eq(7)','text'],
            'book_id'=>['td:eq(8)','text'],
        ];

        $data=QueryList::Query($page,$rule,'center table:eq(2) tr')->data;
        if(strlen($data[0]['end_time'])!=8)
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

        $strlen =strlen($str);
        while($length > $strlen){
            $str .= $str;
            $strlen *= 2;
        }
        $str = str_shuffle($str); //随机打乱字符串
        return substr($str, 0,$length);
    }
}