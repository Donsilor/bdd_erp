<?php

namespace addons\Warehouse\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Warehouse\common\enums\PartsStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use yii\db\Expression;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousePartsService extends Service
{
    /**
     *
     * 配件库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'配件详情','url'=>Url::to(['parts/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'领件信息','url'=>Url::to(['parts/lingjian','id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'配件日志','url'=>Url::to(['parts-log/index','id'=>$id,'tab'=>3,'returnUrl'=>$returnUrl])],
        ];
        return $tabList;
    }
    /**
     *
     * 创建批次号
     * @param WarehouseParts $model
     * @param bool $save
     * @throws
     * @return
     */
    public function createPartsSn($model, $save = true)
    {
        //1.供应商
        $parts_sn = $model->supplier->supplier_tag ?? '00';
        //2.配件类型
        $type_codes = Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE,'id','code');
        $parts_sn .= $type_codes[$model->parts_type] ?? '0';
        //3.数字编号
        $parts_sn .= str_pad($model->id,6,'0',STR_PAD_LEFT).'P';
        if($save === true) {
            $model->parts_sn = $parts_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $parts_sn;
    }
    /**
     *
     * 更改配件库存
     * @param string $parts_sn
     * @param integer $adjust_num
     * @param double $adjust_weight
     * @param integer $adjust_type
     * @throws
     *
     */
    public function adjustPartsStock($parts_sn, $adjust_num, $adjust_weight, $adjust_type) {
        
        $adjust_weight = abs(floatval($adjust_weight));
        
        $model = WarehouseParts::find()->where(['parts_sn'=>$parts_sn])->one();
        if(empty($model)) {
            throw new \Exception("({$parts_sn})配件编号不存在");
        }elseif ($model->parts_status != PartsStatusEnum::IN_STOCK && $model->parts_status != PartsStatusEnum::SOLD_OUT) {
            throw new \Exception("({$parts_sn})配件不是库存中");
        }elseif($adjust_type == AdjustTypeEnum::MINUS){
            if($model->parts_weight < $adjust_weight) {
                throw new \Exception("({$parts_sn})配件库存不足");
            }
        }
        if($adjust_num <= 0){
            throw new \Exception("({$parts_sn})配件调整数量不能为0");
        }
        if($adjust_weight <= 0){
            //throw new \Exception("({$parts_sn})配件调整重量不能为0");
        }
        if($adjust_type == AdjustTypeEnum::ADD) {
            $update = ['parts_num'=>new Expression("parts_num+{$adjust_num}"),'parts_status'=>PartsStatusEnum::IN_STOCK];
            $result = WarehouseParts::updateAll($update,new Expression("parts_sn='{$parts_sn}'"));
            if(!$result) {
                throw new \Exception("({$parts_sn})配件库存变更失败");
            }
            /*$update = ['parts_weight'=>new Expression("parts_weight+{$adjust_weight}"),'parts_status'=>PartsStatusEnum::IN_STOCK];
            $result = WarehouseParts::updateAll($update,new Expression("parts_sn='{$parts_sn}'"));
            if(!$result) {
                throw new \Exception("({$parts_sn})配件库存变更失败");
            }*/
        }else{
            $update = ['parts_num'=>new Expression("parts_num-{$adjust_num}")];
            $result = WarehouseParts::updateAll($update,new Expression("parts_sn='{$parts_sn}' and parts_num>={$adjust_num}"));
            if(!$result) {
                throw new \Exception("({$parts_sn})配件库存不足");
            }
            /*$update = ['parts_weight'=>new Expression("parts_weight-{$adjust_weight}")];
            $result = WarehouseParts::updateAll($update,new Expression("parts_sn='{$parts_sn}' and parts_weight>={$adjust_weight}"));
            if(!$result) {
                throw new \Exception("({$parts_sn})配件库存不足");
            }*/
            //更新为已售馨
            if($model->parts_num <= $adjust_num){
                $result = WarehouseParts::updateAll(['parts_status'=>PartsStatusEnum::SOLD_OUT],new Expression("parts_sn='{$parts_sn}' and parts_num <= 0"));
            }
            if($model->parts_weight <= $adjust_weight){
                //$result = WarehouseParts::updateAll(['parts_status'=>PartsStatusEnum::SOLD_OUT],new Expression("parts_sn='{$parts_sn}' and parts_weight <= 0"));
            }
        }
        
    }

}