<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * 款式属性关系索引表 Model
 *
 * @property int $attr_type 商品id
 * @property int $cat_id 分类id
 * @property int $attr_value_id 属性值id
 * @property int $style_id 商品公共表id
 * @property int $type_id 类型id
 * @property int $attr_id 属性id
 */
class PurchaseGoodsAttribute extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("purchase_goods_attribute");
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
                [['id','attr_id','attr_value_id'], 'required'],
                [['id','attr_id','attr_value_id'], 'integer'],
                [['attr_value_min', 'attr_value_max'], 'number'],
                [['attr_value'], 'string','max'=>255]
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => '商品ID',
                'attr_id' => "属性ID",
                'attr_value_id' => "属性值ID",
                'attr_value' => "属性值",
                'attr_value_min' => "最小值",
                'attr_value_max' => "最大值",
                'sort' => "排序",
        ];
    }
}
