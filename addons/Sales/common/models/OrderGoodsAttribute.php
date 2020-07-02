<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_order_goods_attribute".
 *
 * @property int $id 订单明细ID
 * @property int $attr_id 属性id
 * @property int $attr_value_id 属性值id
 * @property string $attr_value 属性值
 * @property int $sort 排序
 */
class OrderGoodsAttribute extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sales_order_goods_attribute';
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
                
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'attr_id'], 'required'],
            [['id', 'attr_id', 'attr_value_id', 'sort'], 'integer'],
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
            'id' => '订单明细ID',
            'attr_id' => '属性id',
            'attr_value_id' => '属性值id',
            'attr_value' => '属性值',
            'sort' => '排序',
        ];
    }
}
