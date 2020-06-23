<?php

namespace addons\Warehouse\services;

use Yii;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\components\Service;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use yii\db\Expression;
use addons\Warehouse\common\enums\StoneStatusEnum;

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
    /* public function editStone($form)
    {
        $goods = WarehouseStoneBillGoods::find()->where(['bill_id'=>$form->id])->all();
        foreach ($goods as $detail){
            $stoneM = new WarehouseStone();
            $stone = [
                'stone_sn' => rand(10000000000,99999999999),//临时
                'stone_name' => $detail->stone_name,
                'stone_status' => StoneStatusEnum::IN_STOCK,    
                'style_sn' => $detail->style_sn,
                'stone_type' => $detail->stone_type,
                'supplier_id' => $form->supplier_id,
                'stone_color' => $detail->color,
                'stone_clarity' => $detail->clarity,
                'stock_cnt' => $detail->stone_num,
                'ms_cnt' => $detail->stone_num,
                'stock_weight' => $detail->stone_weight,
                'ms_weight' => $detail->stone_weight,
                'cost_price' => $detail->cost_price,
                'sale_price' => $detail->sale_price,
            ];
            $stoneM->attributes = $stone;
            if(false === $stoneM->save()){
                throw new \Exception($this->getError($stoneM));
            }
            $this->createStoneSn($stoneM);
        }
       
    } */

    /**
     * 更新库存信息
     * @param $stone
     */
    public function updateStockCnt($stone){
        $stock_cnt = $stone->ms_cnt+$stone->fenbaoru_cnt-$stone->ss_cnt-$stone->fenbaochu_cnt+$stone->ts_cnt-$stone->ys_cnt-$stone->sy_cnt-$stone->th_cnt+$stone->rk_cnt-$stone->ck_cnt;
        $stock_weight = $stone->ms_weight+$stone->fenbaoru_weight-$stone->ss_weight-$stone->fenbaochu_weight+$stone->ts_weight-$stone->ys_weight-$stone->sy_weight-$stone->th_weight+$stone->rk_weight-$stone->ck_weight;
        $stone->stock_cnt = $stock_cnt;
        $stone->ck_weight = $stock_weight;
        if(false === $stone->save()){
            throw new \Exception($this->getError($stone));
        }
    }
    /**
     * 创建石包号
     * @param WarehouseStone $model
     * @param string $save
     */
    public function createStoneSn($model, $save = true)
    {
        //1.供应商
        $stone_sn = $model->supplier->supplier_tag ?? '00';
        //2.石料类型
        $type_codes = Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE,'id','code');
        $stone_sn .= $type_codes[$model->stone_type] ?? '0';
        //3.数字编号
        $stone_sn .= str_pad($model->id,7,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->stone_sn = $stone_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $stone_sn;
    }
    
    /**
     * 更改石料库存
     * @param string $stone_sn
     * @param integer $adjust_num 调整数量
     * @param double $adjust_weight 调整重量
     * @param integer $adjust_type 调整类型 1增加 0减
     */
    public function adjustStoneStock($stone_sn,$adjust_num ,$adjust_weight, $adjust_type) {

        $adjust_num = abs(floatval($adjust_num));
        $adjust_weight = abs(floatval($adjust_weight));
        
        $model = WarehouseStone::find()->where(['stone_sn'=>$stone_sn])->one();
        if(empty($model)) {
            throw new \Exception("({$stone_sn})石包编号不存在");
        }elseif ($model->stone_status != StoneStatusEnum::IN_STOCK) {
            throw new \Exception("({$stone_sn})石包不是库存中".$model->stone_status);
        }elseif($adjust_type == AdjustTypeEnum::MINUS){
            if($model->stock_cnt < $adjust_num) {
                throw new \Exception("({$stone_sn})石包库存不足：数量不足");
            }elseif($model->stock_weight < $adjust_weight) {
                throw new \Exception("({$stone_sn})石包库存不足：重量不足");
            }
        }        
        if($adjust_weight <= 0){
            throw new \Exception("({$stone_sn})石包调整重量不能小于或等于0");
        }
        if($adjust_type == AdjustTypeEnum::ADD) {
            $update = ["stock_cnt"=>new Expression("stock_cnt+{$adjust_num}"), "stock_weight" =>new Expression("stock_weight+{$adjust_weight}")];
            $where  = new Expression("stone_sn='{$stone_sn}'");
            $result = WarehouseStone::updateAll($update,$where);
            if(!$result) {
                throw new \Exception("({$stone_sn})石包库存变更失败(新增)");
            }
        }else{
            $update = ["stock_cnt"=>new Expression("stock_cnt-{$adjust_num}"), "stock_weight" =>new Expression("stock_weight-{$adjust_weight}")];
            $where  = new Expression("stone_sn='{$stone_sn}' and stock_cnt >={$adjust_num} and stock_weight>={$adjust_weight}");
            $result = WarehouseStone::updateAll($update,$where);
            if(!$result) {
                throw new \Exception("({$stone_sn})石包库存变更失败(库存不足)");
            }
        }
        
    }
}