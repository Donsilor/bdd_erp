<?php

namespace addons\Purchase\common\forms;

use addons\Style\common\enums\AttrIdEnum;
use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 */
class PurchaseStoneReceiptGoodsForm extends PurchaseStoneReceiptGoods
{
    public $ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules() , $rules);
    }

    /**
     * 石料类型列表
     * @return array
     */
    public static function getStoneTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE);
    }
    /**
     * 石料颜色列表
     * @return array
     */
    public static function getColorMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR);
    }
    /**
     * 石料净度列表
     * @return array
     */
    public static function getClarityMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'id'=>'流水号',
        ]);
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

    /**
     * {@inheritdoc}
     */
    public function checkDistinct($col, $ids){
        $query = PurchaseStoneReceiptGoods::find();
        $query->from(['rg'=> PurchaseStoneReceiptGoods::tableName()]);
        $query->leftJoin(['r' => PurchaseReceipt::tableName()], 'r.id = rg.receipt_id');
        $query->where(['rg.id' => $ids]);
        $query->distinct($col);
        $res = $query->count(1);
        return $res==1?:0;
    }
}
