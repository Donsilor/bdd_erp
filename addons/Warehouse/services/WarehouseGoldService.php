<?php

namespace addons\Warehouse\services;

use common\enums\StatusEnum;
use Yii;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\forms\WarehouseGoldBillLGoodsForm;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Style\common\enums\AttrIdEnum;
use common\components\Service;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldService extends Service
{
    /**
     * 创建/编辑-金料信息
     * @param $form
     */
    public function editGold($form)
    {
        $gold = WarehouseGoldBillLGoodsForm::find()->where(['bill_id'=>$form->id])->all();
        $ids = [];
        foreach ($gold as $detail){
            $goldM = new WarehouseGold();
            $good = [
                'gold_sn' => "---",//临时
                'style_sn' => $detail->style_sn,
                'gold_name' => $detail->gold_name,
                'gold_type' => $detail->gold_type,
                'supplier_id' => $form->supplier_id,
                'gold_num' => 1,
                'gold_weight' => $detail->gold_weight,
                'cost_price' => $detail->cost_price,
                'gold_price' => $detail->gold_price,
                'warehouse_id' => $form->to_warehouse_id,
                'status' => StatusEnum::ENABLED,
                'created_at' => time(),
            ];
            $goldM->attributes = $good;
            if(false === $goldM->save()){
                throw new \Exception($this->getError($goldM));
            }
            $ids[] = $goldM->attributes['id'];
        }
        if($ids){
            foreach ($ids as $id){
                $stone = WarehouseGold::findOne(['id'=>$id]);
                $this->createGoldSn($stone);
            }
        }
    }
    /**
     * 创建批次号
     * @param WarehouseStone $model
     * @param string $save
     *
     */
    public function createGoldSn($model, $save = true)
    {
        //1.供应商
        $gold_sn = $model->supplier->supplier_tag ?? '00';
        //2.金料类型
        $type_codes = Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE,'id','code');
        $gold_sn .= $type_codes[$model->gold_type] ?? '0';
        //3.数字编号
        $gold_sn .= str_pad($model->id,6,'0',STR_PAD_LEFT)."G";
        if($save === true) {
            $model->gold_sn = $gold_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $gold_sn;
    }
}