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
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;

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
     * 扫码添加出库单明细
     * @param int $bill_id
     * @param array $goods_ids
     */
    public function scanGoods($bill_id, $goods_ids)
    {
        $bill = WarehouseBill::find()->where(['id'=>$bill_id,'bill_type'=>BillTypeEnum::BILL_TYPE_C])->one();
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
        $this->billCSummary($bill->id);
        
        return $bill;
    }
    
    /**
     * 快捷创建出库单（先挑选商品）
     * @param WarehouseBillCForm $form
     */
    public function quickBillC($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill = new WarehouseBill();
        $bill->attributes = $form->toArray();
        $bill->bill_no = SnHelper::createBillSn($form->bill_type);
        if(false === $bill->save()) {
            throw new \Exception($this->getError($bill));
        }
        //商品主键id数组
        $ids = $form->getGoodsIds();
        foreach ($ids as $id) {
            $goods = WarehouseGoods::find()->where(['id'=>$id])->one();
            if(empty($goods)) {
                throw new \Exception("[{$id}]商品查询失败");
            }
            $this->createBillGoodsByGoods($bill, $goods);
        }
        //更新收货单汇总：总金额和总数量
        $this->billCSummary($bill->id);
        
        //单据日志
        $log = [
                'bill_id' => $bill->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '快捷创建',
                'log_msg' => '快捷创建其它出库单，单据编号：'.$bill->bill_no
        ];
        \Yii::$app->warehouseService->billLog->createBillLog($log);
        
        return $bill;
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
        
        if($form->bill_status != BillStatusEnum::PENDING) {
            throw new \Exception("单据不是待审核状态");
        }
        
        if($form->audit_status == AuditStatusEnum::PASS){
            
            $form->bill_status = BillStatusEnum::CONFIRM;            
            //更新库存状态
            $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
            if(empty($billGoods)) {
                throw new \Exception("单据明细不能为空");
            }
            
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::HAS_SOLD,'chuku_time'=>time()],['goods_id' => $goods->goods_id]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不存在，请查看原因");
                }

                //插入商品日志
                $log = [
                    'goods_id' => $goods->goods->id,
                    'goods_status' => GoodsStatusEnum::HAS_SOLD,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_msg' => '其他出库单：'.$form->bill_no.";货品状态:“".GoodsStatusEnum::getValue(GoodsStatusEnum::IN_STOCK)."”变更为：“".GoodsStatusEnum::getValue(GoodsStatusEnum::HAS_SOLD)."”"
                ];
                Yii::$app->warehouseService->goodsLog->createGoodsLog($log);

            }


            
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
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
        $billGoods = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id' => $form->id])->all();
        if($billGoods){
            foreach ($billGoods as $goods){                
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK,'chuku_time'=>null],['goods_id' => $goods->goods_id]);
                if(!$res){
                    throw new \Exception("商品{$goods->goods_id}不存在，请查看原因");
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
        \Yii::$app->warehouseService->billLog->createBillLog($log);
    }

    /**
     * 其它出库单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function deleteBillC($form)
    {
        //删除明细
        WarehouseBillGoods::deleteAll(['bill_id' => $form->id]);
        if(false === $form->delete()){
            throw new \Exception($this->getError($form));
        }
        
        $log = [
                'bill_id' => $form->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据删除',
                'log_msg' => '取消其它出库单'
        ];
        \Yii::$app->warehouseService->billLog->createBillLog($log);
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
              4=>'salesman',  
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
        $errors = [];
        //1.数据校验及格式化
        foreach ($rows as $rowKey=> & $row) {
             if($rowKey == $startRow) {
                 $rtitle = $row;
                 continue;
             }
             foreach ($row as $colKey=> $colValue) {
                 //必填校验
                 if(in_array($colKey,$requredColumns) && $colValue === '') {
                     $errors[$rowKey][$colKey] = "不能为空";
                     //throw new \Exception($rtitle[$colKey]."不能为空");
                 }
                 if(in_array($colKey,$specialColumns)) {
                     if(preg_match("/^(\d+?)\.(.*)/is", $colValue,$matches) && count($matches) == 3) {
                         $row[$colKey] = $matches[1];
                     }else {
                         $errors[$rowKey][$colKey] = "[{$colValue}]格式错误";
                         //throw new \Exception($rtitle[$colKey]."填写格式错误");
                     }
                 }                 
             }
             $goods_id = $row['goods_id'] ?? 0;
             $channel_id = $row['channel_id'] ?? 0;
             $salesman  = $row['salesman'] ?? '';
             $groupKey = $channel_id;
             if($salesman && !($salesman_id = $userMap[$salesman]??0)) {
                 $errors[$rowKey]['salesman'] = "[{$salesman}]系统不存在";
                 //throw new \Exception("[{$salesman}]销售人不存在");
             }
             
             $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
             if(empty($goods)) {
                 $errors[$rowKey]['goods_id'] = "[{$goods_id}]系统不存在";
                 //throw new \Exception("[{$goods_id}]条码货号不存在");
             }else if($goods->goods_status != GoodsStatusEnum::IN_STOCK) {
                 $errors[$rowKey]['goods_id'] = "[{$goods_id}]不是库存状态";
                 //throw new \Exception("[{$goods_id}]条码货号不是库存状态");
             }  
             //发生错误
             if(!empty($errors)) {
                 continue;   
             }
                 
             $billGroup[$groupKey] = [
                  'channel_id'=>$channel_id,
                  'salesman_id' =>$salesman_id,
             ];             
             $billGoodsGroup[$groupKey][] = [ 
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
                'chuku_price'=>$goods->calcChukuPrice(),//出库成本价
                'market_price'=>$goods->market_price,
                'markup_rate'=>$goods->markup_rate,                   
             ];
            
        }
        if ($errors) {
            //发生错误
            $message = "";
            foreach ($errors as $k => $error) {
                $message .= '第' . ($k) . '行：';
                foreach ($columnMap as $code) {
                     if(isset($error[$code])) {
                         $message .= "【".$rtitle[$code]."=>值".$error[$code]."】";
                     }
                }
                $message .= PHP_EOL;
            }
            header("Content-Disposition: attachment;filename=错误提示" . date('YmdHis') . ".log");
            echo iconv("utf-8", "gbk", $message);
            exit();
        }

        foreach ($billGroup as $groupKey=>$billInfo) {
            $billInfo = ArrayHelper::merge($billInfo, $form->toArray());
            $bill = new WarehouseBill();            
            $bill->attributes = $billInfo;
            $bill->bill_type = BillTypeEnum::BILL_TYPE_C;
            $bill->bill_no = SnHelper::createBillSn($form->bill_type);
            $bill->bill_status = BillStatusEnum::SAVE;
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
                $res = WarehouseGoods::updateAll(['chuku_price'=>$billGoods->chuku_price,'chuku_time'=>time(),'goods_status'=>GoodsStatusEnum::IN_SALE],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_STOCK]);
                if(!$res) {
                    throw new \Exception("[{$billGoods->goods_id}]条码货号不是库存中");
                }
            }
            $this->billCSummary($bill->id);
            
            //日志
            $log = [
                    'bill_id' => $bill->id,
                    'bill_status'=>$bill->bill_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '批量导入',
                    'log_msg' => '批量导入其它出库单，单据编号：'.$bill->bill_no
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            
        }
    }    
    /**
     * 出库单据汇总
     * @param unknown $id
     */
    public function billCSummary($bill_id)
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
        if($goods->goods_status != GoodsStatusEnum::IN_STOCK) {
            throw new \Exception("[{$goods_id}]条码货号不是库存状态");
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
                'chuku_price'=>$goods->calcChukuPrice(),//计算出库成本价
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