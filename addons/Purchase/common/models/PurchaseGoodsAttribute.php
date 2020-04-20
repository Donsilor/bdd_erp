<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_goods_attribute".
 *
 * @property int $goods_id 采购商品ID
 * @property int $attr_id 属性id
 * @property int $attr_value_id 属性值ID
 * @property string $attr_value 属性值
 */
class PurchaseGoodsAttribute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_goods_attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'attr_id'], 'required'],
            [['goods_id', 'attr_id', 'attr_value_id'], 'integer'],
            [['attr_value'], 'string', 'max' => 255],
            [['goods_id', 'attr_id'], 'unique', 'targetAttribute' => ['goods_id', 'attr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '采购商品ID',
            'attr_id' => '属性id',
            'attr_value_id' => '属性值ID',
            'attr_value' => '属性值',
        ];
    }
}
