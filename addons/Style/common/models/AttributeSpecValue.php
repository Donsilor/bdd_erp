<?php

namespace addons\style\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_attribute_spec_value}}".
 *
 * @property int $id ID
 * @property int $spec_id 规格ID
 * @property int $attr_id 属性ID
 * @property int $attr_value_id 属性值id
 * @property string $attr_value 属性值
 * @property int $usage 使用类型（1基础属性 2搜索属性）
 * @property int $sort 属性排序(数字越小越前)
 * @property int $status 状态(-1删除,0禁用,1-正常)
 */
class AttributeSpecValue extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.attribute_spec_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['spec_id', 'attr_id'], 'required'],
            [['spec_id', 'attr_id', 'attr_value_id', 'usage', 'sort', 'status','created_at','updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spec_id' => '规格ID',
            'attr_id' => '属性',
            'attr_value_id' => '属性值',
            'usage' => '用途',
            'sort' => '排序',
            'status' => '状态',
            'created_at'=>'创建时间',
            'updated_at'=>'更新时间',
        ];
    }
}
