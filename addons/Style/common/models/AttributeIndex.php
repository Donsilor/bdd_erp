<?php

namespace addons\Style\common\models;

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
class AttributeIndex extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("attribute_index");
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
            [['attr_type', 'attr_value_id', 'style_id', 'type_id', 'attr_id'], 'required'],
            [['attr_type', 'attr_value_id', 'style_id', 'type_id', 'attr_id'], 'integer'],
            [['attr_value'], 'string'],
            //[['attr_type', 'attr_value_id'], 'unique', 'targetAttribute' => ['style_id', 'attr_value_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'style_id' => Yii::t('goods', '款式ID'), 
            'type_id' => Yii::t('goods', '产品线ID'),
            'attr_id' => Yii::t('goods', '属性ID'),
            'attr_type' => Yii::t('goods', '属性类型'),
            'attr_value_id' => Yii::t('goods', '属性值ID'),
            'attr_value' => Yii::t('goods', '属性值'),
        ];
    }
}
