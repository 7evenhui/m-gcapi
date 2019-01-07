<?php
/**
 * 千应支付接口调用 (更新)
 * User: lqh
 * Date: 2018/08/13
 * Time: 16:30
 */
defined('BASEPATH') or exit('No direct script access allowed');
//引用公用文件
include_once  __DIR__.'/Publicpay_model.php';

class Qianying_model extends Publicpay_model
{
    protected $c_name = 'qianying';
    private $p_name = 'QIANYING';//商品名称

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
        return $this->buildForm($data);
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
        $sign_data = array_values($data);
        $sign_string = implode('',$sign_data);
        $data['key'] = md5($sign_string);
        return $data;
    }

    /**
     * 构造支付基本参数
     * @return array
     */
    private function getBaseData()
    {
        $data['uid'] = $this->merId;
        $data['price'] = $this->money;
        $data['istype'] = $this->getPayType();
        $data['notify_url'] = $this->callback;
        $data['return_url'] = $this->returnUrl;
        $data['orderid'] = $this->orderNum;
        $data['orderuid'] = $this->user['id'];
        $data['goodsname'] = $this->p_name;
        $data['token'] = $this->key;
        return $data;
    }

    /**
     * 根据code值获取支付方式
     * @param string code
     * @return string 聚合付支付方式 参数
     */
    private function getPayType()
    {
        switch ($this->code)
        {
            case 1:
            case 2:
                return '2';//微信扫码WAP
                break;
            case 4:
            case 5:
                return '1';//支付宝扫码WAP
                break;
            default:
                return '1';//微信扫码
                break;
        }
    }
}