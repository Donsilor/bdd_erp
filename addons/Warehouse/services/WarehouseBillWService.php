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

/**
 * 盘点单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillWService extends WarehouseBillService
{
    /**
     * 仓储单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id, $returnUrl = null)
    {
        return [
                1=>['name'=>'盘点详情','url'=>Url::to(['warehouse-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'盘点商品','url'=>Url::to(['warehouse-bill-w-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'盘点日志','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
        ];
    }
    
    /**
     * 创建盘点单
     * @param unknown $form
     * @throws \Exception
     */
    public function createBill($form)
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
                            'from_warehouse_id'=>$goods['warehouse_id'],
                            'pandian_status'=>PandianStatusEnum::SAVE,
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
     * 实际盘点总数
     * @param unknown $bill_id
     * @return number|string
     */
    public function getPandianCount($bill_id)
    {
        return WarehouseBillGoods::find()->where(['bill_id'=>$bill_id])->andWhere(['>','pandian_status',PandianStatusEnum::SAVE])->count();
    }
}