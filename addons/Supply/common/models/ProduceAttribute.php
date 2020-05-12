<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_produce_attribute".
 *
 * @property int $produce_id 布产ID
 * @property int $attr_id 属性id
 * @property int $attr_value_id 属性值ID
 * @property string $attr_value 属性值
 * @property string $sort 属性值
 */
class ProduceAttribute extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_attribute');
    }
    /**
     * 
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
            [['produce_id', 'attr_id'], 'required'],
            [['produce_id', 'attr_id', 'attr_value_id','sort'], 'integer'],
            [['attr_value'], 'string', 'max' => 255],
            [['produce_id', 'attr_id'], 'unique', 'targetAttribute' => ['produce_id', 'attr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'produce_id' => '布产ID',
            'attr_id' => '属性id',
            'attr_value_id' => '属性值ID',
            'sort' => '排序',
            'attr_value' => '属性值',
        ];
    }


}
