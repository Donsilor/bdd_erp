<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\models\WarehouseBill;
use Yii;
use yii\db\Exception;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\LendStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\helpers\UploadHelper;
use common\helpers\ExcelHelper;
use addons\Sales\common\models\SaleChannel;
use common\helpers\SnHelper;

/**
 * 其它出库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillCService extends WarehouseBillService
{

    /**
     * 创建其它出库单明细
     * @param WarehouseBillCForm $form
     * @param array $bill_goods
     * @throws
     *
     */
    public function createBillGoodsC($form, $bill_goods)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //批量创建单据明细
        $goods_ids = $goods_val = [];
        foreach ($bill_goods as &$goods) {
            $goods_id = $goods['goods_id'];
            $goods_ids[] = $goods_id;
            $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods_info)){
                throw new Exception("货号{$goods_id}不存在或者不是库存中");
            }
            $goods['bill_id'] = $form->id;
            $goods['bill_no'] = $form->bill_no;
            $goods['bill_type'] = $form->bill_type;
            $goods['warehouse_id'] = $goods_info->warehouse_id;
            $goods['put_in_type'] = $goods_info->put_in_type;
            $goods_val[] = array_values($goods);
            $goods_key = array_keys($goods);
            if(count($goods_val)>=10){
                $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
                if(false === $res){
                    throw new Exception('更新单据汇总失败1');
                }
                $goods_val = [];
            }
        }
        if(!empty($goods_val)){
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
            if(false === $res){
                throw new Exception('更新单据汇总失败2');
            }
        }
        //更新商品库存状态
        if(in_array($form->delivery_type, [DeliveryTypeEnum::QUICK_SALE, DeliveryTypeEnum::PLATFORM]))
        {
            $status = GoodsStatusEnum::IN_SALE;
        }else{
            //其它出库类型
            $status = GoodsStatusEnum::IN_STOCK;//待定
        }
//        $execute_num = WarehouseGoods::updateAll(['goods_status'=> $status],['goods_id'=>$goods_ids, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
//        if($execute_num <> count($bill_goods)){
//            throw new Exception("货品改变状态数量与明细数量不一致");
//        }

        foreach ($goods_ids as $goods_id){
            $outbound_cost = Yii::$app->warehouseService->warehouseGoods->getOutboundCost($goods_id);
            $res = WarehouseGoods::updateAll(['goods_status'=> $status,'outbound_cost'=>$outbound_cost],['goods_id'=>$goods_id, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
            if(false === $res){
                throw new Exception('更新库存信息失败');
            }
        }

        //更新收货单汇总：总金额和总数量
        $res = \Yii::$app->warehouseService->bill->WarehouseBillSummary($form->id);
        if(false === $res){
            throw new Exception('更新单据汇总失败');
        }
    }

    /**
     * 其它出库单审核
     * @param WarehouseBillCForm $form
     * @throws
     */
    public function auditBillC($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        $billGoods = WarehouseBillGoods::find()->select(['id', 'goods_id'])->where(['bill_id' => $form->id])->asArray()->all();
        if(empty($billGoods)){
            throw new \Exception("单据明细不能为空");
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $goods_ids = ArrayHelper::getColumn($billGoods, 'goods_id');
            //更新商品库存状态
            if(in_array($form->delivery_type, [DeliveryTypeEnum::QUICK_SALE, DeliveryTypeEnum::PLATFORM])){
                $status = GoodsStatusEnum::HAS_SOLD;
                $conStatus = GoodsStatusEnum::IN_SALE;
            }else{
                //其它出库类型
                $status = GoodsStatusEnum::IN_STOCK;//待定
                $conStatus = GoodsStatusEnum::IN_STOCK;//待定
            }
            $condition = ['goods_status' => $conStatus, 'goods_id' => $goods_ids];
            $res = WarehouseGoods::updateAll(['goods_status' => $status], $condition);
            if(false === $res){
                throw new \Exception("更新货品状态失败");
            }
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 其它出库单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function cancelBillC($form)
    {
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
        if($billGoods){
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不存在，请查看原因");
                }
            }
        } 
        $form->bill_status = BillStatusEnum::CANCEL;
        if(false && false === $form->save()){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 其它出库单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function deleteBillC($form)
    {
        //删除明细
        $res = WarehouseBillGoods::deleteAll(['bill_id' => $form->id]);
        if(false === $res){
            throw new \Exception("删除明细失败");
        }
        if(false === $form->delete()){
            throw new \Exception($this->getError($form));
        }
    }
    
    /**
     * 其它出库单导入
     * @param unknown $form
     * @throws \Exception
     */
    public function importBillC($form)
    {
        if (!($form->file->tempName ?? true)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'xlsx') {
            throw new \Exception("请上传xlsx格式文件");
        }
        $columnMap = [
              1=>'goods_id',
              2=>'channel_id',
              3=>'order_sn',
              4=>'saleman',  
        ];
        $requredColumns = [
            'goods_id',
            'channel_id',  
        ];
        $specialColumns = [
            'channel_id',
        ];
        
        $userMap = \Yii::$app->services->backendMember->getDropDown();
        $userMap = array_flip($userMap);
        
        $startRow = 2;
        $endColumn = 4;
        $rows = ExcelHelper::import($form->file->tempName, $startRow,$endColumn,$columnMap);//从第1行开始,第4列结束取值      
        if(!isset($rows[3])) {
            throw new \Exception("导入数据不能为空");
        }        
        //1.数据校验及格式化
        foreach ($rows as $rowKey=> & $row) {
             if($rowKey == $startRow) {
                 $rtitle = $row;
                 continue;
             }
             foreach ($row as $colKey=> $colValue) {
                 //必填校验
                 if(in_array($colKey,$requredColumns) && $colValue == '') {
                     throw new \Exception($rtitle[$colKey]."不能为空");
                 }
                 if(in_array($colKey,$specialColumns)) {
                     if(preg_match("/^(\d+?)\.(.*)/is", $colValue,$matches) && count($matches) ==3) {
                         $row[$colKey] = $matches[1];
                     }else {
                         throw new \Exception($rtitle[$colKey]."填写格式错误");
                     }
                 }                 
             }
             $goods_id = $row['goods_id'] ?? 0;
             $channel_id = $row['channel_id'] ?? 0;
             $saleman  = $row['saleman'] ?? '';
             $groupKey = $channel_id;
             if($saleman && !($saleman_id = $userMap[$saleman])) {
                 throw new \Exception("[{$saleman}]销售人不存在");
             }
             $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
             if(empty($goods)) {
                 throw new \Exception("[{$goods_id}]条码货号不存在");
             }else if($goods->goods_status != GoodsStatusEnum::IN_STOCK) {
                 throw new \Exception("[{$goods_id}]条码货号不是库存状态");
             }  
             $chuku_price = Yii::$app->warehouseService->warehouseGoods->calcChukuPrice($goods);
             $billGroup[$groupKey] = [
                  'channel_id'=>$channel_id,
                  'saleman_id' =>$saleman_id,
             ];
             $billGoodsGroup[$groupKey][] = [ 
                'goods_id'=>$goods_id,
                'goods_name'=>$goods->goods_name,
                'style_sn'=>$goods->style_sn,
                'goods_num'=>1,
                'put_in_type'=>$goods->put_in_type,
                'warehouse_id'=>$goods->warehouse_id,
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
                'chuku_price'=>$chuku_price,//出库成本价
                'market_price'=>$goods->market_price,
                'markup_rate'=>$goods->markup_rate,                   
             ];
        }
        
        foreach ($billGroup as $groupKey=>$billInfo) {
            $billInfo = ArrayHelper::merge($billInfo, $form->toArray());
            $bill = new WarehouseBill();            
            $bill->attributes = $billInfo;
            $bill->bill_no = SnHelper::createBillSn($form->bill_type);
            if(false == $bill->save()){
                throw new \Exception("导入失败:".$this->getError($bill));
            }            
            foreach ($billGoodsGroup[$groupKey]??[] as $goodsInfo) {
                $billGoods = new WarehouseBillGoods();
                $billGoods->attributes = $goodsInfo;
                $billGoods->bill_id= $bill->id;
                $billGoods->bill_no = $bill->bill_no;
                $billGoods->bill_type = $bill->bill_type;
                if(false == $billGoods->save()) {
                    throw new \Exception("导入失败:".$this->getError($billGoods));
                }
                $res = WarehouseGoods::updateAll(['outbound_cost'=>$billGoods->chuku_price,'goods_status'=>GoodsStatusEnum::IN_SALE],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_STOCK]);
                if(!$res) {
                    throw new \Exception("[{$billGoods->goods_id}]条码货号不是库存中");
                }
            }
            $this->billCSummary($bill->id);
        }
    }
    /**
     * 出库单据汇总
     * @param unknown $id
     */
    public function billCSummary($id)
    {
        $this->warehouseBillSummary($id);
    }
}