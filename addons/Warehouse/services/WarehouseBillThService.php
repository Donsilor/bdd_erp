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
use addons\Warehouse\common\forms\WarehouseBillThForm;

/**
 * 其它退货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillThService extends WarehouseBillService
{
    public $billFix = BillFixEnum::BILL_CK;
    
    /**
     * 批量添加其它退货单明细
     * @param WarehouseBillThForm $form
     * @param array $saveGoods
     * @throws
     */
    public function batchAddGoods($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        foreach ($form->goods_list ?? [] as $goods_id=>$goods) {
            $wareGoods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(empty($goods)) {
                throw new \Exception("[{$goods_id}]条码货号不存在");
            }
            if($goods['return_num'] <= 0) {
                throw new \Exception("[{$goods_id}]退货数量必须大于0");
            }
            $wareGoods->return_num = $goods['return_num'];
            $this->createBillGoodsByGoods($form, $wareGoods);
        }        
        //更新收货单汇总：总金额和总数量
        if(false === $this->billSummary($form->id)){
            throw new \Exception('更新单据汇总失败');
        }
    }
    
    /**
     * 扫码添加退货单明细
     * @param int $bill_id
     * @param array $goods_ids
     */
    public function scanAddGoods($bill_id, $goods_ids)
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
                $goods = WarehouseGoods::find()->where(['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_REFUND])->one();
                if(empty($goods)) {
                    throw new \Exception("[{$billGoods->goods_id}]商品不是退货中");
                }
                $goods->goods_status = GoodsStatusEnum::IN_STOCK;
                if(false === $goods->save(true,['goods_status'])){
                    throw new \Exception("[{$goods->goods_id}]商品状态更新失败");
                }
                //插入商品日志
                $log = [
                        'goods_id' => $goods->id,
                        'goods_status' => $goods->goods_status,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_msg' => '其他退货单：'.$form->bill_no.";退货数量：".$billGoods->goods_num
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
                $goods = WarehouseGoods::find()->where(['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_REFUND])->one();
                if(empty($goods)) {
                    throw new \Exception("[{$goods->goods_id}]商品状态不是退货中");
                }
                $goods->stock_num = $goods->stock_num - $billGoods->goods_num;
                $goods->goods_status = $goods->stock_num <=0 ? GoodsStatusEnum::HAS_SOLD : GoodsStatusEnum::IN_STOCK;
                if(false === $goods->save(true,['goods_id','stock_num','goods_status'])){
                    throw new \Exception("取消失败");
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
        if($form->bill_status != BillStatusEnum::CANCEL) {
             throw new \Exception("单据不是取消状态");
        }
        //删除明细
        WarehouseBillGoods::deleteAll(['bill_id' => $form->id]);
        WarehouseBill::deleteAll(['id' => $form->id]);
        WarehouseBillLog::deleteAll(['bill_id' => $form->id]);
    }
    
    /**
     * 其它退货单明细-删除
     * @param array(int) | int  $ids  单据明细id
     * @throws
     */
    public function deleteGoods($ids)
    {
        //更新库存状态
        $billGoodsList = WarehouseBillGoods::find()->select(['goods_id','goods_num'])->where(['id' => $ids])->all();
        if($billGoodsList){
            foreach ($billGoodsList as $billGoods){
                $goods = WarehouseGoods::find()->where(['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_REFUND])->one();
                if(empty($goods)) {
                    throw new \Exception("删除失败,商品状态异常");
                }
                $goods->stock_num = $goods->stock_num - $billGoods->goods_num;
                $goods->goods_status = $goods->stock_num <=0 ? GoodsStatusEnum::HAS_SOLD : GoodsStatusEnum::IN_STOCK;
                if(false === $goods->save(true,['goods_id','stock_num','goods_status'])){
                    throw new \Exception("[{$goods->goods_id}]删除失败");
                }
            }
            //删除明细
            WarehouseBillGoods::deleteAll(['id' => $ids]);
        }
    }
    /**
     * 退货单据汇总
     * @param unknown $id
     */
    public function billSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoods::find()
                ->select(['sum(goods_num) as goods_num', 'sum(cost_price * goods_num) as total_cost', 'sum(sale_price) as total_sale', 'sum(market_price) as total_market'])
                ->where(['bill_id' => $bill_id, 'status' => StatusEnum::ENABLED])
                ->asArray()->one();
        if ($sum) {
            $result = WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_sale' => $sum['total_sale'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        return $result;
    }
    /**
     * 更改退货数量
     * @param int $id 单据明细ID
     * @param int $goods_num 退货数量
     * @throws \Exception
     * @return boolean
     */
    public function updateReturnNum($id, $return_num)
    {
        if($return_num < 0) {
            throw new \Exception("退货数量不能小于0");
        }
        
        $billGoods = WarehouseBillGoods::find()->where(['id'=>$id])->one();
        if(empty($billGoods)) {
            throw new \Exception("不可更改,明细查询失败");
        }
        
        $goods = WarehouseGoods::find()
            ->select(['id','goods_id','goods_status','goods_num','stock_num'])
            ->where(['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_REFUND])
            ->one();
        if(empty($goods)) {
            throw new \Exception("不可更改,商品状态异常");
        }
        $max_num = $goods->goods_num - $goods->stock_num - $goods->do_chuku_num + $billGoods->goods_num;

        if($return_num > $max_num) {
            throw new \Exception("退货数量不能大于{$max_num}");
        }
                
        $goods->stock_num = $goods->stock_num + ($return_num - $billGoods->goods_num);
        if(false === $goods->save(true,['stock_num'])) {
            throw new \Exception($this->getError($goods));
        }  
        
        $billGoods->goods_num = $return_num;
        if(false === $billGoods->save(true,['goods_num'])) {
            throw new \Exception($this->getError($billGoods));
        }        
        //汇总统计
        $this->billSummary($billGoods->bill_id);        
        return true;
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
        $return_num = $goods->return_num ? $goods->return_num : 0;        
        //最大退货数量
        $max_num = $goods->goods_num - $goods->stock_num - $goods->do_chuku_num;
        if($return_num > $max_num) {
            throw new \Exception("[{$goods->goods_id}]退货数量不能大于{$max_num}");
        }
        if($max_num <= 0 || !in_array($goods->goods_status,[GoodsStatusEnum::IN_STOCK,GoodsStatusEnum::HAS_SOLD])) {
            throw new \Exception("[{$goods_id}]不满足退货条件");
        }
        if($max_num == 1) {
            $return_num = 1;
        }
        $billGoods = new WarehouseBillGoods();
        $billGoods->attributes = [
                'bill_id' =>$bill->id,
                'bill_no' =>$bill->bill_no,
                'bill_type'=>$bill->bill_type,
                'goods_id'=>$goods_id,
                'goods_name'=>$goods->goods_name,
                'style_sn'=>$goods->style_sn,
                'goods_num'=>$return_num,
                'put_in_type'=>$goods->put_in_type,
                'warehouse_id'=>$goods->warehouse_id,
                'from_warehouse_id'=>$goods->warehouse_id,
                'to_warehouse_id'=>$goods->warehouse_id,
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
                'market_price'=>$goods->market_price,
                'markup_rate'=>$goods->markup_rate,
        ];
        if(false === $billGoods->save()) {
            throw new \Exception("[{$goods_id}]".$this->getError($billGoods));
        }
        $goods->stock_num = $goods->stock_num + $return_num;
        $goods->goods_status = GoodsStatusEnum::IN_REFUND;
        if(false === $goods->save(true,['goods_id','stock_num','goods_status'])){
            throw new \Exception("[{$goods->goods_id}]单据明细添加失败");
        }  
        return $billGoods;
    }
}