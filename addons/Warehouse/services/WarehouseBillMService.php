<?php
namespace addons\Warehouse\services;
    

use addons\Warehouse\common\models\WarehouseBillLog;
use common\helpers\Url;

/**
 *  调拨单
 * Class WarehouseBillMService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillMService extends WarehouseService
{

    /**
     * 调拨单单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id,$returnUrl = null)
    {
        return [
            1=>['name'=>'单据详情','url'=>Url::to(['warehouse-bill-m/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'单据明细','url'=>Url::to(['warehouse-bill-m-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'日志信息','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }

    public function createWarehouseBillLog($log){
        $warehouse_goods_log = new WarehouseBillLog();
        $warehouse_goods_log->attributes = $log;
        if(false === $warehouse_goods_log->save()){
            throw new \Exception($this->getError($warehouse_goods_log));
        }
        return $warehouse_goods_log ;

    }
}