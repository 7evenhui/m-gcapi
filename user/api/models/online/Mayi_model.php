<?php
/**
 * 蚂蚁金服支付接口调用
 * User: lqh
 * Date: 2018/07/10
 * Time: 17:45
 */
defined('BASEPATH') or exit('No direct script access allowed');
//引用公用文件
include_once  __DIR__.'/Publicpay_model.php';

class Mayi_model extends Publicpay_model
{
    protected $c_name = 'mayi';
    private $p_name = 'MAYI';//商品名称
    //参与签名参数
    private $ks = '&';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取前端返回数据 部分第三方支付不一样
     * @param array
     * @return array
     */
    protected function returnApiData($data)
    {
        //wap支付
        if (in_array($this->code,[5]))
        {
            return $this->buildWap($data);
            //扫码支付
        } elseif (in_array($this->code,[4])) {
            return $this->buildScan($data);
            //h5
        } else {
            return $this->buildForm($data);
        }
    }

    /**
     * 构造支付参数+sign值
     * @return array
     */
    protected function getPayData()
    {
        //构造基本参数
        $data = $this->getBaseData();
        //构造签名参数
        ksort($data);
        $k = $this->ks . $this->key;
        $string = ToUrlParams($data) . $k;
        $data['sign'] = md5($string);
        return $data;
    }

    /**
     * 构造支付基本参数
     * @return array
     */
    private function getBaseData()
    {
        if (in_array($this->code,[36]))
        {
            $data['transType'] = 'G006';
        } else {
            $data['transType'] = 'G004';
        }
        $data['merchantId'] = $this->merId;//商户号
        $data['money'] = yuan_to_fen($this->money);
        $data['orderNo'] = $this->orderNum;
        $data['tradetime'] = date('YmdHis');
        $data['notifyUrl'] = $this->callback;
        $data['joinType'] = $this->getPayType();
        return $data;
    }

    /**
     * 获取支付网关地址 部分接口地址不唯一
     * @param array $pay 支付参数
     * @return array
     */
    protected function getPayUrl($pay)
    {
        $pay_url = '';
        if (isset($pay['pay_url'])) 
        {
            $pay_url = trim($pay['pay_url']);
        }
        if (in_array($this->code,[36]))
        {
            $pay_url .= "/gateWayWap";
        } else {
            $pay_url .= "/gateWay"; 
        }
        return $pay_url;
    }

    /**
     * 根据code值获取支付方式
     * @param string code
     * @return string 支付方式 参数
     */
    private function getPayType()
    {
        switch ($this->code)
        {
            case 4:
                return '0';//支付宝
                break;
            case 5:
                return '1';//支付宝WAP
                break;
            case 36:
                return '2';//H5支付宝WAP
                break;
            default:
                return '1';//支付宝
                break;
        }
    }

    /**
     * 获取支付结果
     * @param $data 支付参数
     * @return return 二维码内容
     */
    protected function getPayResult($pay_data)
    {
        //传递参数
        $pay_data = http_build_query($pay_data);
        $data = post_pay_data($this->url,$pay_data);
        if (empty($data)) $this->retMsg('接口返回信息错误！');
        //接收参数为JSON格式 转化为数组
        $data = json_decode($data,true);
        if (empty($data)) $this->retMsg('接口返回信息格式错误！');
        //判断是否下单成功
        if (empty($data['qrCodeUrl']))
        {
            $msg = isset($data['respMsg']) ? $data['respMsg'] : '返回信息错误';
            $this->retMsg("下单失败：{$msg}");
        }
        //返回支付连接或二维码地址
        return $data['qrCodeUrl'];
    }
}
