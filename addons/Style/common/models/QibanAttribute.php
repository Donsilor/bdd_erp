<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "style_qiban_attribute".
 *
 * @property int $qiban_id 款式id
 * @property int $attr_id 属性id
 * @property int $input_type 属性显示方式
 * @property int $is_require 是否必填 1必填 0选填
 * @property int $attr_type 属性类型
 * @property string $attr_values 属性值
 * @property int $status 状态 1启用 0禁用 -1删除
 */
class QibanAttribute extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return static::tableFullName("qiban_attribute");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qiban_id', 'attr_id'], 'required'],
            [['qiban_id', 'attr_id', 'input_type', 'is_require', 'attr_type', 'status'], 'integer'],
            [['attr_values'], 'string', 'max' => 2000],
            [['qiban_id', 'attr_id'], 'unique', 'targetAttribute' => ['qiban_id', 'attr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qiban_id' => '款式id',
            'attr_id' => '属性id',
            'input_type' => '属性显示方式',
            'is_require' => '是否必填 1必填 0选填',
            'attr_type' => '属性类型',
            'attr_values' => '属性值',
            'status' => '状态 1启用 0禁用 -1删除',
        ];
    }
}
