<?php

namespace addons\Warehouse\common\models;


use addons\Supply\common\models\Supplier;
use Yii;
use common\models\backend\Member;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;

/**
 * This is the model class for table "warehouse_bill".
 *
 * @property int $id ID
 * @property int $merchant_id 商户ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property int $bill_status 仓储单据状态
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
            [['id', 'merchant_id', 'bill_status', 'supplier_id', 'put_in_type', 'order_type', 'goods_num', 'to_warehouse_id', 'to_company_id', 'from_company_id', 'from_warehouse_id', 'auditor_id', 'audit_status', 'audit_time', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['total_cost', 'total_sale', 'total_market'], 'number'],
            [['bill_no', 'order_sn'], 'string', 'max' => 30],
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
            'bill_status' => '仓储单据状态',
            'supplier_id' => '供应商',
            'put_in_type' => '入库方式',
            'order_sn' => '订单号',
            'order_type' => '订单类型',
            'goods_num' => '货品数量',
            'total_cost' => '总成本',
            'total_sale' => '实际销售总额',
            'total_market' => '市场名义总额',
            'to_warehouse_id' => '入库仓库',
            'to_company_id' => '入库公司',
            'from_company_id' => '出库公司',
            'from_warehouse_id' => '出库仓库',
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
            $this->creator_id = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
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
        return $this->hasOne(Warehouse::class, ['id'=>'to_warehouse_id'])->alias('ToWarehouse');
    }

    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
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
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id']);
    }

    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id']);
    }
}
