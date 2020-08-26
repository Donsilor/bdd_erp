<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_return_goods".
 *
 * @property int $id ID
 * @property int $return_id 退款ID
 * @property string $return_no 退款编号
 * @property int $order_detail_id 订单明细ID
 * @property string $goods_id 条码号(货号)
 * @property string $goods_name 商品名称
 * @property int $goods_num 数量
 * @property string $should_amount 应退金额
 * @property string $apply_amount 申请退款金额
 * @property string $real_amount 实际退款金额
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class SalesReturnGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('return_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['return_id', 'return_no'], 'required'],
            [['return_id', 'order_detail_id', 'goods_num', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['should_amount', 'apply_amount', 'real_amount'], 'number'],
            [['return_no', 'goods_id'], 'string', 'max' => 30],
            [['goods_name', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'return_id' => '退款ID',
            'return_no' => '退款编号',
            'order_detail_id' => '订单明细ID',
            'goods_id' => '条码号(货号)',
            'goods_name' => '商品名称',
            'goods_num' => '数量',
            'should_amount' => '应退金额',
            'apply_amount' => '申请退款金额',
            'real_amount' => '实际退款金额',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
