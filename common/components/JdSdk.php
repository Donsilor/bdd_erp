<?php

namespace common\components;

use Yii;
use yii\base\Component;
use JD\JdClient;
use JD\request\PopOrderGetRequest;
use JD\request\PopOrderSearchRequest;
use ACES\TDEClient;
use JD\request\SkuReadSearchSkuListRequest;
use JD\request\NewWareAttributesQueryRequest;
use JD\request\NewWareAttributeGroupsQueryRequest;
use JD\request\SkuReadFindSkuByIdRequest;
use JD\request\WareReadSearchWare4ValidRequest;
use JD\request\VenderCategoryGetFullValidCategoryResultByVenderIdRequest;
use JD\request\CategoryReadFindAttrsByCategoryIdUnlimitCateRequest;



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
        echo $this->appKey.PHP_EOL;
        echo $this->appSecret.PHP_EOL;
        echo $this->accessToken.PHP_EOL;
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
    public function getOrderList($start_date = null, $end_date = null ,$page = 1, $order_type = 1)
    {
        $page_size = 20;
        $request = new PopOrderSearchRequest();
        //1）WAIT_SELLER_STOCK_OUT 等待出库 2）WAIT_GOODS_RECEIVE_CONFIRM 等待确认收货   5）FINISHED_L 完成 
        $order_state = 'WAIT_SELLER_STOCK_OUT,WAIT_GOODS_RECEIVE_CONFIRM,FINISHED_L';
        $request->setOrderState($order_state);
        $option_fields = 'orderId,orderTotalPrice,orderSellerPrice,orderPayment,freightPrice,sellerDiscount,orderState,deliveryType,invoiceEasyInfo,invoiceInfo,invoiceCode,salesPin,open_id_buyer,open_id_seller,orderRemark,orderStartTime,orderEndTime,consigneeInfo,itemInfoList,orderExt,paymentConfirmTime,logisticsId,waybill,venderRemark,vatInfo,couponDetailList';
        $request->setOptionalFields($option_fields);
        $request->setPage($page);
        $request->setPageSize(20);
        $request->setStartDate($start_date);        
        $request->setEndDate($end_date);
        //排序方式，默认升序,1是降序,其它数字都是升序
        $request->setSortType(0);
        //查询时间类型，0按修改时间查询，1为按订单创建时间查询；其它数字同0，也按订单修改（订单状态、修改运单号）修改时间
        $request->setDateType($order_type);// 1订单创建时间，0订单修改时间
        $responce = $this->client->execute($request, $this->accessToken);

        if(isset($responce->error_response)){
            throw new \Exception($responce->error_response->zh_desc);
        }  
        
        $result = $responce->jingdong_pop_order_search_responce->searchorderinfo_result;        
        $order_count = $result->orderTotal;
        $page_count = floor($order_count/$page_size);
        if($result->orderTotal == 0 && !$result->apiResult->success) {
            throw new \Exception($result->apiResult->chineseErrCode);         
        }
        $tde = TDEClient::getInstance($this->accessToken, $this->appKey, $this->appSecret);
        $order_list = $result->orderInfoList;
        foreach ($order_list as & $order) {
            if($order->consigneeInfo) {
                $order->consigneeInfo->fullAddress = $tde->decrypt($order->consigneeInfo->fullAddress);
                $order->consigneeInfo->telephone= $tde->decrypt($order->consigneeInfo->telephone);
                $order->consigneeInfo->fullname= $tde->decrypt($order->consigneeInfo->fullname);
                $order->consigneeInfo->mobile= $tde->decrypt($order->consigneeInfo->mobile);
            }
            if($order->invoiceEasyInfo) {
                if(isset($order->invoiceEasyInfo->invoiceTitle)) {
                    $order->invoiceEasyInfo->invoiceTitle = $tde->decrypt($order->invoiceEasyInfo->invoiceTitle);
                }
                if(isset($order->invoiceEasyInfo->invoiceConsigneePhone)) {
                    $order->invoiceEasyInfo->invoiceConsigneePhone = $tde->decrypt($order->invoiceEasyInfo->invoiceConsigneePhone);
                }
                if(isset($order->invoiceEasyInfo->invoiceConsigneeEmail)) {
                    $order->invoiceEasyInfo->invoiceConsigneeEmail = $tde->decrypt($order->invoiceEasyInfo->invoiceConsigneeEmail);
                }
            }
        }
        return [$order_list,$page_count];
    }
    /**
     * SKU信息查询
     * @param unknown $skuIds
     */
    public function getWareList($wareIds)
    {
        $request = new WareReadSearchWare4ValidRequest();
        $option_fields = 'wareId,wareStatus,features,multiCateProps,multiCategoryId';
        $request->setField($option_fields); 
        $request->setWareStatusValue("1,2,4,8,513,513,514,516,520,1028");
        $request->setWareId($wareIds);
        $request->setPageNo(1);
        $request->setPageSize(20);
        $responce = $this->client->execute($request, $this->accessToken);
        if(isset($responce->error_response)){
            throw new \Exception($responce->error_response->zh_desc);
        }
        return $responce->jingdong_ware_read_searchWare4Valid_responce->page->data ?? [];      
    }
    
    /**
     * SKU信息查询
     * @param unknown $skuIds
     */
    public function getSkuList($skuIds)
    {
        $request = new SkuReadSearchSkuListRequest();
        $option_fields = 'skuId,skuName,status,categoryId,saleAttrs,features,multiCateProps,multiCategoryId';
        $request->setField($option_fields);
        $request->setSkuId($skuIds);
        $request->setSkuStatuValue("1,2,4");
        $request->setPageNo(1);
        $request->setPageSize(30);
        $responce = $this->client->execute($request, $this->accessToken);
        if(isset($responce->error_response)){
            throw new \Exception($responce->error_response->zh_desc);
        }
        return $responce->jingdong_sku_read_searchSkuList_responce->page->data ?? [];
    }
    /**
     * 查询类目列表
     */
    public function getCateList()
    {
        $key = "JdCates:0";
        if($data = \Yii::$app->cache->get($key)) {
            return $data;
        }
        $request = new VenderCategoryGetFullValidCategoryResultByVenderIdRequest();
        $responce = $this->client->execute($request, $this->accessToken);
        if(isset($responce->error_response)){
            throw new \Exception($responce->error_response->zh_desc);
        }
        $data = $responce->jingdong_vender_category_getFullValidCategoryResultByVenderId_responce->returnType->list ?? [];
        if($data) {
            \Yii::$app->cache->set($key,$data);
        }
        return $data;
    }
    /**
     * 查询属性列表
     * @param int $cate_id
     */
    public function getAttrList($cate_id) 
    {
        $key = "JdAttrs:".$cate_id;
        if($data = \Yii::$app->cache->get($key)) {
            return $data;
        }
        $request = new CategoryReadFindAttrsByCategoryIdUnlimitCateRequest();
        $request->setField('id,name,attrValueList');
        $request->setCid($cate_id);
        $responce = $this->client->execute($request, $this->accessToken);
        if(isset($responce->error_response)){
            throw new \Exception($responce->error_response->zh_desc);
        }
        $data = $responce->jingdong_category_read_findAttrsByCategoryIdUnlimitCate_responce->findattrsbycategoryidunlimitcate_result ?? [];
        if($data) {
            \Yii::$app->cache->set($key,$data);
        }
        return $data;
    }
    
    
}