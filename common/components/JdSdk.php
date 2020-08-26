<?php

namespace common\components;

use Yii;
use yii\base\Component;
use JD\JdClient;
use JD\request\PopOrderGetRequest;
use JD\request\PopOrderSearchRequest;
use ACES\TDEClient;



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
    
    public $refreshToken;
    
    private $client;
    public function init()
    {
        if(!$this->client) {
            $this->client = new JdClient();
        }
        $this->client->appKey = $this->appKey;
        $this->client->appSecret = $this->appSecret; 
        //获取access _token
        if($this->refreshToken) {
            $url = "https://auth.360buy.com/oauth/token?grant_type=refresh_token&client_id=".$this->appKey."&client_secret=".$this->appSecret."&refresh_token=".$this->refreshToken;
            $jsonData = file_get_contents($url);
            $data = json_decode($jsonData,true);
            if(empty($data['access_token'])) {
                throw new \Exception("access_token error");
            }else {
                $this->accessToken = $data['access_token'];
                $this->client->accessToken = $this->accessToken;
            }
        }        
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
    public function getOrderList($start_date = null, $end_date = null ,$page = 1, $page_size = 20)
    {
        $request = new PopOrderSearchRequest();
        //1）WAIT_SELLER_STOCK_OUT 等待出库 2）WAIT_GOODS_RECEIVE_CONFIRM 等待确认收货   5）FINISHED_L 完成 
        $order_state = 'WAIT_SELLER_STOCK_OUT,WAIT_GOODS_RECEIVE_CONFIRM,FINISHED_L';
        $request->setOrderState($order_state);
        $option_fields = 'orderId,orderTotalPrice,orderSellerPrice,orderPayment,freightPrice,sellerDiscount,orderState,deliveryType,invoiceEasyInfo,orderRemark,orderStartTime,orderEndTime,consigneeInfo,itemInfoList,orderExt,paymentConfirmTime,logisticsId,waybill,venderRemark';
        $request->setOptionalFields($option_fields);
        $request->setPage($page);
        $request->setPageSize($page_size);
        $request->setStartDate($start_date);        
        $request->setEndDate($end_date);
        //排序方式，默认升序,1是降序,其它数字都是升序
        $request->setSortType(0);
        //查询时间类型，0按修改时间查询，1为按订单创建时间查询；其它数字同0，也按订单修改（订单状态、修改运单号）修改时间
        $request->setDateType(1);// 1订单创建时间，0订单修改时间
        $responce = $this->client->execute($request, $this->accessToken);

        if(isset($responce->error_response)){
            throw new \Exception($responce->error_response->zh_desc);
        }  
        
        $result = $responce->jingdong_pop_order_search_responce->searchorderinfo_result;        
        $order_count = $result->orderTotal;
        $page_count = floor($order_count/$page_size);
        if($result->orderTotal == 0) {
            if($page <= $page_count) {
                throw new \Exception($result->apiResult->chineseErrCode);
            }else{
                return [];
            }            
        }
        $tde = TDEClient::getInstance($this->accessToken, $this->appKey, $this->appSecret);
        $order_list = $result->orderInfoList;
        foreach ($order_list as & $order) {
            $order->consigneeInfo->fullAddress = $tde->decrypt($order->consigneeInfo->fullAddress);
            $order->consigneeInfo->telephone= $tde->decrypt($order->consigneeInfo->telephone);
            $order->consigneeInfo->fullname= $tde->decrypt($order->consigneeInfo->fullname);
            $order->consigneeInfo->mobile= $tde->decrypt($order->consigneeInfo->mobile);
        }
        return [$order_list,$page,$page_count,$order_count];
    }
    
    
}