<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_produce_stone_goods".
 *
 * @property int $id 配石id
 * @property string $stone_sn 石包号
 * @property int $stone_num 配石数量
 * @property string $stone_weight 石料总重
 */
class ProduceStoneGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('supply_produce_stone_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stone_sn'], 'required'],
            [['id', 'stone_num'], 'integer'],
            [['stone_weight'], 'number'],
            [['stone_sn'], 'string', 'max' => 30],
            [['id', 'stone_sn'], 'unique', 'targetAttribute' => ['id', 'stone_sn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '配石id'),
            'stone_sn' => Yii::t('app', '石包号'),
            'stone_num' => Yii::t('app', '配石数量'),
            'stone_weight' => Yii::t('app', '石料总重'),
        ];
    }
}
