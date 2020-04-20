<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase".
 *
 * @property int $id
 * @property string $title
 * @property string $purchase_sn 采购单号
 * @property string $cost_total 总成本
 * @property int $goods_total 总数量
 * @property int $creator_id 创建人
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property string $audit_remark 审核备注
 * @property string $remark 采购备注
 * @property int $status 状态 1已布产 0待布产  -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Purchase extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_total'], 'number'],
            [['goods_total', 'creator_id', 'auditor_id', 'audit_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'audit_remark', 'remark'], 'string', 'max' => 255],
            [['purchase_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'purchase_sn' => '采购单号',
            'cost_total' => '采购成本',
            'goods_total' => '采购数量',
            'creator_id' => '创建人',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_remark' => '审核备注',
            'remark' => '采购备注',
            'status' => '状态 ',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
