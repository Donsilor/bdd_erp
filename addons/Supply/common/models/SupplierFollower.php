<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_supplier_follower".
 *
 * @property int $id
 * @property int $merchant_id 商户ID
 * @property int $supplier_id 供应商ID
 * @property int $member_id 跟单人ID
 * @property string $member_name 跟单人
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class SupplierFollower extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('supplier_follower');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'merchant_id', 'supplier_id', 'member_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['member_name'], 'string', 'max' => 30],
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
            'merchant_id' => '商户ID',
            'supplier_id' => '供应商ID',
            'member_id' => '跟单人ID',
            'member_name' => '跟单人',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
