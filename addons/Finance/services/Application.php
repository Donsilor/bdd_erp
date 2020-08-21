<?php

namespace addons\Finance\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Finance\services
 * @property \addons\Finance\services\BankPayService $bankPay
 * @property \addons\Finance\services\BorrowPayService $borrowPay
 * @property \addons\Finance\services\ContractPayService $contractPay
 * @property \addons\Finance\services\OrderPayService $orderPay 订单点款
 * @property \addons\Finance\services\SaleDetailService $saleDetail
 * @var array
 */
class Application extends Service
{
    
    public $childService = [
        'bankPay' => 'addons\Finance\services\BankPayService',
        'borrowPay' => 'addons\Finance\services\BorrowPayService',
        'contractPay' => 'addons\Finance\services\ContractPayService',            
        'orderPay' => 'addons\Finance\services\OrderPayService',
        'saleDetail' => 'addons\Finance\services\SaleDetailService',
    ];
}