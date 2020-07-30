<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_parts_bill_goods_w".
 *
 * @property int $id
 * @property int $actual_num 实盘件数
 * @property double $actual_weight 实盘重量
 * @property int $fin_status 财务审核状态
 * @property string $fin_checker 财务确认人
 * @property int $fin_adjust_status 财务调整状态
 * @property int $fin_check_time 财务确认时间
 * @property string $fin_remark 财务确认备注
 * @property int $adjust_status 调整状态
 * @property int $adjust_reason 调整原因
 * @property int $status 盘点状态 1已盘点 0未盘点
 * @property int $updated_at 更新时间
 */
class WarehousePartsBillGoodsW extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_parts_bill_goods_w');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'actual_num', 'fin_status', 'fin_adjust_status', 'fin_check_time', 'adjust_status', 'adjust_reason', 'status', 'updated_at'], 'integer'],
            [['actual_weight'], 'number'],
            [['fin_checker'], 'string', 'max' => 30],
            [['fin_remark'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'actual_num' => '实盘件数',
            'actual_weight' => '实盘重量',
            'fin_status' => '财务审核状态',
            'fin_checker' => '财务确认人',
            'fin_adjust_status' => '财务调整状态',
            'fin_check_time' => '财务确认时间',
            'fin_remark' => '财务确认备注',
            'adjust_status' => '调整状态',
            'adjust_reason' => '调整原因',
            'status' => '盘点状态',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * 财务确认人
     * @return \yii\db\ActiveQuery
     */
    public function getFiner()
    {
        return $this->hasOne(Member::class, ['id'=>'fin_checker'])->alias('finer');
    }
}
