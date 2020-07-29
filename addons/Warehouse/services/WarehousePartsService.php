<?php

namespace addons\Warehouse\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use yii\db\Expression;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousePartsService extends Service
{
    /**
     * 配件库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'配件详情','url'=>Url::to(['parts/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'领件信息','url'=>Url::to(['parts/lingliao','id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'配件日志','url'=>Url::to(['parts-log/index','id'=>$id,'tab'=>3,'returnUrl'=>$returnUrl])],
        ];
        return $tabList;
    }
    /**
     * 创建批次号
     * @param WarehouseParts $model
     * @param string $save
     * @throws
     *
     */
    public function createPartsSn($model, $save = true)
    {
        //1.供应商
        $parts_sn = $model->supplier->supplier_tag ?? '00';
        //2.金料类型
        $type_codes = Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE,'id','code');
        $parts_sn .= $type_codes[$model->parts_type] ?? '0';
        //3.数字编号
        $parts_sn .= str_pad($model->id,6,'0',STR_PAD_LEFT)."G";
        if($save === true) {
            $model->parts_sn = $parts_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $parts_sn;
    }
    /**
     * 更改配件库存
     * @param string $parts_sn
     * @param double $adjust_weight
     * @param integer $adjust_type
     * @throws
     *
     */
    public function adjustPartsStock($gold_sn, $adjust_weight, $adjust_type) {
        
        $adjust_weight = abs(floatval($adjust_weight));
        
        $model = WarehouseGold::find()->where(['gold_sn'=>$gold_sn])->one();
        if(empty($model)) {
            throw new \Exception("({$gold_sn})金料编号不存在");
        }elseif ($model->gold_status != GoldStatusEnum::IN_STOCK && $model->gold_status != GoldStatusEnum::SOLD_OUT) {
            throw new \Exception("({$gold_sn})金料不是库存中");
        }elseif($adjust_type == AdjustTypeEnum::MINUS){
            if($model->gold_weight < $adjust_weight) {
                throw new \Exception("({$gold_sn})金料库存不足");
            }
        }
        if($adjust_weight <= 0){
            throw new \Exception("({$gold_sn})金料调整重量不能为0");
        }
        if($adjust_type == AdjustTypeEnum::ADD) {
            $update = ['gold_weight'=>new Expression("gold_weight+{$adjust_weight}"),'gold_status'=>GoldStatusEnum::IN_STOCK];
            $result = WarehouseGold::updateAll($update,new Expression("gold_sn='{$gold_sn}'"));
            if(!$result) {
                throw new \Exception("({$gold_sn})金料库存变更失败");
            }
        }else{
            $update = ['gold_weight'=>new Expression("gold_weight-{$adjust_weight}")];
            $result = WarehouseGold::updateAll($update,new Expression("gold_sn='{$gold_sn}' and gold_weight>={$adjust_weight}"));
            if(!$result) {
                throw new \Exception("({$gold_sn})金料库存不足");
            }
            //更新为已售馨
            if($model->gold_weight <= $adjust_weight){
                $result = WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::SOLD_OUT],new Expression("gold_sn='{$gold_sn}' and gold_weight <= 0"));
            }
        }
        
    }

}