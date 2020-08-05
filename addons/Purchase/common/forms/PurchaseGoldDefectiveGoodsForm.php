<?php

namespace addons\Purchase\common\forms;

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use Yii;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
/**
 * 不良返厂单明细 Form
 *
 */
class PurchaseGoldDefectiveGoodsForm extends PurchaseDefectiveGoods
{
    public $xuhaos;
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'material_type'=>'金料材质',
        ]);
    }
    /**
     * 批量获取序号
     */
    public function getXuhaos()
    {
        return StringHelper::explodeIds($this->xuhaos);
    }
    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE);
    }
}
