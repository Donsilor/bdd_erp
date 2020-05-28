<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_w".
 *
 * @property int $id 单据ID
 * @property int $save_num 待盘数量
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
            [['id', 'save_num','should_num', 'actual_num', 'profit_num', 'loss_num', 'adjust_num', 'normal_num'], 'integer'],
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
            'save_num' => '待盘数量',
            'should_num' => '应盘数量',
            'actual_num' => '实盘数量',
            'profit_num' => '盘盈数量',
            'loss_num' => '盘亏数量',
            'adjust_num' => '调整数量',
            'normal_num' => '正常数量',
        ];
    }
}
