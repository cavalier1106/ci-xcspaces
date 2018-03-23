<?php
/**
 * User: xiaoqing Email: liuxiaoqing437@gmail.com
 * Date: 13-11-8
 * Time: 上午10:24
 * 短信接口类
 */
class sendSMS
{
    /**
     * @var string 企业ID
     */
    private $corpid;
    /**
     * @var string 账号名称
     */
    private $name;
    /**
     * @var string 密码
     */
    private $password;
    /**
     * @var mixed 发送内容
     */
    private $content;
    /**
     * @var string 发送url
     */
    private $sendurl;
    /**
     * @var mixed 手机号码
     */
    private $phone;

    /**
     * 初始化函数
     **/
    public function __construct()
    {
        $this->corpid = 'IS001-1867';
        $this->name = iconv('UTF-8', 'GB2312', '捷游软件');
        $this->password = "111111";
        $this->sendurl = "http://211.147.242.161:8080/";
    }

    /**
     * 设置发送内容和发送手机号码
     * @param string $con 内容
     * @param $phone 手机号码
     */
    public function set_content_mobile($con='', $phone)
    {
        $this->content=iconv('UTF-8', 'GB2312//TRANSLIT//IGNORE', $con);
        $this->phone=$phone;
    }

    /**
     * 发送短信
     * @return mixed|string
     */
    public function send_message()
    {
        $params = array(
            'CORPID' => $this->corpid,
            'USERNAME' => $this->name,
            'PASSWORD' => $this->password,
            'MOBILE' => $this->phone,
            'CONTENT' => $this->content,
            'EXTNO' => '',
        );
        $query = http_build_query($params);
        if(function_exists('file_get_contents'))
        {
            $file_contents = file_get_contents($this->sendurl.'?'.$query);
        }else{
            $file_contents = Net::fetch($this->sendurl,array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $query, //注意CURL无法转换数组成为name[]=value&这种格式
                CURLOPT_TIMEOUT => 10,
            ), $http_code);
        }
        $code = explode(':',$file_contents);

        return $this->getStatus($code[0]);
    }

    /**
     * 用户短信余量
     * @return mixed|string
     */
    public function searchnNum()
    {
        $params = array(
            'CORPID' => $this->corpid,
            'USERNAME' => $this->name,
            'PASSWORD' => $this->password,
        );
        $query = http_build_query($params);
        if(function_exists('file_get_contents'))
        {
            $file_contents = file_get_contents($this->sendurl.'Balance.asp?'.$query);
        }else{
            $file_contents = Net::fetch($this->sendurl,array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $query, //注意CURL无法转换数组成为name[]=value&这种格式
                CURLOPT_TIMEOUT => 10,
            ), $http_code);
        }
        //错误返回状态码，成功返回短信剩余条数记录
        if((int)$file_contents < 0)
        {
            return $this->getStatus($file_contents);
        } else {
            return $file_contents;
        }
    }

    /**
     * 接收状态报告接口
     */
    public function getReport()
    {
        $params = array(
            'CORPID' => $this->corpid,
            'USERNAME' => $this->name,
            'PASSWORD' => $this->password,
        );
        $query = http_build_query($params);
        if(!function_exists('file_get_contents'))
        {
            $file_contents = file_get_contents($this->sendurl.'Mo.asp?'.$query);
        }else{
            $file_contents = Net::fetch($this->sendurl.'Mo.asp',array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $query, //注意CURL无法转换数组成为name[]=value&这种格式
                CURLOPT_TIMEOUT => 10,
            ), $http_code);
        }
        return $file_contents;
    }

    private function getStatus($code)
    {
        $stauts_lsit = array(
            '1' => '发送成功',
            '-1' => '访问数据库写入数据错误',
            '-3' => '一次发送的手机号码过多',
            '-4' => '内容包含不合法文字',
            '-5' => '登录账户错误',
            '-9' => '手机号码不合法黑名单',
            '-10' => '号码太长不能超过100条一次提交',
            '-11' => '内容太长',
            '-13' => '余额不足',
            '-14' => '子号不正确',
            '-999' => '参数不全',
        );

        return $stauts_lsit[$code];
    }
}
