<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_apply_goods_attribute".
 *
 * @property int $id 商品ID
 * @property int $attr_id 属性id
 * @property int $attr_value_id 属性值id
 * @property string $attr_value 属性值
 * @property double $attr_value_min 最小值(暂时作废)
 * @property double $attr_value_max 最大值（暂时作废）
 * @property int $sort 排序
 */
class PurchaseApplyGoodsAttribute extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_apply_goods_attribute');
    }
    /**
     * behaviors
     * {@inheritDoc}
     * @see \common\models\base\BaseModel::behaviors()
     */
    public function behaviors()
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'attr_id'], 'required'],
            [['id', 'attr_id', 'attr_value_id', 'sort'], 'integer'],
            [['attr_value_min', 'attr_value_max'], 'number'],
            [['attr_value'], 'string', 'max' => 255],
            [['id', 'attr_id'], 'unique', 'targetAttribute' => ['id', 'attr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'attr_id' => Yii::t('app', '属性id'),
            'attr_value_id' => Yii::t('app', '属性值id'),
            'attr_value' => Yii::t('app', '属性值'),
            'attr_value_min' => Yii::t('app', '最小值(暂时作废)'),
            'attr_value_max' => Yii::t('app', '最大值（暂时作废）'),
            'sort' => Yii::t('app', '排序'),
        ];
    }
}
