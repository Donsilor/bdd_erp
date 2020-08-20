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
use common\components\jingdong\request\PopOrderSearchRequest;



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
    public function getOrderInfo($orderId) 
    {
        $reuqest = new PopOrderGetRequest();
        $reuqest->setOrderId($orderId);
        $reuqest->setOptionalFields(['orderId']); 
        
        $res = $this->client->execute($reuqest, $this->accessToken);
        print_r($res);
    }
    /**
     * 订单列表
     * @param number $page
     * @param number $page_size
     * @param unknown $start_date
     * @param unknown $end_date
     */
    public function getOrderList($start_date, $end_date, $page = 1 ,$page_size = 20)
    {
        $option_fields = [
            'orderId','orderTotalPrice','orderSellerPrice','orderPayment','freightPrice',
            'sellerDiscount','orderState','deliveryType','invoiceEasyInfo','orderRemark','orderStartTime',
            'orderEndTime','consigneeInfo','itemInfoList','orderExt','paymentConfirmTime'            
        ];
        $request = new PopOrderSearchRequest();
        //1）WAIT_SELLER_STOCK_OUT 等待出库 2）WAIT_GOODS_RECEIVE_CONFIRM 等待确认收货   5）FINISHED_L 完成 
        $request->setOrderState(['WAIT_SELLER_STOCK_OUT','WAIT_GOODS_RECEIVE_CONFIRM','FINISHED_L']);
        $request->setOptionalFields($option_fields);
        $request->setPage($page);
        $request->setPageSize($page_size);
        $request->setStartDate($start_date);        
        $request->setEndDate($end_date);
        //排序方式，默认升序,1是降序,其它数字都是升序
        $request->setSortType(0);
        //查询时间类型，0按修改时间查询，1为按订单创建时间查询；其它数字同0，也按订单修改（订单状态、修改运单号）修改时间
        $request->setDateType(1);// 1订单创建时间，0订单修改时间
        $res = $this->client->execute($request, $this->accessToken);
        print_r($res);
        
    }
    
    
}