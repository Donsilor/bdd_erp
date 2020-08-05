<?php

namespace addons\Purchase\services;

use Yii;
use common\components\Service;
use common\helpers\Url;
use common\enums\StatusEnum;
use addons\Purchase\common\models\PurchaseStone;
use addons\Purchase\common\models\PurchaseStoneGoods;

/**
 * Class PurchaseStoneService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseStoneService extends Service
{
    
    /**
     * 石料采购单菜单
     * @param int $id 采购单id
     * @return array
     */
    public function menuTabList($purchase_id, $returnUrl = null)
    {
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['purchase-stone/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'采购商品','url'=>Url::to(['purchase-stone-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['purchase-stone-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];        
    }
    
    /**
     * 采购单汇总
     * @param unknown $purchase_id
     */
    public function summary($purchase_id)
    {
        $sum = PurchaseStoneGoods::find()
            ->select(['sum(goods_num) as total_num','sum(stone_num) as total_stone_num','sum(cost_price*goods_num) as total_cost'])
            ->where(['purchase_id'=> $purchase_id,'status'=> StatusEnum::ENABLED])
            ->asArray()->one();
        
        if($sum) {
            PurchaseStone::updateAll(['total_num'=>$sum['total_num'],'total_stone_num'=>$sum['total_stone_num']/1,'total_cost'=>$sum['total_cost']],['id'=>$purchase_id]);
        }
    }
    
    
}