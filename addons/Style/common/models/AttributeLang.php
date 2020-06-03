<?php

namespace addons\Style\common\models;

use Yii;

/**
 * 属性语言表 Model
 *
 * @property int $id 主键
 * @property string $language 语言类型(zh-CN,zh-HK,en-US)
 * @property int $attr_id 属性ID
 * @property string $attr_name 属性名称
 * @property string $remark 备注描述
 */
class AttributeLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("attribute_lang");
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
            [['attr_name','attr_label'], 'string', 'max' => 50],
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
            'id' => 'ID',
            'language' => '语言类别',
            'master_id' => "属性",
            'attr_name' => '属性名称',
            'attr_label'=> '显示名称',
            'attr_values' => '属性值',
            'remark' => '属性备注',
        ];
    }
}
