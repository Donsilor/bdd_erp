<?php

namespace addons\Warehouse\services;

use common\helpers\Url;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\ArrayHelper;

/**
 * 领料单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillTService extends Service
{

    /**
     * 金料其他入库单tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-t/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'单据明细列表','url'=>Url::to(['gold-bill-t-goods/index','bill_id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],

        ];
        return $tabList;
    }

}