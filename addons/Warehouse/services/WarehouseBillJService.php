<?php

namespace addons\Warehouse\services;

use Yii;
use yii\db\Exception;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillJ;
use addons\Warehouse\common\models\WarehouseBillGoodsJ;
use addons\Warehouse\common\forms\WarehouseBillJForm;
use addons\Warehouse\common\forms\WarehouseBillGoodsForm;
use addons\Warehouse\common\forms\WarehouseBillJGoodsForm;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\enums\LendStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

/**
 * 借货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillJService extends WarehouseBillService
{

    /**
     * 创建借货单
     * @param WarehouseBillJForm $form
     * @throws
     *
     */
    public function createBillJ($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if (!$form->lender_id) {
            throw new \Exception("借货人不能为空");
        }
        if (!$form->est_restore_time) {
            throw new \Exception("预计还货日期不能为空");
        }
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }

        //创建借货单附表
        $billJ = WarehouseBillJ::findOne($form->id);
        $billJ = $billJ ?? new WarehouseBillJ();
        $billJ->id = $form->id;
        $billJ->lender_id = $form->lender_id;
        $billJ->est_restore_time = $form->est_restore_time;
        $billJ->afterValidate();

        if (false === $billJ->save()) {
            throw new \Exception($this->getError($billJ));
        }
    }

    /**
     * 批量添加借货单明细
     * @param WarehouseBillJGoodsForm $form
     * @throws
     */
    public function batchAddGoods($form)
    {
        $bill = WarehouseBill::findOne($form->bill_id);
        $form->bill_no = $bill->bill_no;
        $form->bill_type = $bill->bill_type;
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        foreach ($form->goods_list ?? [] as $goods_id => $goods) {
            $wareGoods = WarehouseGoods::find()->where(['goods_id' => $goods_id])->one();
            if (empty($goods)) {
                throw new \Exception("[{$goods_id}]条码货号不存在");
            }
            if ($goods['goods_num'] <= 0) {
                throw new \Exception("[{$goods_id}]借货数量必须大于0");
            }
            $wareGoods->goods_num = $goods['goods_num'];
            $this->createBillGoodsByGoods($bill, $wareGoods);
        }
        //更新收货单汇总：总金额和总数量
        $this->goodsJSummary($form->bill_id);
    }

    /**
     * 扫码添加退货单明细
     * @param int $bill_id
     * @param array $goods_ids
     * @return object
     * @throws
     */
    public function scanAddGoods($bill_id, $goods_ids)
    {
        $bill = WarehouseBill::find()->where(['id' => $bill_id, 'bill_type' => BillTypeEnum::BILL_TYPE_J])->one();
        if (empty($bill) || $bill->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("单据不是保存状态");
        }
        foreach ($goods_ids as $goods_id) {
            $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id])->one();
            if (empty($goods)) {
                throw new \Exception("[{$goods_id}]条码货号不存在");
            }
            $this->createBillGoodsByGoods($bill, $goods);
        }
        //同步更新借货单附表
        $this->goodsJSummary($bill_id);
        return $bill;
    }

    /**
     * 添加单据明细 通用代码
     * @param WarehouseBillCForm $bill
     * @param WarehouseGoods $goods
     * @throws \Exception
     */
    private function createBillGoodsByGoods($bill, $goods)
    {
        $goods_id = $goods->goods_id;
        if ($goods->goods_status != GoodsStatusEnum::IN_STOCK) {
            throw new \Exception("[{$goods_id}]条码货号不是库存状态");
        }
        $billGoods = new WarehouseBillGoods();
        $bGoods = $billGoods::findOne(['bill_id' => $bill->id, 'goods_id' => $goods_id]);
        if ($bGoods) {
            throw new \Exception("[{$goods_id}]条码货号不能重复添加");
        }
        $billGoods->attributes = [
            'bill_id' => $bill->id,
            'bill_no' => $bill->bill_no,
            'bill_type' => $bill->bill_type,
            'goods_id' => $goods_id,
            'goods_name' => $goods->goods_name,
            'style_sn' => $goods->style_sn,
            'goods_num' => $goods->goods_num,
            'put_in_type' => $goods->put_in_type,
            'warehouse_id' => $goods->warehouse_id,
            'from_warehouse_id' => $goods->warehouse_id,
            'material' => $goods->material,
            'material_type' => $goods->material_type,
            'material_color' => $goods->material_color,
            'gold_weight' => $goods->gold_weight,
            'gold_loss' => $goods->gold_loss,
            'diamond_carat' => $goods->diamond_carat,
            'diamond_color' => $goods->diamond_color,
            'diamond_clarity' => $goods->diamond_clarity,
            'diamond_cert_id' => $goods->diamond_cert_id,
            'diamond_cert_type' => $goods->diamond_cert_type,
            'cost_price' => $goods->cost_price,//采购成本价
            'chuku_price' => $goods->calcChukuPrice(),//计算出库成本价
            'market_price' => $goods->market_price,
            'markup_rate' => $goods->markup_rate,
        ];
        if (false === $billGoods->save()) {
            throw new \Exception("[{$goods_id}]" . $this->getError($billGoods));
        }
        //同步借货单明细附属表
        $billGJ = WarehouseBillGoodsJ::findOne($billGoods->id);
        $billGJ = $billGJ ?? new WarehouseBillGoodsJ();
        $billGJ->id = $billGoods->id;
        $billGJ->bill_id = $billGoods->bill_id;
        if (false === $billGJ->save()) {
            throw new \Exception($this->getError($billGJ));
        }
        //扣减库存
        if ($goods->goods_num >= 1) {
            \Yii::$app->warehouseService->warehouseGoods->updateStockNum($goods_id, $goods->goods_num, AdjustTypeEnum::MINUS, true);
        }
        //WarehouseGoods::updateAll(['chuku_price' => $billGoods->chuku_price, 'chuku_time' => time()], ['goods_id' => $goods_id]);
    }

    /**
     * 借货单-审核
     * @param WarehouseBill $form
     * @throws
     */
    public function auditBillJ($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $billJ = WarehouseBillJ::findOne($form->id);
        if ($form->audit_status == AuditStatusEnum::PASS) {
            $billGoods = WarehouseBillGoods::find()->select(['id', 'goods_id', 'goods_num'])->where(['bill_id' => $form->id])->all();
            if (!$billGoods) {
                throw new \Exception("单据明细不能为空");
            }
            //更新单据明细状态
            $ids = ArrayHelper::getColumn($billGoods, 'id');
            $execute_num = WarehouseBillGoodsJ::updateAll(['lend_status' => LendStatusEnum::IN_RECEIVE], ['id' => $ids, 'lend_status' => LendStatusEnum::SAVE]);
            if ($execute_num <> count($ids)) {
                throw new \Exception("同步更新商品明细状态失败");
            }
            $form->bill_status = BillStatusEnum::CONFIRM;
            $billJ->lend_status = LendStatusEnum::IN_RECEIVE;
            //同步更新商品库存状态
            foreach ($billGoods as $goods) {
                $stock_num = $goods->goods->stock_num ?? 0;
                $do_chuku_num = $goods->goods->do_chuku_num ?? 0;
                $log_msg = '';
                if ($stock_num) {
                    $goods_status = GoodsStatusEnum::IN_STOCK;
                } else {
                    $goods_status = GoodsStatusEnum::HAS_LEND;
                    $log_msg .= "货品状态:“" . GoodsStatusEnum::getValue(GoodsStatusEnum::IN_STOCK) . "”变更为:“" . GoodsStatusEnum::getValue($goods_status) . "”;";
                }
                $res = WarehouseGoods::updateAll(['goods_status' => $goods_status, 'do_chuku_num' => ($do_chuku_num - $goods->goods_num)], ['goods_id' => $goods->goods_id]);//, 'goods_status' => GoodsStatusEnum::IN_LEND
                if (!$res) {
                    //throw new \Exception("商品{$goods->goods_id}状态不是借货中或者不存在，请查看原因");
                }
                //插入商品日志
                $log = [
                    'goods_id' => $goods->goods->id,
                    'goods_status' => $goods_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_msg' => '借货单: ' . $form->bill_no . ";" . $log_msg . "借货数量: " . $goods->goods_num,
                ];
                Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
            }
        } else {
            $form->bill_status = BillStatusEnum::SAVE;
            $billJ->lend_status = LendStatusEnum::SAVE;
        }
        if (false === $billJ->save()) {
            throw new \Exception($this->getError($billJ));
        }
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 借货单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function closeBillJ($form)
    {
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id', 'goods_num'])->all();
        if ($billGoods) {
            foreach ($billGoods as $goods) {
                $goodsM = WarehouseGoods::findOne(['goods_id' => $goods->goods_id]);
                $goodsM->goods_status = GoodsStatusEnum::IN_STOCK;
                $goodsM->stock_num = $goodsM->stock_num + $goods->goods_num;
                $goodsM->do_chuku_num = $goodsM->do_chuku_num - $goods->goods_num;
                if (false === $goodsM->save()) {
                    throw new \Exception($this->getError($goodsM));
                }
            }
        }
        $form->bill_status = BillStatusEnum::CANCEL;
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 借货单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function deleteBillJ($form)
    {
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->all();
        if ($billGoods) {
            foreach ($billGoods as $goods) {
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK], ['goods_id' => $goods->goods_id]);//'goods_status' => GoodsStatusEnum::IN_LEND
                if (!$res) {
                    //throw new Exception("商品{$goods->goods_id}不是借货中或者不存在，请查看原因");
                }
            }
        }
        $ids = ArrayHelper::getColumn($billGoods, 'id');
        $execute_num = WarehouseBillGoodsJ::deleteAll(['id' => $ids]);
        if ($execute_num <> count($ids)) {
            throw new Exception("删除单据明细失败1");
        }
        if (false === WarehouseBillGoods::deleteAll(['bill_id' => $form->id])) {
            throw new \Exception("删除单据明细失败2");
        }
        $billJ = WarehouseBillJ::findOne($form->id);
        if (false === $billJ->delete()) {
            throw new \Exception($this->getError($billJ));
        }
        if (false === $form->delete()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     *  接收验证
     * @param object $form
     * @throws \Exception
     */
    public function receiveValidate($form)
    {
        $ids = $form->getIds();
        if ($ids && is_array($ids)) {
            foreach ($ids as $id) {
                $goods = WarehouseBillGoodsForm::find()->where(['id' => $id])->select(['goods_id'])->one();
                $goodsJ = WarehouseBillGoodsJ::findOne($id);
                if ($goodsJ->lend_status != LendStatusEnum::IN_RECEIVE) {
                    throw new Exception("货号【{$goods->goods_id}】不是待接收状态");
                }
            }
        } else {
            throw new Exception("ID不能为空");
        }
    }

    /**
     *  借货单-接收
     * @param WarehouseBillJGoodsForm $form
     * @throws \Exception
     */
    public function receiveGoods($form)
    {
//        $ids = $form->getIds();
        $bill = WarehouseBill::findOne($form->id);
        $goodsM = WarehouseBillGoods::find()->select(['id'])->where(['bill_id' => $form->id])->all();
        $ids = ArrayHelper::getColumn($goodsM, 'id');
        if (!$ids && !is_array($ids)) {
            throw new \Exception("ID不能为空");
        }
        //同步更新明细关系表
        $update = [
            'lend_status' => LendStatusEnum::HAS_LEND,
            'receive_id' => \Yii::$app->user->identity->getId(),
            'receive_time' => time(),
            'receive_remark' => $form->receive_remark,
        ];
        $execute_num = WarehouseBillGoodsJ::updateAll($update, ['id' => $ids, 'lend_status' => LendStatusEnum::IN_RECEIVE]);
        if ($execute_num <> count($ids)) {
            //throw new \Exception("同步更新明细关系表失败");
        }

        //同步借货单附表
        WarehouseBillJ::updateAll($update, ['id' => $form->id]);
    }

    /**
     *  还货验证
     * @param object $form
     * @throws \Exception
     */
    public function returnValidate($form)
    {
        $ids = $form->getIds();
        if ($ids && is_array($ids)) {
            foreach ($ids as $id) {
                $goods = WarehouseBillGoodsForm::find()->where(['id' => $id])->select(['status', 'goods_id'])->one();
                $goodsJ = WarehouseBillGoodsJ::findOne($id);
                if ($goodsJ->lend_status == LendStatusEnum::HAS_RETURN) {
                    throw new Exception("货号【{$goods->goods_id}】已还货");
                }
            }
        } else {
            throw new Exception("ID不能为空");
        }
    }

    /**
     *  借货单-还货
     * @param WarehouseBillJGoodsForm $form
     * @throws \Exception
     */
    public function returnGoods($form)
    {
        $ids = $form->getIds();
        if (!$ids && !is_array($ids)) {
            throw new \Exception("ID不能为空");
        }

        foreach ($form->goods_list as $id => $item) {
            $bGoods = WarehouseBillGoods::findOne($id);
            $restore_num = $item['restore_num'] ?? 0;
            if (!is_numeric($restore_num) || $restore_num <= 0) {
                throw new \Exception("[{$bGoods->goods_id}]还货数量必须大于0");
            }
            $goodsJ = WarehouseBillGoodsJ::findOne($id);
            //最大还货数量
            $max_num = $bGoods->goods_num - $goodsJ->restore_num;
            if ($restore_num > $max_num) {
                throw new \Exception("[{$bGoods->goods_id}]还货数量不能大于{$max_num}");
            }

            //总还货数量
            $restore_num_total = $goodsJ->restore_num + $restore_num;
            $is_over = $restore_num_total == $bGoods->goods_num ? true : false;//是否还货完结
            //同步更新明细关系表
            $update = [
                'lend_status' => $is_over ? LendStatusEnum::HAS_RETURN : LendStatusEnum::PORTION_RETURN,
                'qc_status' => $form->qc_status,
                'restore_num' => $restore_num_total,
                'restore_time' => $form->restore_time ? strtotime($form->restore_time) : 0,
                'qc_remark' => $form->qc_remark,
            ];
            $execute_num = WarehouseBillGoodsJ::updateAll($update, ['id' => $id]);//, 'lend_status' => LendStatusEnum::HAS_LEND
            if ($execute_num <> count([$id])) {
                throw new \Exception("同步更新明细关系表失败");
            }
            //同步更新商品库存状态
            $goods = WarehouseGoods::findOne(['goods_id' => $bGoods->goods_id]);
            $goods->stock_num = $goods->stock_num + $restore_num;
            $goods->goods_status = GoodsStatusEnum::IN_STOCK;
            if (false === $goods->save(true, ['goods_id', 'stock_num', 'goods_status'])) {
                throw new \Exception("[{$goods->goods_id}]单据明细添加失败");
            }
            $log_msg = "货品状态变更为:“" . GoodsStatusEnum::getValue($goods->goods_status) . "”;";
            //插入商品日志
            $log = [
                'goods_id' => $goods->id,
                'goods_status' => $goods->goods_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_msg' => '借货单: ' . $bGoods->bill_no . ";" . $log_msg . "还货数量: " . $restore_num,
            ];
            Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
        }

        //同步更新单据附表
        $billJ = WarehouseBillJ::findOne($form->bill_id);
        $count = WarehouseBillGoodsJ::find()->where(['bill_id' => $form->bill_id, 'lend_status' => [LendStatusEnum::HAS_LEND, LendStatusEnum::PORTION_RETURN]])->count();
        if ($count > 0) {
            $billJ->lend_status = LendStatusEnum::PORTION_RETURN;
        } else {
            $billJ->lend_status = LendStatusEnum::HAS_RETURN;
        }
        $billJ->rel_restore_time = time();
        if (false === $billJ->save()) {
            throw new \Exception($this->getError($billJ));
        }

        //同步更新借货单附表
        $this->goodsJSummary($form->bill_id);
    }

    /**
     *  明细汇总
     * @param int $bill_id
     * @throws \Exception
     */
    public function goodsJSummary($bill_id)
    {
        $sum = WarehouseBillGoods::find()
            ->select(['sum(goods_num) as goods_num', 'sum(cost_price) as total_cost', 'sum(sale_price) as total_sale', 'sum(market_price) as total_market'])
            ->where(['bill_id' => $bill_id, 'status' => StatusEnum::ENABLED])
            ->asArray()->one();
        if ($sum) {
            WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_sale' => $sum['total_sale'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        $goods = WarehouseBillGoods::find()->select(['id'])->where(['bill_id' => $bill_id])->all();
        if ($goods) {
            $ids = ArrayHelper::getColumn($goods, 'id');
            $restore_num = WarehouseBillGoodsJ::find()->where(['id' => $ids, 'lend_status' => [LendStatusEnum::PORTION_RETURN, LendStatusEnum::HAS_RETURN]])->sum('restore_num');
            $billJ = WarehouseBillJ::findOne($bill_id);
            $billJ->restore_num = $restore_num ?? 0;
            if (false === $billJ->save(true, ['restore_num'])) {
                throw new \Exception($this->getError($billJ));
            }
        }
    }

    /**
     * 更改借货数量
     * @param int $id 单据明细ID
     * @param int $receive_num 借货数量
     * @return boolean
     * @throws \Exception
     */
    public function updateLendNum($id, $lend_num)
    {
        if ($lend_num < 0) {
            throw new \Exception("借货数量不能小于0");
        }

        $billGoods = WarehouseBillGoods::find()->where(['id' => $id])->one();
        if (empty($billGoods)) {
            throw new \Exception("不可更改,明细查询失败");
        }

        $goods = WarehouseGoods::find()
            ->select(['id', 'goods_id', 'goods_status', 'goods_num', 'stock_num'])
            ->where(['goods_id' => $billGoods->goods_id])//, 'goods_status' => GoodsStatusEnum::IN_LEND
            ->one();
        if (empty($goods)) {
            throw new \Exception("不可更改,商品状态异常");
        }
        $max_num = $goods->stock_num - $goods->do_chuku_num + $billGoods->goods_num;

        if ($lend_num > $max_num) {
            throw new \Exception("借货数量不能大于{$max_num}");
        }

        $goods->stock_num = ($goods->stock_num + $billGoods->goods_num) - $lend_num;
        $goods->do_chuku_num = $goods->do_chuku_num + $lend_num;
        if (false === $goods->save(true, ['goods_id', 'stock_num', 'do_chuku_num'])) {
            throw new \Exception($this->getError($goods));
        }

        $billGoods->goods_num = $lend_num;
        if (false === $billGoods->save(true, ['goods_num'])) {
            throw new \Exception($this->getError($billGoods));
        }
        //汇总统计
        $this->goodsJSummary($billGoods->bill_id);

        //更新收货单汇总：总金额和总数量
//        if (false === \Yii::$app->warehouseService->bill->WarehouseBillSummary($billGoods->bill_id)) {
//            throw new \Exception('更新单据汇总失败');
//        }
        return true;
    }
}