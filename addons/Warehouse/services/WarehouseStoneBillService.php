<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

/**
 * 石包单据
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillService extends Service
{

    /**
     * 石包单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id, $bill_type, $returnUrl = null, $tag = null)
    {
        $tabList = [];
        switch ($bill_type){

            case StoneBillTypeEnum::STONE_MS:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ms/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ms-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ms/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-ms-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case StoneBillTypeEnum::STONE_SS:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ss/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ss-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ss/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-ss-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case StoneBillTypeEnum::STONE_TS:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ts/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ts-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ts/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-ts-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case StoneBillTypeEnum::STONE_W:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-w-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-w-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
        }
        return $tabList;
    }
    /**
     * 单据汇总
     * @param integer $bill_id
     * @throws \Exception
     */
    public function stoneBillSummary($bill_id)
    {
        $result = [];
        $sum = WarehouseStoneBillGoods::find()
            ->select(['sum(1) as total_num','sum(stone_weight) as total_weight','sum(cost_price) as total_cost'])
            ->where(['bill_id'=>$bill_id, 'status'=>StatusEnum::ENABLED])
            ->asArray()->one();
        if($sum) {
            $result = WarehouseStoneBill::updateAll(['total_num'=>$sum['total_num']/1,'total_weight'=>$sum['total_weight']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$bill_id]);
        }
        return $result?:null;
    }
    /**
     * 添加单据明细
     * @param $form
     */
    public function createBillGoods($form)
    {
        $stone = WarehouseStone::findOne(['stone_name'=>$form->stone_name]);
        $bill = WarehouseStoneBill::findOne(['id'=>$form->bill_id]);
        $goods = [
            'bill_id' => $form->bill_id,
            'bill_type' => $bill->bill_type,
            'stone_name' => $stone->stone_name,
            'stone_type' => $stone->stone_type,
            'stone_num' => $form->stone_num,
            'stone_weight' => $form->stone_weight,
            'color' => $stone->stone_color,
            'clarity' => $stone->stone_clarity,
            'cost_price' => $stone->cost_price,
            'sale_price' => $stone->sale_price,
            'status' => StatusEnum::ENABLED,
            'created_at' => time()
        ];
        $billGoods = new WarehouseStoneBillGoods();
        $billGoods->attributes = $goods;
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}