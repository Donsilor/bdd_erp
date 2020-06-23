<?php

namespace addons\Supply\common\models;

use Yii;
use addons\Warehouse\common\models\WarehouseStone;

/**
 * This is the model class for table "supply_produce_stone_goods".
 *
 * @property int $id 配石id
 * @property string $stone_sn 石包号
 * @property int $stone_num 配石数量
 * @property number $stone_weight 石料总重
 */
class ProduceStoneGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_stone_goods');
    }
    /**
     * @return array
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
            [['id', 'stone_sn','stone_num','stone_weight'], 'required'],
            [['id', 'stone_num'], 'integer','min'=>0],
            [['stone_weight'], 'number','min'=>0],
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
            'stone_weight' => Yii::t('app', '配石总重'),
        ];
    }
    /**
     * 石包现货  一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStone()
    {
        return $this->hasOne(WarehouseStone::class, ['stone_sn'=>'stone_sn'])->alias('stone');
    }
}
