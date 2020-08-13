<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\components\jingdong\JdClient;
use common\components\jingdong\request\B2bOrderGetRequest;
use common\components\jingdong\request\PopOrderGetRequest;
use common\components\jingdong\request\PopOrderEnGetRequest;
use common\components\jingdong\request\OrderVenderRemarkQueryByOrderIdRequest;
use common\components\jingdong\request\PopOrderPrintDataGetRequest;
use common\components\jingdong\request\OrderGetRequest;



/**
 * 京东API
 * Class JdSdk
 * @package common\components
 * @author gaopeng
 */
class JdSdk extends Component
{
   
    public $appKey;
    
    public $appSecret;
    
    public $accessToken;
    
    private $client;   
    
    public function init()
    {
        if(!$this->client) {
            $this->client = new JdClient();
        }
        $this->client->appKey = $this->appKey;
        $this->client->appSecret = $this->appSecret;
        $this->client->accessToken = $this->accessToken;
        parent::init();
    }
    /**
     * 
     * @param unknown $order_no
     * @param unknown $customKeys
     * MAIN, 查询订单主信息 
     * SKU, 查询订单商品 
     * CONSIGNEE, 查询订单收货人信息 
     * INVOICE，查询订单发票信息
     * SNAPSHOT, 查询订单快照
     * ADDITIONAL_PAY，查询附加支付
     * SUIT, 查询套装
     * EXT_INFO, 订单扩展信息
     */
    public function getOrderInfo($jdOrderId,$customKeys = ['MAIN','SKU','CONSIGNEE','INVOICE']) 
    {
        /* $reuqest = new B2bOrderGetRequest();
        $reuqest->setJdOrderId($jdOrderId);
        $reuqest->setCustomKeys($customKeys); */
        
         $reuqest = new OrderGetRequest();
        $reuqest->setOrderId($jdOrderId);
        $reuqest->setOptionalFields(['orderId']); 
        
        $res = $this->client->execute($reuqest, $this->accessToken);
        print_r($res);
    }
    
    
}