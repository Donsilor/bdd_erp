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
                
        $bill = new WarehouseBill();
        $bill->attributes = $form->toArray();  
        
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }  
        //批量创建单据明细
        $page_size = 100;
        for($page = 1; $page <= 200 ; $page ++) {

            $goods_list = WarehouseGoods::find()->select(['goods_id','style_sn','goods_name','warehouse_id'])->where(['warehouse_id'=>$bill->from_warehouse_id,'goods_status'=>GoodsStatusEnum::IN_STOCK])->limit($page_size)->asArray()->all();
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
                            'to_warehouse_id'=>$goods['warehouse_id'],
                            'status'=>PandianStatusEnum::SAVE,
                    ];
                    $bill_goods_values[] = array_values($bill_goods);
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
        //更新应盘数量和总金额
        $this->warehouseBillSummary($bill->id);
        
        
    }
    /**
     * 添加盘点明细
     * @param WarehouseBillWForm $form
     */
    public function createBillGoodsW($form)
    {
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
                $billGoods->to_warehouse_id = $goods->to_warehouse_id;//盘点仓库
                $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
            }else {
                if($billGoods->from_warehouse_id == $goods->warehouse_id) {
                    $billGoods->status = PandianStatusEnum::NORMAL;//正常
                }else if($form->from_warehouse_id != $goods->warehouse_id){
                    $billGoods->status = PandianStatusEnum::LOSS;//盘亏
                }
            }
            $billGoods->goods_name = $goods->goods_name;            
            $billGoods->from_warehouse_id = $goods->warehouse_id;//归属仓库
            
            
            if($billGoods->from_warehouse_id == $goods->warehouse_id) {
                $billGoods->status = PandianStatusEnum::NORMAL;
            }else if($form->from_warehouse_id != $goods->warehouse_id){
                $billGoods->status = PandianStatusEnum::LOSS;
            }else {
                $billGoods['status'] = PandianStatusEnum::PROFIT;
            }
            
        }
        
    }
    /**
     * 实际盘点总数
     * @param unknown $bill_id
     * @return number|string
     */
    public function getPandianCount($bill_id)
    {
        return WarehouseBillGoods::find()->where(['bill_id'=>$bill_id])->andWhere(['>','pandian_status',PandianStatusEnum::SAVE])->count();
    }
}