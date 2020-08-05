<?php

namespace addons\Warehouse\common\forms;

use addons\Purchase\common\models\PurchaseReceipt;
use addons\Warehouse\common\enums\GoldBillStatusEnum;
use Yii;
use addons\Warehouse\common\models\WarehouseGold;
use common\helpers\ArrayHelper;

/**
 * 金料 Form
 *
 */
class WarehouseGoldForm extends WarehouseGold
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [

         ];
         return ArrayHelper::merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBillInfo(){
        //入库单
        $billL = WarehouseGoldBillLForm::find()->alias('l')
            ->leftJoin(WarehouseGoldBillLGoodsForm::tableName().' lg', 'lg.bill_id=l.id')
            ->select(['l.bill_no','l.delivery_no'])
            ->where(['lg.gold_sn'=>$this->gold_sn, 'l.bill_status'=>GoldBillStatusEnum::CONFIRM])
            ->one();
        $bill_l_no = $billL->bill_no??"";
        $delivery_no = $billL->delivery_no??"";
        $purchase_sn = "";
        if($delivery_no){
            $receipt = PurchaseReceipt::find()->select(['purchase_sn'])->where(['receipt_no' => $delivery_no])->one();
            $purchase_sn = $receipt->purchase_sn??"";
        }
        return [
            'bill_no'=>$bill_l_no,
            'receipt_no'=>$delivery_no,
            'purchase_sn'=>$purchase_sn,
        ];
    }
}
