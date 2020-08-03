<?php

namespace addons\Warehouse\services;

use addons\Shop\common\models\Style;
use addons\Warehouse\common\enums\LayoutTypeEnum;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\models\WarehouseTemplet;
use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseStone;
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
class WarehouseTempletService extends Service
{
    /**
     * 样板库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'样板详情','url'=>Url::to(['templet/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            //2=>['name'=>'领料信息','url'=>Url::to(['templet/lingliao','id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'样板日志','url'=>Url::to(['templet-log/index','id'=>$id,'tab'=>3,'returnUrl'=>$returnUrl])],
        ];
        return $tabList;
    }
    /**
     * 创建批次号
     * @param WarehouseTemplet $model
     * @param string $save
     * @throws
     *
     */
    public function createBatchSn($model, $save = true)
    {
        //1.供应商
        $batch_sn = $model->supplier->supplier_tag ?? '00';
        //2.样板类型
        if($model->layout_type == LayoutTypeEnum::SILVER){
            $batch_sn .="S";
        }elseif($model->layout_type == LayoutTypeEnum::RUBBER){
            $batch_sn .="R";
        }
        //3.数字编号
        $batch_sn .= str_pad($model->id,7,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->batch_sn = $batch_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $batch_sn;
    }
    /**
     * 商品图片
     * @param WarehouseGift $model
     * @throws
     * @return
     */
    public function getStyleImage($model){
        $style = Style::find()->where(['style_sn'=>$model->style_sn])->one();
        $image = $style->style_image ?? '';
        return $image;
    }
    /**
     * 更改库存
     * @param string $gold_sn
     * @param double $adjust_weight
     * @param integer $adjust_type
     * @throws
     *
     */
    public function adjustGoldStock($gold_sn, $adjust_weight, $adjust_type) {
        
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