<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_templet_log".
 *
 * @property int $id ID
 * @property int $templet_id 配件ID
 * @property string $order_sn 订单号
 * @property string $bill_no 单据编号
 * @property int $adjust_type 调整类型 0扣减 1增加
 * @property int $parts_num 数量
 * @property int $stock_num 库存数量
 * @property string $remark 备注
 * @property int $status 状态
 * @property int $creator_id 操作人
 * @property string $creator
 * @property int $created_at 操作时间
 * @property int $updated_at 更新时间
 */
class WarehouseTempletLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_templet_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['templet_id'], 'required'],
            [['templet_id', 'adjust_type', 'parts_num', 'stock_num', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['order_sn', 'bill_no', 'creator'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'templet_id' => '配件ID',
            'order_sn' => '订单号',
            'bill_no' => '单据编号',
            'adjust_type' => '调整类型',
            'parts_num' => '数量',
            'stock_num' => '库存数量',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '操作人',
            'creator' => 'Creator',
            'created_at' => '操作时间',
            'updated_at' => '更新时间',
        ];
    }
}
