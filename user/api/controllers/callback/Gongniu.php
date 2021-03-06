<?php
/**
 * 公牛支付回调模板
 * Created by sublim Text3
 * User: lqh6249
 * Date: 2018/07/30
 * Time: 10:58
 */
defined('BASEPATH') or exit('No direct script access allowed');
//引用公用文件
include_once __DIR__.'/Publicpay.php';

class Gongniu extends Publicpay
{
    //redis错误标识名称
    protected $r_name = 'GONGNIU';
    //商户处理后通知第三方接口响应信息
    protected $success = "success"; //成功响应
    //异步返回必需验证参数
    protected $sf = 'merchant_sign'; //签名参数
    protected $of = 'merchant_order_no'; //订单号参数
    protected $mf = 'merchant_amount'; //订单金额参数(实际支付金额)
    protected $vm = 0;//是否验证金额(部分第三方实际支付金额不一致)
    protected $vs = ['merchant_code','merchant_amount_orig']; //参数签名字段必需参数

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 验证签名
     * @access protected
     * @param Array $data   回调参数数组
     * @param String $key 秘钥
     * @return boolean $name 错误标识
     */
    protected function verifySign($data,$key,$name)
    {
        //获取签名字段并删除不参与签名字段
        $sign = $data[$this->sf];
        unset($data[$this->sf]);
        //获取签名字符串
        $sdata = array(
            'merchant_code' => $data['merchant_code'],
            'merchant_order_no' => $data['merchant_order_no'],
            'merchant_amount' => $data['merchant_amount'],
            'merchant_amount_orig' => $data['merchant_amount_orig'],
            'merchant_md5' => $key
        );
        $string = ToUrlParams($sdata);
        $v_sign = base64_encode(md5($string));
        //验证签名是否正确
        if (strtoupper($sign) <> strtoupper($v_sign))
        {
            $this->PM->online_erro($name, '签名验证失败:' . $sign);
            exit($this->error);
        }
    }
}
