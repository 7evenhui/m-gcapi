<?php

defined('BASEPATH') or exit('No direct script access allowed');
//引用公用文件
include_once __DIR__.'/Publicpay.php';

class Chuizi extends Publicpay
{
    //redis错误标识名称
    protected $r_name = 'CHUIZI';
    //商户处理后通知第三方接口响应信息
    protected $success = "success"; //成功响应
    //异步返回必需验证参数
    protected $sf = 'sign'; //签名参数
    protected $of = 'out_trade_no'; //订单号参数
    protected $mf = 'total_amount'; //订单金额参数(实际支付金额)
    protected $tf = 'trade_status'; //支付状态参数字段名
    protected $tc = '200'; //支付状态成功的值
    protected $ks = '&key='; //参与签名字符串连接符
    protected $mt = 'X'; //返回签名是否大写 D/X

    public function __construct()
    {
        parent::__construct();
    }
}
