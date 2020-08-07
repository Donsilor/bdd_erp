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
use addons\Warehouse\common\enums\LendStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
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
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if(!$form->lender_id){
            throw new \Exception("借货人不能为空");
        }
        if(!$form->est_restore_time){
            throw new \Exception("预计还货日期不能为空");
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }

        //创建借货单附表
        $billJ = WarehouseBillJ::findOne($form->id);
        $billJ = $billJ ?? new WarehouseBillJ();
        $billJ->id = $form->id;
        $billJ->lender_id = $form->lender_id;
        $billJ->est_restore_time = $form->est_restore_time;
        $billJ->afterValidate();

        if(false === $billJ->save()){
            throw new \Exception($this->getError($billJ));
        }
    }

    /**
     * 创建借货单明细
     * @param WarehouseBillJGoodsForm $form
     * @param array $bill_goods
     * @throws
     *
     */
    public function createBillGoodsJ($form, $bill_goods)
    {
        $bill = WarehouseBillJForm::find()->where(['id' => $form->bill_id])->one();

        //批量创建单据明细
        $goods_val = [];
        $goods_id_arr = [];
        foreach ($bill_goods as &$goods) {
            $goods_id = $goods['goods_id'];
            $goods_id_arr[] = $goods_id;
            $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods_info)){
                throw new \Exception("货号{$goods_id}不存在或者不是库存中");
            }

            //是否维修中
            //\Yii::$app->warehouseService->repair->checkRepairStatus($goods);
            $goods['bill_id'] = $bill->id;
            $goods['bill_no'] = $bill->bill_no;
            $goods['bill_type'] = $bill->bill_type;
            $goods['warehouse_id'] = $goods_info->warehouse_id;
            $goods['put_in_type'] = $goods_info->put_in_type;

            $goods_key = array_keys($goods);
            $goods_val[] = array_values($goods);
            if(count($goods_val) > 10){
                $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
                if(false === $res){
                    throw new \Exception('创建单据明细失败1');
                }
                $goods_val = [];
            }
        }
        if(!empty($goods_val)){
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
            if(false === $res){
                throw new \Exception('创建单据明细失败2');
            }
        }
        WarehouseBillGoodsJ::deleteAll(['bill_id'=>$bill->id]);
        //同步单据明细关系表
        $sql = "INSERT INTO ".WarehouseBillGoodsJ::tableName()."(id,bill_id,lend_status,qc_status) SELECT id,bill_id,0,0 FROM ".WarehouseBillGoods::tableName()." WHERE bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('创建单据明细失败3');
        }
        //更新商品库存状态
        $condition = ['goods_id'=>$goods_id_arr, 'goods_status' => GoodsStatusEnum::IN_STOCK];
        $execute_num = WarehouseGoods::updateAll(['goods_status'=> GoodsStatusEnum::IN_LEND], $condition);

        if($execute_num <> count($bill_goods)){
            throw new Exception("货品改变状态数量与明细数量不一致");
        }
        //更新收货单汇总：总金额和总数量
        $res = \Yii::$app->warehouseService->bill->WarehouseBillSummary($bill->id);
        if(false === $res){
            throw new Exception('更新单据汇总失败');
        }
    }

    /**
     * 借货单-审核
     * @param WarehouseBill $form
     * @throws
     */
    public function auditBillC($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $billJ = WarehouseBillJ::findOne($form->id);
        if($form->audit_status == AuditStatusEnum::PASS){
            $goods = WarehouseBillGoods::find()->select(['id', 'goods_id'])->where(['bill_id' => $form->id])->all();
            if(!$goods){
                throw new \Exception("单据明细不能为空");
            }
            //更新单据明细状态
            $ids = ArrayHelper::getColumn($goods, 'id');
            $execute_num = WarehouseBillGoodsJ::updateAll(['lend_status' => LendStatusEnum::IN_RECEIVE], ['id' => $ids, 'lend_status' => LendStatusEnum::SAVE]);
            if($execute_num <> count($ids)){
                throw new \Exception("同步更新商品明细状态失败");
            }
            $form->bill_status = BillStatusEnum::CONFIRM;
            $billJ->lend_status = LendStatusEnum::HAS_LEND;
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
            $billJ->lend_status = LendStatusEnum::SAVE;
        }
        if(false === $billJ->save()){
            throw new \Exception($this->getError($billJ));
        }
        if(false === $form->save()) {
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
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
        if($billGoods){
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_LEND]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是借货中或者不存在，请查看原因");
                }
            }
        }
        $form->bill_status = BillStatusEnum::CANCEL;
        if(false === $form->save()){
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
        if($billGoods){
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id]);//'goods_status' => GoodsStatusEnum::IN_LEND
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是借货中或者不存在，请查看原因");
                }
            }
        }
        $ids = ArrayHelper::getColumn($billGoods, 'id');
        $execute_num = WarehouseBillGoodsJ::deleteAll(['id'=>$ids]);
        if($execute_num <> count($ids)){
            throw new Exception("删除单据明细失败1");
        }
        if(false === WarehouseBillGoods::deleteAll(['bill_id' => $form->id])){
            throw new \Exception("删除单据明细失败2");
        }
        $billJ = WarehouseBillJ::findOne($form->id);
        if(false === $billJ->delete()){
            throw new \Exception($this->getError($billJ));
        }
        if(false === $form->delete()){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     *  接收验证
     * @param object $form
     * @throws \Exception
     */
    public function receiveValidate($form){
        $ids = $form->getIds();
        if($ids && is_array($ids)){
            foreach ($ids as $id) {
                $goods = WarehouseBillGoodsForm::find()->where(['id'=>$id])->select(['goods_id'])->one();
                $goodsJ = WarehouseBillGoodsJ::findOne($id);
                if($goodsJ->lend_status != LendStatusEnum::IN_RECEIVE){
                    throw new Exception("货号【{$goods->goods_id}】不是待接收状态");
                }
            }
        }else{
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
        $ids = $form->getIds();
        if(!$ids && !is_array($ids)) {
            throw new \Exception("ID不能为空");
        }

        //同步更新明细关系表
        $update = [
            'lend_status'=>LendStatusEnum::HAS_LEND,
            'receive_id'=>\Yii::$app->user->identity->getId(),
            'receive_time'=>time(),
            'receive_remark'=>$form->receive_remark,
        ];
        $execute_num = WarehouseBillGoodsJ::updateAll($update, ['id'=>$ids, 'lend_status'=>LendStatusEnum::IN_RECEIVE]);
        if($execute_num <> count($ids)){
            throw new \Exception("同步更新明细关系表失败");
        }

        //同步更新商品库存状态
        $billGoods = WarehouseBillGoods::find()->where(['id' => $ids])->select(['goods_id'])->all();
        foreach ($billGoods as $goods){
            $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::HAS_LEND],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_LEND]);
            if(!$res){
                throw new \Exception("商品{$goods->goods_id}状态不是借货中或者不存在，请查看原因");
            }
        }
    }

    /**
     *  还货验证
     * @param object $form
     * @throws \Exception
     */
    public function returnValidate($form){
        $ids = $form->getIds();
        if($ids && is_array($ids)){
            foreach ($ids as $id) {
                $goods = WarehouseBillGoodsForm::find()->where(['id'=>$id])->select(['status', 'goods_id'])->one();
                $goodsJ = WarehouseBillGoodsJ::findOne($id);
                if($goodsJ->lend_status != LendStatusEnum::HAS_LEND){
                    throw new Exception("货号【{$goods->goods_id}】不是已借货状态");
                }
            }
        }else{
            throw new Exception("ID不能为空");
        }
    }

    /**
     *  借货单-还货
     * @param WarehouseBillJGoodsForm $form
     * @throws \Exception
     */
    public function returnGoods($form){

        $ids = $form->getIds();
        if(!$ids && !is_array($ids)) {
            throw new \Exception("ID不能为空");
        }

        //同步更新明细关系表
        $update = [
            'lend_status'=>LendStatusEnum::HAS_RETURN,
            'qc_status'=>$form->qc_status,
            'restore_time'=>$form->restore_time?strtotime($form->restore_time):0,
            'qc_remark'=>$form->qc_remark,
        ];
        $execute_num = WarehouseBillGoodsJ::updateAll($update, ['id'=>$ids, 'lend_status'=>LendStatusEnum::HAS_LEND]);
        if($execute_num <> count($ids)){
            throw new \Exception("同步更新明细关系表失败");
        }

        //同步更新商品库存状态
        $billGoods = WarehouseBillGoods::find()->where(['id' => $ids])->select(['goods_id'])->all();
        foreach ($billGoods as $goods){
            $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::HAS_LEND]);
            if(!$res){
                throw new \Exception("商品{$goods->goods_id}状态不是已借货或者不存在，请查看原因");
            }
        }

        //同步更新单据附表
        $billJ = WarehouseBillJ::findOne($form->bill_id);
        $count = WarehouseBillGoodsJ::find()->where(['bill_id' => $form->bill_id, 'lend_status' => LendStatusEnum::HAS_LEND])->count();
        if ($count > 0) {
            $billJ->lend_status = LendStatusEnum::PORTION_RETURN;
        } else {
            $billJ->lend_status = LendStatusEnum::HAS_RETURN;
        }
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
        $goods = WarehouseBillGoods::find()->select(['id'])->where(['bill_id' => $bill_id])->all();
        if ($goods) {
            $ids = ArrayHelper::getColumn($goods, 'id');
            $restore_num = WarehouseBillGoodsJ::find()->where(['id' => $ids, 'lend_status' => LendStatusEnum::HAS_RETURN])->count();
            $billJ = WarehouseBillJ::findOne($bill_id);
            $billJ->restore_num = $restore_num??0;
            if (false === $billJ->save(true, ['restore_num'])) {
                throw new \Exception($this->getError($billJ));
            }
        }
    }
}