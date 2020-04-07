<?php

namespace addons\Style\common\models;

use Yii;

/**
 * 属性值语言Model
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
        return self::tableFullName("attribute_value_lang");
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
            'id' => Yii::t('goods_attribute', 'ID'),
            'master_id' => Yii::t('goods_attribute', 'Master ID'),
            'language' => Yii::t('goods_attribute', 'Language'),
            'attr_value_name' => Yii::t('goods_attribute', '属性值'),
            'remark' => Yii::t('goods_attribute', '描述'),
        ];
    }
}
