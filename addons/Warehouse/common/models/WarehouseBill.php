<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;
use addons\Supply\common\models\Supplier;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\StyleChannel;
use addons\Sales\common\models\SaleChannel;
use addons\Shop\common\models\Order;

/**
 * This is the model class for table "warehouse_bill".
 *
 * @property int $id ID
 * @property int $merchant_id 商户ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property int $bill_status 仓储单据状态
 * @property int $channel_id 渠道
 * @property int $supplier_id 供应商
 * @property int $put_in_type 入库方式
 * @property string $order_sn 订单号
 * @property int $order_type 订单类型 1收货单 2.客订单
 * @property int $goods_num 货品总数量
 * @property string $total_cost 总成本
 * @property string $total_sale 实际销售总额
 * @property string $total_market 市场名义总额
 * @property int $to_warehouse_id 入库仓库
 * @property int $to_company_id 入库公司
 * @property int $from_company_id 出库公司
 * @property int $from_warehouse_id 出库仓库
 * @property int $delivery_type 出库类型
 * @property int $salesman_id 销售人
 * @property string $send_goods_sn 送货单号
 * @property int $is_settle_accounts 是否结价
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 单据备注
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBill extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['bill_status', 'audit_status','bill_no'], 'required'],
            [['id', 'merchant_id', 'bill_status', 'channel_id', 'supplier_id', 'put_in_type', 'order_type', 'goods_num', 'to_warehouse_id', 'to_company_id', 'from_company_id', 'from_warehouse_id', 'is_settle_accounts', 'delivery_type', 'salesman_id', 'auditor_id', 'audit_status', 'audit_time', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['total_cost', 'total_sale', 'total_market'], 'number'],
            [['bill_no', 'order_sn', 'send_goods_sn'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
            [['bill_no'], 'unique'],
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
            'bill_no' => '单据编号',
            'bill_type' => '单据类型',
            'bill_status' => '单据状态',
            'channel_id' => '渠道',
            'supplier_id' => '供应商',
            'put_in_type' => '入库方式',
            'order_sn' => '订单号',
            'order_type' => '订单类型',
            'goods_num' => '货品数量',
            'total_cost' => '总成本',
            'total_sale' => '实际销售总额',
            'total_market' => '市场价总额',
            'to_warehouse_id' => '入库仓库',
            'to_company_id' => '入库公司',
            'from_company_id' => '出库公司',
            'from_warehouse_id' => '出库仓库',
            'delivery_type' => '出库类型',
            'salesman_id' => '销售人',
            'send_goods_sn' => '送货单号',
            'is_settle_accounts' => '是否结价',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '单据备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }
    /**
     * 渠道
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }
    /**
     * 销售渠道
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }
    /**
     * 供应商 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
    /**
     * 出库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getFromWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'from_warehouse_id'])->alias('fromWarehouse');
    }
    /**
     * 入库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getToWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'to_warehouse_id'])->alias('toWarehouse');
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
    /**
     * 销售人
     * @return \yii\db\ActiveQuery
     */
    public function getSalesman()
    {
        return $this->hasOne(Member::class, ['id'=>'salesman_id'])->alias('salesman');
    }
    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias("productType");
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias("styleCate");
    }
    /**
     * 盘点单附属表
     * @return \yii\db\ActiveQuery
     */
    public function getBillW()
    {
        return $this->hasOne(WarehouseBillW::class, ['id'=>'id'])->alias('billW');
    }
    /**
     * 借货单附属表
     * @return \yii\db\ActiveQuery
     */
    public function getBillJ()
    {
        return $this->hasOne(WarehouseBillJ::class, ['id'=>'id'])->alias('billJ');
    }
    /**
     * 订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['order_sn'=>'order_sn'])->alias('order');
    }
}
