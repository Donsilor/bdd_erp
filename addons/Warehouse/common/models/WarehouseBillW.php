<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_w".
 *
 * @property int $id 单据ID
 * @property int $should_num 应盘数量
 * @property int $actual_num 实盘数量
 * @property int $profit_num 盘盈数量
 * @property int $loss_num 盘亏数量
 * @property int $wrong_num 异常数量
 * @property int $normal_num 正常数量
 */
class WarehouseBillW extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_w');
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
            [['id'], 'required'],
            [['id', 'should_num', 'actual_num', 'profit_num', 'loss_num', 'wrong_num', 'normal_num'], 'integer'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '单据ID',
            'should_num' => '应盘数量',
            'actual_num' => '实盘数量',
            'profit_num' => '盘盈数量',
            'loss_num' => '盘亏数量',
            'wrong_num' => '异常数量',
            'normal_num' => '正常数量',
        ];
    }
}
