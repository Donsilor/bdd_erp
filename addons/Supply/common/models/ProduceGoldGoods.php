<?php

namespace addons\Supply\common\models;

use Yii;
use addons\Warehouse\common\models\WarehouseGold;

/**
 * This is the model class for table "supply_produce_gold_goods".
 *
 * @property int $id 配石id
 * @property string $gold_sn 金料编号
 * @property string $gold_weight 领料重量
 */
class ProduceGoldGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_gold_goods');
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
            [['id', 'gold_sn','gold_weight'], 'required'],
            [['id'], 'integer'],
            [['gold_weight'], 'number'],
            [['gold_sn'], 'string', 'max' => 30],
            [['id', 'gold_sn'], 'unique', 'targetAttribute' => ['id', 'gold_sn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '配石id'),
            'gold_sn' => Yii::t('app', '金料编号'),
            'gold_weight' => Yii::t('app', '领料重量'),
        ];
    }
    
    /**
     * 金料现货  一对一
     * @return \yii\db\ActiveQuery
     */
    public function getGold()
    {
        return $this->hasOne(WarehouseGold::class, ['gold_sn'=>'gold_sn'])->alias('gold');
    }
}
