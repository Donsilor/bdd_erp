<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use addons\Warehouse\common\enums\PartsBillTypeEnum;
use common\enums\StatusEnum;
use common\helpers\Url;

/**
 * 配件单据
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousePartsBillService extends Service
{
    /**
     * 配件单据明细 tab
     * @param int $bill_id 单据ID
     * @param int $bill_type
     * @param $returnUrl URL
     * @param $tag
     * @return array
     */
    public function menuTabList($bill_id, $bill_type, $returnUrl = null, $tag = null)
    {
        $tabList = [];
        switch ($bill_type){

            case PartsBillTypeEnum::PARTS_L:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-l/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['parts-bill-l-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-l/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['parts-bill-l-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PartsBillTypeEnum::PARTS_C:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-c/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['parts-bill-c-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-c/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['parts-bill-c-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PartsBillTypeEnum::PARTS_D:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-d/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['parts-bill-d-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-d/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['parts-bill-d-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PartsBillTypeEnum::PARTS_W:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['parts-bill-w-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['parts-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['parts-bill-w-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['parts-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
        }
        return $tabList;
    }
    /**
     * 单据汇总
     * @param int $bill_id
     * @throws
     * @return
     */
    public function partsBillSummary($bill_id)
    {
        $sum = WarehousePartsBillGoods::find()
            ->select(['sum(1) as total_num','sum(parts_weight) as total_weight','sum(cost_price) as total_cost'])
            ->where(['bill_id'=>$bill_id, 'status'=>StatusEnum::ENABLED])
            ->asArray()->one();
        if($sum) {
            $result = WarehousePartsBill::updateAll(['total_num'=>$sum['total_num']/1,'total_weight'=>$sum['total_weight']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$bill_id]);
        }
        return $result?:null;
    }
    /**
     * 添加单据明细
     * @param $form
     * @throws
     */
    public function createBillGoods($form)
    {
        $parts = WarehouseParts::findOne(['parts_sn'=>$form->parts_sn]);
        $goods = [
            'bill_id' => $form->bill_id,
            'bill_no' => $form->bill_no,
            'bill_type' => $form->bill_type,
            'parts_sn' => $parts->parts_sn,
            'parts_name' => $parts->parts_name,
            'style_sn' => $parts->style_sn,
            'parts_type' => $parts->parts_type,
            'material_type' => $parts->material_type,
            'parts_num' => $form->stone_num,
            'parts_weight' => $form->stone_weight,
            'color' => $parts->color,
            'shape' => $parts->shape,
            'size' => $parts->size,
            'chain_type' => $parts->chain_type,
            'cramp_ring' => $parts->cramp_ring,
            'cost_price' => $parts->cost_price,
            'sale_price' => $parts->sale_price,
            'status' => StatusEnum::ENABLED,
            'created_at' => time()
        ];
        $billGoods = new WarehousePartsBillGoods();
        $billGoods->attributes = $goods;
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}