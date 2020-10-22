<?php

namespace addons\Warehouse\services;

use Yii;
use yii\db\Exception;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\BillFixEnum;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\UploadHelper;
use common\helpers\ExcelHelper;

/**
 * 其它出库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillCService extends WarehouseBillService
{

    public $billFix = BillFixEnum::BILL_CK;

    /**
     * 创建其它出库单明细
     * @param WarehouseBillCForm $form
     * @param array $saveGoods
     * @throws
     *
     */
    public function batchAddGoods($form, $saveGoods)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //批量创建单据明细
        foreach ($saveGoods as $good) {
            $goods_id = $good['goods_id'];
            $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods)){
                throw new \Exception("货号{$goods_id}不存在或者不是库存中");
            }            
            $this->createBillGoodsByGoods($form, $goods);        
        }
        $this->billCSummary($form->id);        
    }
    
    /**
     * 扫码添加出库单明细
     * @param int $bill_id
     * @param array $goods_ids
     */
    public function scanAddGoods($bill_id, $goods_ids)
    {
        $bill = WarehouseBill::find()->where(['id'=>$bill_id,'bill_type'=>BillTypeEnum::BILL_TYPE_C])->one();
        if(empty($bill) || $bill->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("单据不是保存状态");
        }
        foreach ($goods_ids as $goods_id) {
            $wareGoods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(empty($wareGoods)) {
                throw new \Exception("[{$goods_id}]条码货号不存在");
            }
            $this->createBillGoodsByGoods($bill, $wareGoods);            
        }
        //更新收货单汇总：总金额和总数量
        $this->billCSummary($bill->id);
        
        return $bill;
    }
    
    /**
     * 快捷创建出库单（先挑选商品）
     * @param WarehouseBillCForm $form
     */
    public function quickCreate($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill = new WarehouseBill();
        $bill->attributes = $form->toArray();
        //$bill->bill_no = SnHelper::createBillSn($form->bill_type);
        $bill->bill_no = \Yii::$app->warehouseService->bill->createBillSn($this->billFix);
        if(false === $bill->save()) {
            throw new \Exception($this->getError($bill));
        }
        //商品主键id数组
        $ids = $form->getGoodsIds();
        foreach ($ids as $id) {
            $wareGoods = WarehouseGoods::find()->where(['id'=>$id])->one();
            if(empty($wareGoods)) {
                throw new \Exception("[{$id}]商品查询失败");
            }
            $this->createBillGoodsByGoods($bill, $wareGoods);            
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
     *
     * 其它出库单审核
     * @param WarehouseBillCForm $form
     * @throws
     */
    public function audit($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if ($form->bill_status != BillStatusEnum::PENDING) {
            throw new \Exception("单据不是待审核状态");
        }
        
        $form->audit_time = time();
        $form->auditor_id = \Yii::$app->user->identity->getId();
        
        if ($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = BillStatusEnum::CONFIRM;
            //更新库存状态
            $billGoodsList = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id','goods_num'])->all();
            if (empty($billGoodsList)) {
                throw new \Exception("单据明细不能为空");
            }
            foreach ($billGoodsList as $billGoods) {
                $wareGoods = WarehouseGoods::find()->where(['goods_id' => $billGoods->goods_id])->one();
                $wareGoods->chuku_time = time();
                $wareGoods->do_chuku_num = $wareGoods->do_chuku_num - $billGoods->goods_num;                
                if ($wareGoods->stock_num == 0) {
                    $wareGoods->goods_status = GoodsStatusEnum::HAS_SOLD;
                }
                if(false === $wareGoods->save(true,['id','goods_status','chuku_time','do_chuku_num'])) {
                    throw new \Exception("[{$billGoods->goods_id}]商品出库失败");
                }
                //插入商品日志
                $log = [
                    'goods_id' => $wareGoods->id,
                    'goods_status' => $wareGoods->goods_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_msg'  => "其它出库单：{$form->bill_no}，出库数量：{$billGoods->goods_num}件"
                ];
                \Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
            }
        } else {
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 其它出库单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function cancel($form)
    {
        //更新库存状态
        $billGoodsList = WarehouseBillGoods::find()->select(['goods_id', 'goods_num'])->where(['bill_id' => $form->id])->all();
        if ($billGoodsList) {
            foreach ($billGoodsList as $billGoods) {
                $wareGoods = WarehouseGoods::find()->where(['goods_id' => $billGoods->goods_id])->one();
                $wareGoods->goods_status = GoodsStatusEnum::IN_STOCK;
                $wareGoods->chuku_time = null;
                if(false === $wareGoods->save(true,['goods_status','chuku_time','do_chuku_num'])) {
                     throw new \Exception("[{$billGoods->goods_id}]货号还原库存失败");
                }
                \Yii::$app->warehouseService->warehouseGoods->updateStockNumByModel($wareGoods, $billGoods->goods_num, AdjustTypeEnum::RESTORE);
            }
        }
        $form->bill_status = BillStatusEnum::CANCEL;
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        
        //日志
        $log = [
            'bill_id' => $form->id,
            'bill_status' => $form->bill_status,
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
    public function delete($form)
    {
        if($form->bill_status != BillStatusEnum::CANCEL) {
            throw new \Exception("需要先取消单据才能删除");
        }
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
     * 更改明细出库数量
     * @param int $id 单据明细ID
     * @param int $goods_num 退货数量
     * @throws \Exception
     * @return boolean
     */
    public function updateChukuNum($id, $chuku_num)
    {
        if($chuku_num <= 0) {
            throw new \Exception("出库数量必须大于0");
        }
        
        $model = WarehouseBillGoods::find()->select(['id','bill_id','goods_id','goods_num'])->where(['id'=>$id])->one();
        if(empty($model)) {
            throw new \Exception("不可更改,明细查询失败");
        }       
        $modify_num = $chuku_num - $model->goods_num;
        if($modify_num != 0) {
            \Yii::$app->warehouseService->warehouseGoods->updateStockNum($model->goods_id, $modify_num, AdjustTypeEnum::MINUS, true);
        }
        $model->goods_num = $chuku_num;
        if(false === $model->save(true,['id','goods_num'])) {
            throw new \Exception($this->getError($model));
        }
        //汇总统计
        $this->billCSummary($model->bill_id);
        return true;
    }
    /**
     * 其它出库单导入
     * @param unknown $form
     * @throws \Exception
     */
    public function import($form)
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
             $salesman_id = 0;
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
            //$bill->bill_no = SnHelper::createBillSn($form->bill_type);
            $bill->bill_no = \Yii::$app->warehouseService->bill->createBillSn($this->billFix);
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
                $res = WarehouseGoods::updateAll(['chuku_price'=>$billGoods->chuku_price,'chuku_time'=>time()],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_STOCK]);
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
            ->select(['sum(goods_num) as goods_num', 'sum(chuku_price) as total_cost', 'sum(sale_price) as total_sale', 'sum(market_price) as total_market'])
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
        $chuku_num = 0;
        if($goods->stock_num == 1) {
            $chuku_num = 1;
        }
        $billGoods = new WarehouseBillGoods();
        $billGoods->attributes = [
                'bill_id' =>$bill->id,
                'bill_no' =>$bill->bill_no,
                'bill_type'=>$bill->bill_type,
                'goods_id'=>$goods_id,
                'goods_name'=>$goods->goods_name,
                'style_sn'=>$goods->style_sn,
                'goods_num'=>$chuku_num,
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
        //扣减库存
        if($chuku_num >= 1) {
            \Yii::$app->warehouseService->warehouseGoods->updateStockNum($goods_id, $chuku_num, AdjustTypeEnum::MINUS, true);
        }
        WarehouseGoods::updateAll(['chuku_price'=>$billGoods->chuku_price,'chuku_time'=>time()],['goods_id'=>$goods_id]);
    }
}