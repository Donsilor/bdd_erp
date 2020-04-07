<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "style_style_attribute".
 *
 * @property int $style_id 款式id
 * @property int $attr_id 属性id
 * @property int $input_type 属性显示方式
 * @property int $attr_type 属性类型
 * @property string $attr_values 属性值
 */
class StyleAttribute extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_attribute");
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id', 'attr_id'], 'required'],
            [['style_id', 'attr_id', 'input_type', 'attr_type'], 'integer'],
            [['attr_values'], 'string', 'max' => 2000],
            [['style_id', 'attr_id'], 'unique', 'targetAttribute' => ['style_id', 'attr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'style_id' => '款式ID',
            'attr_id' => '属性ID',
            'input_type' => '显示方式',
            'attr_type' => '属性类型',
            'attr_values' => '属性值',
        ];
    }
}
