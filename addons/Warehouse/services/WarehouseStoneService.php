<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseGoodsLog;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBillDetail;
use common\components\Service;
use common\enums\ConfirmEnum;
use common\helpers\Url;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Style\common\enums\StyleSexEnum;
use common\enums\AuditStatusEnum;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneService extends Service
{
    /**
     * 创建/编辑-石包信息
     * @param $form
     */
    public function editStone($form)
    {
        $stone = WarehouseStoneBillDetail::find()->where(['bill_id'=>$form->id])->all();
        foreach ($stone as $detail){
            $shibao = WarehouseStone::findOne(['shibao'=>$detail->shibao]);
            if(!$shibao){
                $dia = [
                    'shibao' => $detail->shibao,
                    'kucun_cnt' => $detail->stone_num,
                    'MS_cnt' => $detail->stone_num,
                    'kucun_zhong' => $detail->stone_weight,
                    'MS_zhong' => $detail->stone_weight,
                    'cost_price' => $detail->purchase_price,
                    'sale_price' => $detail->sale_price,
                    'purchase_price' => $detail->purchase_price,
                ];
                $shibao->attributes = $dia;
                if(false === $shibao->save()){
                    throw new \Exception($this->getError($shibao));
                }
            }
        }
    }
}