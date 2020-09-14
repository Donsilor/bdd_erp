<?php

namespace addons\Warehouse\services;


use common\enums\LogTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use Yii;
use yii\db\Exception;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\AuditStatusEnum;

/**
 * 退货返厂单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillBService extends WarehouseBillService
{

    /**
     * 创建退货返厂单明细
     * @param WarehouseBillBForm $form
     */
    public function createBillGoodsB($form, $bill_goods)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }

        //批量创建单据明细
        $goods_val = [];
        $goods_id_arr = [];
        foreach ($bill_goods as &$goods) {
            $goods_id = $goods['goods_id'];
            $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods_info)){
                throw new \yii\base\Exception("货号{$goods_id}不存在或者不是库存中");
            }
            $goods['bill_id'] = $form->id;
            $goods['bill_no'] = $form->bill_no;
            $goods['bill_type'] = $form->bill_type;
            $goods['warehouse_id'] = $form->to_warehouse_id;
            $goods['put_in_type'] = $goods_info['put_in_type'];
            $goods_val[] = array_values($goods);
            $goods_id_arr[] = $goods_id;
        }
        $goods_key = array_keys($bill_goods[0]);
        \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();

        //更新商品库存状态
        $execute_num = WarehouseGoods::updateAll(['goods_status'=> GoodsStatusEnum::IN_RETURN_FACTORY],['goods_id'=>$goods_id_arr, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
        if($execute_num <> count($bill_goods)){
            throw new Exception("货品改变状态数量与明细数量不一致");
        }

        //更新收货单汇总：总金额和总数量
        $res = \Yii::$app->warehouseService->bill->WarehouseBillSummary($form->id);
        if(false === $res){
            throw new Exception('更新单据汇总失败');
        }
    }

    /**
     * 退货返厂单审核
     * @param WarehouseBillBForm $form
     */
    public function auditBillB($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            //$form->status = StatusEnum::ENABLED;
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            //$form->status = StatusEnum::DISABLED;
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        $billGoods = WarehouseBillGoods::find()->select('goods_id')->where(['bill_id' => $form->id])->asArray()->all();
        if(empty($billGoods) && $form->audit_status == AuditStatusEnum::PASS){
            throw new \Exception("单据明细不能为空");
        }
        $goods_ids = ArrayHelper::getColumn($billGoods, 'goods_id');
        $condition = ['goods_status' => GoodsStatusEnum::IN_RETURN_FACTORY, 'goods_id' => $goods_ids];
        $status = $form->audit_status == AuditStatusEnum::PASS ? GoodsStatusEnum::HAS_RETURN_FACTORY : GoodsStatusEnum::IN_RETURN_FACTORY;
        $res = WarehouseGoods::updateAll(['goods_status' => $status], $condition);
        if(false === $res){
            throw new \Exception("更新货品状态失败");
        }

        foreach ($goods_ids as $goods_id){
            $warehouseGoods = WarehouseGoods::find()->select(['id'])->where(['goods_id'=>$goods_id])->one();
            //插入商品日志
            $log = [
                'goods_id' => $warehouseGoods->id,
                'goods_status' => GoodsStatusEnum::HAS_RETURN_FACTORY,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_msg' => '返厂单：'.$form->bill_no.";;货品状态:“".GoodsStatusEnum::getValue(GoodsStatusEnum::IN_STOCK)."”变更为：“".GoodsStatusEnum::getValue(GoodsStatusEnum::HAS_RETURN_FACTORY)."”"
            ];
            Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
        }


    }

    /**
     * 退货返厂单-关闭
     * @param WarehouseBillBForm $form
     * @throws
     */
    public function cancelBillB($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
        foreach ($billGoods as $goods){
            $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_RETURN_FACTORY]);
            if(!$res){
                throw new Exception("商品{$goods->goods_id}不是返厂中或者不存在，请查看原因");
            }
        }
        $form->bill_status = BillStatusEnum::CANCEL;
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 退货返厂单-删除
     * @param WarehouseBillBForm $form
     * @throws
     */
    public function deleteBillB($form)
    {
        //删除明细
        $res = WarehouseBillGoods::deleteAll(['bill_id' => $form->id]);
        if(false === $res){
            throw new Exception("删除明细失败");
        }
        if(false === $form->delete()){
            throw new \Exception($this->getError($form));
        }
    }
}