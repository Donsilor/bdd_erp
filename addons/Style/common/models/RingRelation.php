<?php

namespace addons\style\common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "{{%goods_ring_relation}}".
 *
 * @property int $id 主键ID
 * @property int $style_id 款式ID
 * @property int $ring_id 对戒ID
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class RingRelation extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{%ring_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id', 'ring_id', 'created_at', 'updated_at'], 'integer'],
            ['style_id','unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'style_id' => '款式ID',
            'ring_id' => '对戒ID',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
