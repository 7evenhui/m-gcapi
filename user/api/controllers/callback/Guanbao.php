<?php

defined('BASEPATH') or exit('No direct script access allowed');
//引用公用文件
include_once __DIR__.'/Publicpay.php';

class Guanbao extends Publicpay
{
    //redis错误标识名称
    protected $r_name = 'GUANBAO';
    //商户处理后通知第三方接口响应信息
    protected $success = "success"; //成功响应
    //异步返回必需验证参数
    protected $sf = 'sign'; //签名参数
    protected $of = 'orderIdCp'; //订单号参数
    protected $mf = 'money'; //订单金额参数(实际支付金额)
    protected $vt = 'fen';//金额单位
    protected $tf = 'status'; //支付状态参数字段名
    protected $tc = 0; //支付状态成功的值
    protected $ks = '&'; //参与签名字符串连接符
    protected $mt = 'D'; //返回签名是否大写 D/X
    protected $vs = ['money','orderIdCp','version']; //参数签名字段必需参数

    public function __construct()
    {
        parent::__construct();
    }

    public function verifySign($data, $key, $name){
        $sign = $data[$this->sf];
        $vSignData = [
            'money' => $data['money'],
            'orderIdCp' => $data['orderIdCp'],
            'version' => $data['version'],
        ];
        $vSignStr = data_to_string($vSignData).$this->ks.$key;
        $vSign = strtoupper(md5($vSignStr));
        if ($vSign != $sign){
            $msg = "签名验证失败:{$sign}";
            $this->PM->online_erro($name,$msg);
            exit($this->error);
        }
    }
}
