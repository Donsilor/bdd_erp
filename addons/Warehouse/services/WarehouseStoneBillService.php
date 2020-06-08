<?php

namespace addons\Warehouse\services;

use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\helpers\Url;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

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
                    $tabList = [
                        1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ms/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ms-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                        3=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                    ];
                    break;
                }
            case StoneBillTypeEnum::STONE_SS:
                {
                    $tabList = [
                        1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ss/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ss-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                        3=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                    ];
                    break;
                }
            case StoneBillTypeEnum::STONE_TS:
                {
                    $tabList = [
                        1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ts/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ts-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                        3=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                    ];
                    break;
                }
        }
        return $tabList;
    }

    /**
     * 创建买石单
     * @param array $bill
     * @param array $details
     */
    public function createBillMs($bill, $details){
        $billM = new WarehouseStoneBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseStoneBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $bill_id;
            $good['bill_type'] = $billM->bill_type;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $details = ArrayHelper::toArray($details);
        $value = [];
        $key = array_keys($details[0]);
        foreach ($details as $detail) {
            $value[] = array_values($detail);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建买石单明细失败");
        }
    }

    /**
     * 买石单-审核
     * @param $form
     */
    public function auditBillMs($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;

            $billGoods = WarehouseStoneBillGoods::find()->select(['stone_name', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
            if(empty($billGoods)){
                throw new \Exception("单据明细不能为空");
            }
            //石包入库
            \Yii::$app->warehouseService->stone->editStone($form);
            if($form->audit_status == AuditStatusEnum::PASS){
                //同步石料采购收货单货品状态
                $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
                $res = PurchaseStoneReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步石料采购收货单货品状态失败");
                }
            }
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}