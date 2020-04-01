<?php

namespace addons\style\common\models;

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
        return self::dbName().'.attribute_lang';
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
            [['master_id'], 'integer'],
            [['attr_name'], 'required'],
            [['language'], 'string', 'max' => 5],
            [['attr_name'], 'string', 'max' => 100],
            [['default_value'], 'string', 'max' => 20],
            [['attr_values','remark'], 'string', 'max' => 500],
            /* [['attr_name'],'unique', 'targetAttribute'=>['attr_name','language','remark'],
                 //'targetClass' => 'models\AttributeLang', // 模型，缺省时默认当前模型。
                 'comboNotUnique' => '属性名称重复' //错误信息
            ] */
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods_attribute', 'ID'),
            'language' => Yii::t('goods_attribute', '语言类别'),
            'master_id' => Yii::t('goods_attribute', 'Attr ID'),
            'attr_name' => Yii::t('goods_attribute', '属性名称'),
            'attr_values' => Yii::t('goods_attribute', '属性值'),
            'default_value' => Yii::t('goods_attribute', '默认值'),
            'remark' => Yii::t('goods_attribute', '属性备注'),
        ];
    }
}
