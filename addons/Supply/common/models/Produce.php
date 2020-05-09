<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_produce".
 *
 * @property int $id
 * @property int $merchant_id 商户ID
 * @property string $produce_sn 布产单编号
 * @property int $from_type 订单来源(1订单，2采购单)
 * @property int $from_order_id 来源订单id
 * @property string $from_order_sn 来源订单编号
 * @property int $from_detail_id 订单明细ID
 * @property int $follower_id 跟单人ID
 * @property int $produce_status 生产状态 1待审核 2待分配 3待生产 4生产中 5待出厂 6部分出厂 7已出厂 
 * @property int $qiban_type 起版类型 1有款起版 2无款起版 0 非起版
 * @property string $style_sn 款号
 * @property string $qiban_sn 起版编号
 * @property int $status 状态 -1删除 0禁用 1启用
 * @property int $created_at
 * @property int $updated_at
 */
class Produce extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'from_type', 'from_order_id', 'from_detail_id', 'follower_id', 'produce_status', 'qiban_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['produce_sn', 'from_order_sn', 'style_sn', 'qiban_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'produce_sn' => '布产单编号',
            'from_type' => '订单来源(1订单，2采购单)',
            'from_order_id' => '来源订单id',
            'from_order_sn' => '来源订单编号',
            'from_detail_id' => '订单明细ID',
            'follower_id' => '跟单人ID',
            'produce_status' => '生产状态 1待审核 2待分配 3待生产 4生产中 5待出厂 6部分出厂 7已出厂 ',
            'qiban_type' => '起版类型 1有款起版 2无款起版 0 非起版',
            'style_sn' => '款号',
            'qiban_sn' => '起版编号',
            'status' => '状态 -1删除 0禁用 1启用',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
