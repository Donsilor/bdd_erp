<?php

namespace addons\Gdzb\common\enums;

use common\enums\BaseEnum;

/**
 * 支付方式
 * @package common\enums
 */
class PayTypeEnum extends BaseEnum
{
    const Account = 1;
    const WeChat = 2;
    const Alipay = 3;
    const Other = 4;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [              
              self::Account =>'对公账户',
              self::WeChat =>'微信',
              self::Alipay =>'支付宝',
              self::Other =>'其它',
        ];
    }
    
}