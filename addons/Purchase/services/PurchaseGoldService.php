<?php

namespace addons\Purchase\services;

use Yii;
use common\components\Service;
use common\helpers\Url;
use common\enums\StatusEnum;
use addons\Purchase\common\models\PurchaseGold;
use addons\Purchase\common\models\PurchaseGoldGoods;

/**
 * Class PurchaseGoldService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseGoldService extends Service
{
    
    /**
     * 金料采购单菜单
     * @param int $id 采购单id
     * @return array
     */
    public function menuTabList($purchase_id, $returnUrl = null)
    {
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['purchase-gold/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'采购商品','url'=>Url::to(['purchase-gold-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['purchase-gold-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];        
    }
    
    /**
     * 金料采购单汇总
     * @param unknown $purchase_id
     */
    public function summary($purchase_id)
    {
        $sum = PurchaseGoldGoods::find()
            ->select(['sum(goods_num) as total_num','sum(cost_price*goods_num) as total_cost'])
            ->where(['purchase_id'=> $purchase_id,'status'=> StatusEnum::ENABLED])
            ->asArray()->one();
        
        if($sum) {
            PurchaseGold::updateAll(['total_num'=>$sum['total_num'],'total_cost'=>$sum['total_cost']],['id'=>$purchase_id]);
        }
    }
}