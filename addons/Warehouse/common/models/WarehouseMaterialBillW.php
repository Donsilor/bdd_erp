<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_material_bill_w".
 *
 * @property int $id 单据ID
 * @property int $save_num 待盘点
 * @property double $save_weight 待盘点重量
 * @property int $should_num 应盘数量
 * @property double $should_weight 应盘重量
 * @property int $actual_num 实盘数量
 * @property double $actual_weight 实盘重量
 * @property int $profit_num 盘盈数量
 * @property double $profit_weight 盘盈重量
 * @property int $loss_num 盘亏数量
 * @property double $loss_weight 盘亏重量
 * @property int $normal_num 正常数量
 * @property double $normal_weight 正常重量
 * @property int $adjust_num 调整数量
 * @property double $adjust_weight 调整重量
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseMaterialBillW extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_material_bill_w');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'save_num', 'should_num', 'actual_num', 'profit_num', 'loss_num', 'normal_num', 'adjust_num', 'status', 'created_at', 'updated_at'], 'integer'],
            [['save_weight', 'should_weight', 'actual_weight', 'profit_weight', 'loss_weight', 'normal_weight', 'adjust_weight'], 'number'],
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
            'save_num' => '待盘点',
            'save_weight' => '待盘点重量',
            'should_num' => '应盘数量',
            'should_weight' => '应盘重量',
            'actual_num' => '实盘数量',
            'actual_weight' => '实盘重量',
            'profit_num' => '盘盈数量',
            'profit_weight' => '盘盈重量',
            'loss_num' => '盘亏数量',
            'loss_weight' => '盘亏重量',
            'normal_num' => '正常数量',
            'normal_weight' => '正常重量',
            'adjust_num' => '调整数量',
            'adjust_weight' => '调整重量',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
