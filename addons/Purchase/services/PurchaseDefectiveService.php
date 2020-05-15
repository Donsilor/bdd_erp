<?php

namespace addons\Purchase\services;


use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use common\enums\StatusEnum;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseDefectiveService extends Service
{
    
    /**
     * 不良返厂单明细 tab
     * @param int $id 不良返厂单ID
     * @return array
     */
    public function menuTabList($defective_id,$returnUrl = null)
    {
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['purchase-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'单据明细','url'=>Url::to(['purchase-defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['purchase-defective-log/index','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }
    
    /**
     * 不良返厂单汇总
     * @param unknown $receipt_id
     */
    public function purchaseDefectiveSummary($defective_id)
    {
        $result = false;
        $sum = PurchaseDefectiveGoods::find()
                    ->select(['sum(1) as defective_num','sum(cost_price) as total_cost'])
                    ->where(['receipt_id'=>$defective_id])
                    ->asArray()->one();
        if($sum) {
            $result = PurchaseDefective::updateAll(['defective_num'=>$sum['defective_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$defective_id]);
        }
        return $result;
    }
 
}