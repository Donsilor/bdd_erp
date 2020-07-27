<?php

namespace addons\Finance\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Finance\services
 * @property \addons\Finance\services\BankPayService $bankPay
 * @var array
 */
class Application extends Service
{
    
    public $childService = [
        'bankPay' => 'addons\Finance\services\BankPayService',
    ];
}