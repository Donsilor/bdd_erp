<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Warehouse\common\forms\WarehouseGoldBillTGoodsForm;
use addons\Warehouse\common\models\WarehouseGold;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\helpers\Url;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\ArrayHelper;

/**
 * 领料单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillOService extends Service
{

    /**
     * 金料其他入库单tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-o/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'单据明细列表','url'=>Url::to(['gold-bill-o-goods/index','bill_id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],

        ];
        return $tabList;
    }

    /**
     * 扫码添加出库单明细
     * @param int $bill_id
     * @param array $goods_ids
     */
    public function scanGoods($bill_id, $gold_sns)
    {
        $bill = WarehouseGoldBill::find()->where(['id'=>$bill_id,'bill_type'=>GoldBillTypeEnum::GOLD_O])->one();
        if(empty($bill) || $bill->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("单据不是保存状态");
        }
        foreach ($gold_sns as $gold_sn) {
            $goods = WarehouseGold::find()->where(['gold_sn'=>$gold_sn, 'gold_status'=>GoldStatusEnum::IN_STOCK])->one();
            if(empty($goods)) {
                throw new \Exception("[{$gold_sn}]批次号不存在或者不是库存中");
            }
            $this->createGoldBillGoodsByGoods($bill, $goods);
        }
        //更新收货单汇总：总金额和总数量
        Yii::$app->warehouseService->goldBill->goldBillSummary($bill->id);

        return $bill;
    }

    /**
     * 添加单据明细 通用代码
     * @param WarehouseBillCForm $bill
     * @param WarehouseGoods $goods
     * @throws \Exception
     */
    private function createGoldBillGoodsByGoods($bill, $goods)
    {
        $gold_sn = $goods->gold_sn;
        $billGoods = new WarehouseGoldBillGoods();
        $billGoods->attributes = [
            'bill_id' =>$bill->id,
            'bill_no' =>$bill->bill_no,
            'bill_type'=>$bill->bill_type,
            'gold_sn'=>$gold_sn,
            'gold_name'=>$goods->gold_name,
            'style_sn'=>$goods->style_sn,
            'goods_num'=>1, //无用
            'gold_type'=>$goods->gold_type,
            'gold_weight'=>0,
            'gold_price'=>$goods->gold_price,
        ];
        if(false === $billGoods->save()) {
            throw new \Exception("[{$gold_sn}]".$this->getError($billGoods));
        }

    }

    /***
     * @param $gold_sn
     * @param $adjust_weight
     * @param 更新库存重量
     * @throws \Exception
     */
    public function updateGoldWeight($gold_sn, $adjust_weight) {

        $result = [
            'status' =>true ,
            'msg' =>''
        ];

        $adjust_weight = floatval($adjust_weight);
        $model = WarehouseGold::find()->where(['gold_sn'=>$gold_sn])->one();
        if(empty($model)) {
            $result['status'] = false;
            $result['msg'] = "({$gold_sn})金料编号不存在";
        }elseif ($model->gold_status != GoldStatusEnum::IN_STOCK && $model->gold_status != GoldStatusEnum::SOLD_OUT) {
            $result['status'] = false;
            $result['msg'] = "({$gold_sn})金料不是库存中";
        }
        $new_gold_weight = $model->gold_weight + $adjust_weight;
        $new_gold_weight = floatval($new_gold_weight);
        if($new_gold_weight < 0) {
            $result['status'] = false;
            $result['msg'] = "({$gold_sn})金料库存不足";
        }


        $gold_status = $new_gold_weight == 0 ? GoldStatusEnum::SOLD_OUT : GoldStatusEnum::IN_STOCK;
        $model->gold_status = $gold_status;
        $model->gold_weight = $new_gold_weight;
        $model->cost_price = $model->gold_price * $model->gold_weight;
        $res = $model->save(true,['gold_status','gold_weight','cost_price']);
        if(!$res) {
            $result['status'] = false;
            $result['msg'] = "({$gold_sn})金料库存变更失败";
        }
        return $result;

    }


    /**
     * 其它出库单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function cancelBillO($form)
    {
        //更新库存状态
        $billGoods = WarehouseGoldBillGoods::find()->select(['gold_sn','gold_weight'])->where(['bill_id' => $form->id])->all();
        if($billGoods){
            foreach ($billGoods as $goods){
                $adjust_weight = $goods->gold_weight;
                $res = Yii::$app->warehouseService->goldO->updateGoldWeight($goods->gold_sn, $adjust_weight);
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
        \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);

    }


    /**
     * 其它出库单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function deleteBillO($form)
    {
        //删除明细
        WarehouseGoldBillGoods::deleteAll(['bill_id' => $form->id]);
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
        \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);
    }


}