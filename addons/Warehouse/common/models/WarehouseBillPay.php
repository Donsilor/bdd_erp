<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;
use addons\Supply\common\models\Supplier;

/**
 * This is the model class for table "warehouse_bill_pay".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property int $supplier_id 供应商ID
 * @property int $pay_content 支付内容
 * @property int $pay_method 结算方式
 * @property int $pay_tax 是否含税
 * @property string $pay_amount 金额
 * @property int $pay_material 结算材质
 * @property string $pay_gold_weight 结算金重
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBillPay extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'supplier_id', 'pay_content', 'pay_method', 'pay_tax'], 'required'],
            [['id', 'bill_id', 'supplier_id', 'pay_content', 'pay_method', 'pay_tax', 'pay_material', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['pay_amount', 'pay_gold_weight'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_id' => '单据ID',
            'supplier_id' => '供应商',
            'pay_content' => '支付内容',
            'pay_method' => '结算方式',
            'pay_tax' => '是否含税',
            'pay_amount' => '结算金额',
            'pay_material' => '结算材质',
            'pay_gold_weight' => '结算金重',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1 删除',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
}
