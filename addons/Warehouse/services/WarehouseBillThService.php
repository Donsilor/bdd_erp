<?php

namespace addons\Warehouse\services;


use Yii;
use yii\db\Exception;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\BillFixEnum;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillLog;

/**
 * 其它退货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillThService extends WarehouseBillService
{
    public $billFix = BillFixEnum::BILL_CK;
    /**
     * 创建其它退货单明细
     * @param WarehouseBillCForm $form
     * @param array $saveGoods
     * @throws
     *
     */
    public function create($form, $saveGoods)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //批量创建单据明细
        $goods_ids = $goods_val = [];
        foreach ($saveGoods as &$goods) {
            $goods_id = $goods['goods_id'];
            $goods_ids[] = $goods_id;
            $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods_info)){
                throw new \Exception("货号{$goods_id}不存在或者不是库存中");
            }
            $goods['bill_id'] = $form->id;
            $goods['bill_no'] = $form->bill_no;
            $goods['bill_type'] = $form->bill_type;
            $goods['warehouse_id'] = $goods_info->warehouse_id;
            $goods['from_warehouse_id'] = $goods_info->warehouse_id;
            $goods['put_in_type'] = $goods_info->put_in_type;
            $goods_val[] = array_values($goods);
            $goods_key = array_keys($goods);
            if(count($goods_val)>=10){
                $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
                if(false === $res){
                    throw new Exception('创建单据明细失败1');
                }
                $goods_val = [];
            }
        }
        if(!empty($goods_val)){
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
            if(false === $res){
                throw new Exception('创建单据明细失败2');
            }
        }
        foreach ($goods_ids as $goods_id){
            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if($goods->goods_status != GoodsStatusEnum::IN_STOCK) {
                throw new \Exception("[{$goods_id}]货号条码不是库存状态");
            }
            $goods->chuku_price = $goods->getChukuPrice();
            $goods->chuku_time = time();
            $goods->goods_status = GoodsStatusEnum::IN_SALE;
            if(false === $goods->save()){
                throw new Exception('更新库存信息失败');
            }
        }
        //更新收货单汇总：总金额和总数量
        if(false === $this->billCSummary($form->id)){
            throw new Exception('更新单据汇总失败');
        }
    }
    
    /**
     * 扫码添加退货单明细
     * @param int $bill_id
     * @param array $goods_ids
     */
    public function scanGoods($bill_id, $goods_ids)
    {
        $bill = WarehouseBill::find()->where(['id'=>$bill_id,'bill_type'=>BillTypeEnum::BILL_TYPE_TH])->one();
        if(empty($bill) || $bill->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("单据不是保存状态");
        }
        foreach ($goods_ids as $goods_id) {
            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(empty($goods)) {
                throw new \Exception("[{$goods_id}]条码货号不存在");
            }
            $this->createBillGoodsByGoods($bill, $goods);
        }
        //更新收货单汇总：总金额和总数量
        $this->billSummary($bill->id);
        
        return $bill;
    }
    
    /**
     * 其它退货单审核
     * @param WarehouseBillCForm $form
     * @throws
     */
    public function audit($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        
        if($form->bill_status != BillStatusEnum::PENDING) {
            throw new \Exception("单据不是待审核状态");
        }
        
        if($form->audit_status == AuditStatusEnum::PASS){
            
            $form->bill_status = BillStatusEnum::CONFIRM;
            //更新库存状态
            $billGoodsList = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
            if(empty($billGoodsList)) {
                throw new \Exception("单据明细不能为空");
            }
            
            foreach ($billGoodsList as $billGoods){
                $goods = WarehouseGoods::find()->where(['goods_id'=>$billGoods->goods_id,'goods_stock'=>GoodsStatusEnum::IN_REFUND])->one();
                if(empty($goods)) {
                    throw new \Exception("[{$goods->goods_id}]条码货号不是退货中，请查看原因");
                }
                $max_num = $goods->goods_num - $goods->stock_num;
                $goods->stock_num = $goods->stock_num + $billGoods->goods_num;
                $goods->goods_status = GoodsStatusEnum::IN_STOCK;
                if($goods->stock_num > $goods->goods_num) {
                     throw new \Exception("[{$goods->goods_id}]退货数量不能大于{$max_num}");
                }
                if($goods->save(true,['goods_id','stock_num','goods_status'])){
                    throw new \Exception("[{$goods->goods_id}]条码货号状态更新失败");
                }
                //插入商品日志
                $log = [
                        'goods_id' => $goods->goods->id,
                        'goods_status' => GoodsStatusEnum::HAS_SOLD,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_msg' => '其他退货单：'.$form->bill_no.";货品状态:“".GoodsStatusEnum::getValue(GoodsStatusEnum::IN_REFUND)."”变更为：“".GoodsStatusEnum::getValue($goods->goods_status)."”"
                ];
                Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
                
            }
            
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        $form->audit_time = time();
        $form->auditor_id = \Yii::$app->user->identity->getId();
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        
        //日志
        $log = [
                'bill_id' => $form->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其它退货单',
                'log_msg' => "其它退货单审核, 审核状态：" . AuditStatusEnum::getValue($form->audit_status) . ",审核备注：" . $form->audit_remark
        ];
        \Yii::$app->warehouseService->billLog->createBillLog($log);
    }
    
    /**
     * 其它退货单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function cancel($form)
    {
        //更新库存状态
        $billGoodsList = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id' => $form->id])->all();
        if($billGoodsList){
            foreach ($billGoodsList as $billGoods){
                $goods = WarehouseGoods::find()->where(['goods_id'=>$billGoods->goods_id,'goods_stock'=>GoodsStatusEnum::IN_REFUND])->one();
                if(empty($goods)) {
                    throw new \Exception("[{$goods->goods_id}]条码货号不是退货中，请查看原因");
                }
                $goods->stock_num = $goods->stock_num - $billGoods->goods_num;
                $goods->goods_status = $goods->stock_num <=0 ? GoodsStatusEnum::HAS_SOLD : GoodsStatusEnum::IN_STOCK;
                if($goods->save(true,['goods_id','stock_num','goods_status'])){
                    throw new \Exception("[{$goods->goods_id}]条码货号不是退货中，请查看原因");
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
                'log_msg' => '取消其它退货单'
        ];
        \Yii::$app->warehouseService->billLog->createBillLog($log);
    }
    
    /**
     * 其它退货单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function delete($form)
    {
        if($form->bill_status != BillStatusEnum::SAVE) {
             throw new \Exception("单据不是保存状态");
        }
        //更新库存状态
        $billGoodsList = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id' => $form->id])->all();
        if($billGoodsList){
            foreach ($billGoodsList as $billGoods){
                $goods = WarehouseGoods::find()->where(['goods_id'=>$billGoods->goods_id,'goods_stock'=>GoodsStatusEnum::IN_REFUND])->one();
                if(empty($goods)) {
                    throw new \Exception("[{$goods->goods_id}]条码货号不是退货中，请查看原因");
                }
                $goods->stock_num = $goods->stock_num - $billGoods->goods_num;
                $goods->goods_status = $goods->stock_num <=0 ? GoodsStatusEnum::HAS_SOLD : GoodsStatusEnum::IN_STOCK;
                if($goods->save(true,['goods_id','stock_num','goods_status'])){
                    throw new \Exception("[{$goods->goods_id}]条码货号不是退货中，请查看原因");
                }
            }
        }
        //删除明细
        WarehouseBillGoods::deleteAll(['bill_id' => $form->id]);
        WarehouseBill::deleteAll(['id' => $form->id]);
        WarehouseBillLog::deleteAll(['bill_id' => $form->id]);
    }
    /**
     * 退货单据汇总
     * @param unknown $id
     */
    public function billSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoods::find()
                ->select(['sum(1) as goods_num', 'sum(chuku_price) as total_cost', 'sum(sale_price) as total_sale', 'sum(market_price) as total_market'])
                ->where(['bill_id' => $bill_id, 'status' => StatusEnum::ENABLED])
                ->asArray()->one();
        if ($sum) {
            $result = WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_sale' => $sum['total_sale'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        return $result;
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
        if($goods->goods_num <= $goods->stock_num) {
            throw new \Exception("[{$goods_id}]条码货号不可退货");
        }
        
        $billGoods = new WarehouseBillGoods();
        $billGoods->attributes = [
                'bill_id' =>$bill->id,
                'bill_no' =>$bill->bill_no,
                'bill_type'=>$bill->bill_type,
                'goods_id'=>$goods_id,
                'goods_name'=>$goods->goods_name,
                'style_sn'=>$goods->style_sn,
                'goods_num'=>1,
                'put_in_type'=>$goods->put_in_type,
                'warehouse_id'=>$goods->warehouse_id,
                'from_warehouse_id'=>$goods->warehouse_id,
                'material'=>$goods->material,
                'material_type'=>$goods->material_type,
                'material_color'=>$goods->material_color,
                'gold_weight'=>$goods->gold_weight,
                'gold_loss'=>$goods->gold_loss,
                'diamond_carat'=>$goods->diamond_carat,
                'diamond_color'=>$goods->diamond_color,
                'diamond_clarity'=>$goods->diamond_clarity,
                'diamond_cert_id'=>$goods->diamond_cert_id,
                'diamond_cert_type'=>$goods->diamond_cert_type,
                'cost_price'=>$goods->cost_price,//采购成本价
                //'chuku_price'=>$goods->calcChukuPrice(),//计算退货成本价
                'market_price'=>$goods->market_price,
                'markup_rate'=>$goods->markup_rate,
        ];
        if(false === $billGoods->save()) {
            throw new \Exception("[{$goods_id}]".$this->getError($billGoods));
        }
        $res = WarehouseGoods::updateAll(['chuku_price'=>$billGoods->chuku_price,'chuku_time'=>time(),'goods_status'=>GoodsStatusEnum::IN_SALE],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_STOCK]);
        if(!$res) {
            throw new \Exception("[{$billGoods->goods_id}]条码货号不是库存中");
        }
    }
}