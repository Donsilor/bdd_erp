<?php

namespace addons\Warehouse\services;


use Yii;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\PandianStatusEnum;
use common\enums\StatusEnum;
use common\helpers\Url;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;

/**
 * 盘点单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillWService extends WarehouseBillService
{
    
    /**
     * 创建盘点单
     * @param WarehouseBillWForm $form
     * @throws \Exception
     */
    public function createBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //锁定仓库
        \Yii::$app->warehouseService->warehouse->lockWarehouse($form->to_warehouse_id);

        $bill = new WarehouseBill();
        $bill->attributes = $form->toArray(); 
        $bill->bill_status = BillStatusEnum::SAVE;  
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        } 
        
        //批量创建单据明细
        $page_size = 100;
        $should_num = 0;
        for($page = 1; $page <= 200 ; $page ++) {

            $goods_list = WarehouseGoods::find()->where(['warehouse_id'=>$bill->to_warehouse_id,'goods_status'=>GoodsStatusEnum::IN_STOCK])->limit($page_size)->asArray()->all();
            if(!empty($goods_list)) {
                foreach ($goods_list as $goods) {
                    $goods_ids[] = $goods['goods_id'];
                    $bill_goods = [
                            'bill_id'=>$bill->id,
                            'bill_type'=>$bill->bill_type,
                            'bill_no'=>$bill->bill_no,
                            'goods_id'=>$goods['goods_id'],
                            'style_sn'=>$goods['style_sn'],
                            'goods_name'=>$goods['goods_name'],
                            'goods_num'=>1,
                            'cost_price'=>$goods['cost_price'],
                            'market_price'=>$goods['market_price'],
                            'to_warehouse_id'=>$goods['warehouse_id'],
                            'status'=> PandianStatusEnum::SAVE,
                    ];
                    $bill_goods_values[] = array_values($bill_goods);
                    $should_num ++;
                }
                if(empty($bill_goods_keys)) {
                    $bill_goods_keys = array_keys($bill_goods);
                }
                //更新仓库所有货品 盘点中
                WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_PANDIAN],['goods_id'=>$goods_ids,'goods_status'=>GoodsStatusEnum::IN_STOCK]);
                //导入明细
                $result = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute(); 
                if(!$result) {
                    throw new \Exception('导入单据明细失败');
                }
                
            }            
            if(count($goods_list) < $page_size) {
                break;
            } 
        }
        
        //盘点单附属表
        $billW = new WarehouseBillW();
        $billW->bill_id = $bill->id;
        $billW->should_num = $should_num;
        if(false === $billW->save()){
            throw new \Exception($this->getError($billW));
        }
        
        //更新应盘数量和总金额   
        $this->warehouseBillSummary($bill->id);
    }
    
    /**
     * 盘点操作
     * @param WarehouseBillWForm $form
     */
    public function createBillGoodsW($form)
    {   
        //校验单据状态
        if ($form->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("单据已盘点结束");
        }
        
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        
        foreach ($form->getGoodsIds() as $goods_id) {
            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(empty($goods)) {
                throw new \Exception("[{$goods_id}]货号不存在");
            }
            $billGoods = WarehouseBillGoods::find()->where(['bill_id'=>$form->id,'goods_id'=>$goods_id])->one();
            if(!$billGoods) {
                $billGoods = new WarehouseBillGoods();
                $billGoods->bill_id = $form->id;
                $billGoods->bill_type = $form->bill_type;
                $billGoods->bill_no = $form->bill_no;
                $billGoods->goods_id = $form->goods_id;
                $billGoods->to_warehouse_id = $form->to_warehouse_id;//盘点仓库
                $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
            }else {
                if($billGoods->to_warehouse_id == $goods->warehouse_id) {
                    if($goods->goods_status != GoodsStatusEnum::IN_PANDIAN) {
                        $billGoods->status = PandianStatusEnum::WRONG;//异常
                    }else {
                        $billGoods->status = PandianStatusEnum::NORMAL;//正常
                    }                    
                }elseif($billGoods->to_warehouse_id != $goods->warehouse_id){
                    $billGoods->status = PandianStatusEnum::LOSS;//盘亏
                }
            }
            $billGoods->goods_name = $goods->goods_name;            
            $billGoods->from_warehouse_id = $goods->warehouse_id;//归属仓库
            //更多商品属性
            //............
            
            if(false === $billGoods->save()) {
                throw new \Exception($this->getError($billGoods));
            }
            
        }
        
        $this->warehouseBillSummary($form->id);
        
    }
    
    /**
     * 盘点审核
     * @param WarehouseBillWForm $form
     */
    public function auditBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $subQuery = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id'=>$form->id]);
        if($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = BillStatusEnum::CONFIRM;            
            WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$subQuery,'goods_status'=>GoodsStatusEnum::IN_PANDIAN]);
        }else {
            $form->bill_status = BillStatusEnum::CANCEL;
            WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$subQuery,'goods_status'=>GoodsStatusEnum::IN_PANDIAN]);
        }
        if(false === $form->save() ){
            throw new \Exception($this->getError($form));
        }
    }
    /**
     * 仓储单据汇总
     * @param unknown $bill_id
     */
    public function warehouseBillSummary($bill_id)
    {
        $sum = WarehouseBillGoods::find()
            ->select(['sum(if(status>'.PandianStatusEnum::SAVE.',1,0)) as actual_num',
                    'sum(if(status='.PandianStatusEnum::PROFIT.',1,0)) as profit_num',
                    'sum(if(status='.PandianStatusEnum::LOSS.',1,0)) as loss_num',
                    'sum(if(status='.PandianStatusEnum::NORMAL.',1,0)) as normal_num',
                    'sum(if(status='.PandianStatusEnum::WRONG.',1,0)) as wrong_num',
                    'sum(cost_price) as total_cost',
                    'sum(sale_price) as total_sale',
                    'sum(market_price) as total_market'
            ])->where(['bill_id'=>$bill_id])->asArray()->one();
        
        if($sum) {
            
            $billUpdate = ['goods_num'=>$sum['actual_num']/1, 'total_cost'=>$sum['total_cost']/1, 'total_sale'=>$sum['total_sale']/1, 'total_market'=>$sum['total_market']/1];
            $billWUpdate = ['actual_num'=>$sum['actual_num']/1, 'loss_num'=>$sum['loss_num']/1, 'normal_num'=>$sum['normal_num']/1, 'wrong_num'=>$sum['wrong_num']/1];
            if($sum['actual_num'] > 0 && $sum['actual_num'] == $sum['normal_num']) {
                //盘点结束(待审核)
                $billUpdate['bill_status'] = BillStatusEnum::PENDING;
            } 
            $res1 = WarehouseBill::updateAll($billUpdate,['id'=>$bill_id]);
            $res2 = WarehouseBillW::updateAll($billWUpdate,['bill_id'=>$bill_id]);
            return $res1 && $res2;
        }
        return false;
    }
}