<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_stone".
 *
 * @property int $id
 * @property string $purchase_sn 采购单号
 * @property int $supplier_id 供应商ID
 * @property string $total_cost 总成本
 * @property int $total_num 总数量
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态：0待审核 1通过 2不通过
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $delivery_time 交货时间
 * @property int $purchase_status 采购单状态：1保存 2待审核 3已审核 9已取消
 * @property int $status 状态：1保存 2待审核 3已审核 4驳回 9已取消
 * @property string $remark 采购备注
 * @property int $follower_id 跟单人ID
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseStone extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_stone');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'total_num', 'auditor_id', 'audit_status', 'audit_time', 'delivery_time', 'purchase_status', 'status', 'follower_id', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['total_cost'], 'number'],
            [['purchase_sn'], 'string', 'max' => 30],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_sn' => '采购单号',
            'supplier_id' => '供应商ID',
            'total_cost' => '总成本',
            'total_num' => '总数量',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态：0待审核 1通过 2不通过',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'delivery_time' => '交货时间',
            'purchase_status' => '采购单状态：1保存 2待审核 3已审核 9已取消',
            'status' => '状态：1保存 2待审核 3已审核 4驳回 9已取消',
            'remark' => '采购备注',
            'follower_id' => '跟单人ID',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
