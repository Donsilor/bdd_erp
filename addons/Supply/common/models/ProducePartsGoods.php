<?php

namespace addons\Supply\common\models;

use Yii;
use addons\Warehouse\common\models\WarehouseParts;

/**
 * This is the model class for table "supply_produce_parts_goods".
 *
 * @property int $id 配件id
 * @property string $parts_sn 配件编号
 * @property int $parts_num 领件数量
 * @property string $parts_weight 领件重量
 * @property string $loss_rate 损耗率
 */
class ProducePartsGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_parts_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parts_sn'], 'required'],
            [['id', 'parts_num'], 'integer'],
            [['parts_weight', 'loss_rate'], 'number'],
            [['parts_sn'], 'string', 'max' => 30],
            [['id', 'parts_sn'], 'unique', 'targetAttribute' => ['id', 'parts_sn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '配件id',
            'parts_sn' => '配件编号',
            'parts_num' => '领件数量',
            'parts_weight' => '领件重量',
            'loss_rate' => '损耗率',
        ];
    }
    /**
     * 配料现货  一对一
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasOne(WarehouseParts::class, ['parts_sn'=>'parts_sn'])->alias('parts');
    }
}
