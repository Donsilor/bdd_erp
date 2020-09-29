<?php

namespace addons\Sales\common\forms;


use Yii;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\BaseModel;
use addons\Sales\common\models\Customer;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderAddress;
use addons\Sales\common\models\OrderInvoice;
use addons\Sales\common\models\OrderAccount;

/**
 * 订单 Form
 */
class OrderFullForm extends BaseModel
{
    
    /**
     * 订单单头model
     * @var Order
     */
    public $order;
    /**
     * 订单金额model
     * @var OrderAccount
     */
    public $account;
    /**
     * 订单明细model
     * @var OrderGoods
     */
    public $goods;
    /**
     * 订单明细列表
     * @var array
     */
    public $goods_list;
    
    /**
     * 订单客户model
     * @var Customer
     */
    public $customer;
    /**
     * 订单收货地址model
     * @var OrderAddress
     */
    public $address;
    
    /**
     * 订单发票model
     * @var OrderInvoice
     */
    public $invoice;
    //订单模式  外部订单同步/订单导入
    public $mode;
       
}
