<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_parts_bill_w".
 *
 * @property int $id 单据ID
 * @property string $parts_type 配件类型
 * @property int $save_num 待盘点
 * @property double $save_weight 待盘重量
 * @property int $save_grain 待盘件数
 * @property int $should_num 应盘数量
 * @property double $should_weight 应盘重量
 * @property int $should_grain 应盘件数
 * @property int $actual_num 实盘数量
 * @property double $actual_weight 实盘重量
 * @property int $actual_grain 实盘件数
 * @property int $profit_num 盘盈数量
 * @property double $profit_weight 盘盈重量
 * @property int $profit_grain 盘盈件数
 * @property int $loss_num 盘亏数量
 * @property double $loss_weight 盘亏重量
 * @property int $loss_grain 盘亏件数
 * @property int $normal_num 正常数量
 * @property double $normal_weight 正常重量
 * @property int $normal_grain 正常件数
 * @property int $adjust_num 调整数量
 * @property double $adjust_weight 调整重量
 * @property int $adjust_grain 调整件数
 * @property int $status 盘点状态 0未盘点 1盘点
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehousePartsBillW extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_parts_bill_w');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parts_type'], 'required'],
            [['id', 'save_num', 'save_grain', 'should_num', 'should_grain', 'actual_num', 'actual_grain', 'profit_num', 'profit_grain', 'loss_num', 'loss_grain', 'normal_num', 'normal_grain', 'adjust_num', 'adjust_grain', 'status', 'created_at', 'updated_at'], 'integer'],
            [['save_weight', 'should_weight', 'actual_weight', 'profit_weight', 'loss_weight', 'normal_weight', 'adjust_weight'], 'number'],
            [['parts_type'], 'string', 'max' => 10],
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
            'parts_type' => '配件类型',
            'save_num' => '待盘点',
            'save_weight' => '待盘重量',
            'save_grain' => '待盘件数',
            'should_num' => '应盘数量',
            'should_weight' => '应盘重量',
            'should_grain' => '应盘件数',
            'actual_num' => '实盘数量',
            'actual_weight' => '实盘重量',
            'actual_grain' => '实盘件数',
            'profit_num' => '盘盈数量',
            'profit_weight' => '盘盈重量',
            'profit_grain' => '盘盈件数',
            'loss_num' => '盘亏数量',
            'loss_weight' => '盘亏重量',
            'loss_grain' => '盘亏件数',
            'normal_num' => '正常数量',
            'normal_weight' => '正常重量',
            'normal_grain' => '正常件数',
            'adjust_num' => '调整数量',
            'adjust_weight' => '调整重量',
            'adjust_grain' => '调整件数',
            'status' => '盘点状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
