<?php

namespace addons\Purchase\common\forms;

use addons\Style\common\enums\AttrIdEnum;
use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 */
class PurchaseGoldReceiptGoodsForm extends PurchaseGoldReceiptGoods
{
    public $ids;
    public $remark;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['remark'], 'string', 'max'=>255],
        ];
        return array_merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'id'=>'流水号',
            'remark'=>'备注',
        ]);
    }

    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }
}
