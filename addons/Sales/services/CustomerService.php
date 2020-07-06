<?php

namespace addons\Sales\services;

use Yii;
use common\helpers\Url;
use common\components\Service;

/**
 * Class CustomerService
 * @package services\common
 */
class CustomerService extends Service
{
    /**
     * 客户列表 tab
     * @param int $id 客户ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'客户信息','url'=>Url::to(['view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            //2=>['name'=>'收货地址','url'=>Url::to(['address/index','customer_id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }
}