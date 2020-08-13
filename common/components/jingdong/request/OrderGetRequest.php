<?php
namespace common\components\jingdong\request;
class OrderGetRequest
{
     
    private $apiParas = array();
    
    public function getApiMethodName(){
        return "360buy.order.get";
    }
    
    public function getApiParas(){
        if(empty($this->apiParas)){
            return "{}";
        }
        return json_encode($this->apiParas);
    }    
    public function check(){
        
    }
    
    public function putOtherTextParam($key, $value){
        $this->apiParas[$key] = $value;
        $this->$key = $value;
    }
    /**
     * @var 可选字段
     */
    private $optionalFields;
    public function setOptionalFields($optionalFields)
    {
        $this->optionalFields = $optionalFields;
    }

    public function getOptionalFields()
    {
        return $this->optionalFields;
    }
    /**
     * @var 订单id
     */
    private $orderId;
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
    /**
     * @var 订单状态
     */
    private $orderState;
    public function setOrderState($orderState)
    {
        $this->orderState = $orderState;
    }
    public function getOrderState()
    {
        return $this->orderState;
    }
    
    private  $version; 
    public function setVersion($version){
        $this->version = $version;
    }
    
    public function getVersion(){
        return $this->version;
    }


}