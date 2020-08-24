<?php

namespace addons\Finance\common\models;

use addons\Sales\common\models\SaleChannel;
use addons\Style\common\models\ProductType;
use common\models\common\Department;
use Yii;

/**
 * This is the model class for table "finance_sales_detail".
 *
 * @property int $id
 * @property int $dept_id 部门
 * @property int $sale_channel_id 销售渠道
 * @property string $goods_name 商品名称
 * @property int $product_type_id 产品线
 * @property string $goods_id 货号
 * @property int $goods_num 数量
 * @property string $goods_price 单价
 * @property int $pay_time 平台收款日期
 * @property string $sale_price 销售金额
 * @property int $delivery_time 发货时间
 * @property string $cost_price 采购成本
 * @property int $refund_time 退款时间
 * @property string $refund_price 退款金额
 * @property int $return_time 退货时间
 * @property int $return_type 退款类型
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $creator_id 创建人
 */
class SalesDetail extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('sales_detail');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dept_id', 'sale_channel_id', 'goods_name', 'product_type_id','order_id', 'orde_sn','order_detail_id'], 'required'],
            [['id','dept_id', 'sale_channel_id', 'product_type_id', 'goods_num', 'pay_time', 'delivery_time', 'refund_time', 'return_time', 'return_by', 'created_at', 'updated_at', 'creator_id','order_id','order_detail_id'], 'integer'],
            [['goods_price', 'sale_price', 'cost_price', 'refund_price'], 'number'],
            [['goods_name'], 'string', 'max' => 100],
            [['orde_sn','goods_sn'], 'string', 'max' => 20],
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
            'orde_sn' => '订单号',
            'dept_id' => '部门',
            'sale_channel_id' => '销售渠道',
            'goods_name' => '商品名称',
            'product_type_id' => '产品线',
            'goods_sn' => '货号',
            'goods_num' => '数量',
            'goods_price' => '单价',
            'pay_time' => '平台收款日期',
            'sale_price' => '销售金额',
            'delivery_time' => '发货时间',
            'cost_price' => '采购成本',
            'refund_time' => '退款时间',
            'refund_price' => '退款金额',
            'return_time' => '退货时间',
            'return_by' => '退款方式',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'creator_id' => '创建人',
            'remark' => '备注',
        ];
    }


    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias('type');
    }
    /**
     * 对应销售渠道模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'sale_channel_id'])->alias('saleChannel');
    }


    /**
     * 部门
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id'=>'dept_id'])->alias('department');
    }
}
