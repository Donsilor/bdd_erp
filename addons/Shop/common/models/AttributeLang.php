<?php

namespace addons\Shop\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_attribute_lang}}".
 *
 * @property int $id 主键
 * @property string $language 语言类型(zh-CN,zh-HK,en-US)
 * @property int $attr_id 属性ID
 * @property string $attr_name 属性名称
 * @property string $default_value 默认值
 * @property string $remark 备注描述
 */
class AttributeLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('goods_attribute_lang');
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
            [['master_id'], 'integer'],
            [['attr_name'], 'required'],
            [['language'], 'string', 'max' => 5],
            [['attr_name'], 'string', 'max' => 100],
            [['default_value'], 'string', 'max' => 20],
            [['attr_values','remark'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'language' => '语言类别',
            'master_id' => 'Attr ID',
            'attr_name' => '属性名称',
            'attr_values' => '属性值',
            'default_value' => '默认值',
            'remark' => '属性备注',
        ];
    }
}
