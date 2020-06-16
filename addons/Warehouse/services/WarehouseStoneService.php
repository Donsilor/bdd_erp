<?php

namespace addons\Warehouse\services;

use Yii;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\components\Service;
use addons\Style\common\enums\AttrIdEnum;

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
        $stone = WarehouseStoneBillGoods::find()->where(['bill_id'=>$form->id])->all();
        $ids = [];
        foreach ($stone as $detail){
            //$shibao = WarehouseStone::findOne(['stone_name'=>$detail->stone_name]);
            //if(!$shibao){
                $stoneM = new WarehouseStone();
                $dia = [
                    'stone_sn' => "---",//临时
                    'stone_name' => $detail->stone_name,
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
                $stoneM->attributes = $dia;
                if(false === $stoneM->save()){
                    throw new \Exception($this->getError($stoneM));
                }
                $ids[] = $stoneM->attributes['id'];
            /*}else{
                $cost_price = bcmul($shibao->stock_weight, $shibao->cost_price, 2);
                $sale_price = bcmul($shibao->stock_weight, $shibao->sale_price, 2);
                $total_cost = bcmul($detail->stone_weight, $detail->cost_price, 2);
                $total_sale = bcmul($detail->stone_weight, $detail->sale_price, 2);
                $total_cost = bcadd($total_cost, $cost_price, 2);
                $total_sale = bcadd($total_sale, $sale_price, 2);
                $shibao->stock_weight = bcadd($shibao->stock_weight, $detail->stone_weight, 2);
                $shibao->cost_price = bcdiv($total_cost, $shibao->stock_weight, 2);
                $shibao->sale_price = bcdiv($total_sale, $shibao->stock_weight, 2);
                if(false === $shibao->save()){
                    throw new \Exception($this->getError($shibao));
                }
                $this->updateStockCnt($shibao);
            }*/
        }
        if($ids){
            foreach ($ids as $id){
                $stone = WarehouseStone::findOne(['id'=>$id]);
                $this->createStoneSn($stone);
            }
        }
    }

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
}