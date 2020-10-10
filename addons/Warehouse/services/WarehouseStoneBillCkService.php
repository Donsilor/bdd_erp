<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\helpers\Url;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\helpers\ArrayHelper;

/**
 * 领料单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillCkService extends Service
{

    /**
     * 扫码添加出库单明细
     * @param int $bill_id
     * @param array $goods_ids
     */
    public function scanGoods($bill_id, $stone_sns)
    {
        $bill = WarehouseStoneBill::find()->where(['id'=>$bill_id,'bill_type'=>StoneBillTypeEnum::STONE_CK])->one();
        if(empty($bill) || $bill->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("单据不是保存状态");
        }
        foreach ($stone_sns as $stone_sn) {
            $goods = WarehouseStone::find()->where(['stone_sn'=>$stone_sn, 'stone_status'=>StoneStatusEnum::IN_STOCK])->one();
            if(empty($goods)) {
                throw new \Exception("[{$stone_sn}]批次号不存在或者不是库存中");
            }
            $this->createStoneBillGoodsByGoods($bill, $goods);
        }
        //更新收货单汇总：总金额和总数量
        Yii::$app->warehouseService->stoneBill->stoneBillSummary($bill->id);

        return $bill;
    }

    /**
     * 添加单据明细 通用代码
     * @param WarehouseBillCForm $bill
     * @param WarehouseGoods $goods
     * @throws \Exception
     */
    private function createStoneBillGoodsByGoods($bill, $goods)
    {
        $stone_sn = $goods->stone_sn;
        $billGoods = new WarehouseStoneBillGoods();
        $billGoods->attributes = [
            'bill_id' =>$bill->id,
            'bill_no' =>$bill->bill_no,
            'bill_type'=>$bill->bill_type,
            'stone_sn'=>$stone_sn,
            'stone_name'=>$goods->stone_name,
            'style_sn'=>$goods->style_sn,
            'goods_num'=>1, //无用
            'stone_type'=>$goods->stone_type,
            'stone_weight'=>0,
            'stone_num'=>0,
            'incl_tax_price'=>0,
            'stone_price'=>$goods->stone_price,
            'shape'=>$goods->stone_shape,
            'color'=>$goods->stone_color,
            'clarity'=>$goods->stone_clarity,
            'cut'=>$goods->stone_cut,
            'polish'=>$goods->stone_polish,
            'fluorescence'=>$goods->stone_fluorescence,
            'symmetry'=>$goods->stone_symmetry,
            'stone_colour'=>$goods->stone_colour,
            'stone_norms'=>$goods->stone_norms,
            'stone_size'=>$goods->stone_size,
        ];
        if(false === $billGoods->save()) {
            throw new \Exception("[{$stone_sn}]".$this->getError($billGoods));
        }

    }

    /***
     * @param $stone_sn
     * @param $adjust_weight
     * @param 更新库存重量
     * @throws \Exception
     */
    public function updateStoneWeight($stone_sn, $adjust_weight, $adjust_cnt) {

        $result = [
            'status' =>true ,
            'msg' =>''
        ];

        $adjust_weight = floatval($adjust_weight);
        $model = WarehouseStone::find()->where(['stone_sn'=>$stone_sn])->one();
        if(empty($model)) {
            $result['status'] = false;
            $result['msg'] = "({$stone_sn})石料编号不存在";
        }elseif ($model->stone_status != StoneStatusEnum::IN_STOCK && $model->stone_status != StoneStatusEnum::SOLD_OUT) {
            $result['status'] = false;
            $result['msg'] = "({$stone_sn})石料不是库存中";
        }
        $new_stone_weight = $model->stock_weight + $adjust_weight;
        $new_stock_cnt = $model->stock_cnt + $adjust_cnt;
        $new_stone_weight = floatval($new_stone_weight);
        if($new_stone_weight < 0) {
            $result['status'] = false;
            $result['msg'] = "({$stone_sn})石料重量不足";
        }
        if($new_stock_cnt < 0) {
            $result['status'] = false;
            $result['msg'] = "({$stone_sn})石料数量不足";
        }


        $stone_status = $new_stone_weight == 0 ? StoneStatusEnum::SOLD_OUT : StoneStatusEnum::IN_STOCK;
        $model->stone_status = $stone_status;
        $model->stock_weight = $new_stone_weight;
        $model->stock_cnt = $new_stock_cnt;
        $model->cost_price = $model->stone_price * $model->stock_weight;
        $res = $model->save(true,['stone_status','stock_weight','stock_cnt','cost_price']);
        if(!$res) {
            $result['status'] = false;
            $result['msg'] = "({$stone_sn})石料库存变更失败";
        }
        return $result;

    }


    /**
     * 其它出库单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function cancelBillCk($form)
    {
        //更新库存状态
        $billGoods = WarehouseStoneBillGoods::find()->select(['stone_sn','stock_weight'])->where(['bill_id' => $form->id])->all();
        if($billGoods){
            foreach ($billGoods as $goods){
                $adjust_weight = $goods->stock_weight;
                $res = Yii::$app->warehouseService->stoneCk->updateStoneWeight($goods->stone_sn, $adjust_weight);
                if($res['status'] == false){
                    throw new \Exception( $res['msg']);
                }
            }
        }
        $form->bill_status = BillStatusEnum::CANCEL;
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
        //日志
        $log = [
            'bill_id' => $form->id,
            'bill_status'=>$form->bill_status,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_module' => '单据取消',
            'log_msg' => '取消其它出库单'
        ];
        \Yii::$app->warehouseService->stoneBillLog->createStoneBillLog($log);

    }


    /**
     * 其它出库单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function deleteBillCk($form)
    {
        //删除明细
        WarehouseStoneBillGoods::deleteAll(['bill_id' => $form->id]);
        if(false === $form->delete()){
            throw new \Exception($this->getError($form));
        }

        $log = [
            'bill_id' => $form->id,
            'bill_status'=>$form->bill_status,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_module' => '单据删除',
            'log_msg' => '删除其它出库单'
        ];
        \Yii::$app->warehouseService->stoneBillLog->createStoneBillLog($log);
    }


}