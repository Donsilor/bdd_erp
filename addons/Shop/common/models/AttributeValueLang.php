<?php

namespace addons\Shop\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_attribute_value_lang}}".
 *
 * @property int $id 主键
 * @property int $master_id
 * @property string $language
 * @property string $attr_value_name 属性值名称
 * @property string $remark 属性值描述
 */
class AttributeValueLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('goods_attribute_value_lang');
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
            [['attr_value_name'], 'required'],
            [['master_id'], 'integer'],
            [['language'], 'string', 'max' => 5],
            [['attr_value_name'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'master_id' => 'Master ID',
            'language' => 'Language',
            'attr_value_name' => '属性值',
            'remark' => '描述',
        ];
    }
}
