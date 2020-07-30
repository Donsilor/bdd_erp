<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Warehouse\common\enums\PartsBillStatusEnum;
use common\helpers\ArrayHelper;

/**
 * 配件 Form
 *
 */
class WarehousePartsForm extends WarehouseParts
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
        $billL = WarehousePartsBillLForm::find()->alias('l')
            ->leftJoin(WarehousePartsBillLGoodsForm::tableName().' lg', 'lg.bill_id=l.id')
            ->select(['l.bill_no','l.delivery_no'])
            ->where(['lg.parts_sn'=>$this->parts_sn, 'l.bill_status'=>PartsBillStatusEnum::CONFIRM])
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
