<?php

namespace addons\Warehouse\services;


use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\Url;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillLService extends Service
{

    /**
     * 仓储单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'单据详情','url'=>Url::to(['warehouse-bill-l/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'单据明细','url'=>Url::to(['warehouse-bill-l-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'结算商信息','url'=>Url::to(['warehouse-bill-pay/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
            4=>['name'=>'日志信息','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])],
        ];
    }

}