<?php

namespace addons\Supply\common\models;

/**
 * This is the model class for table "supply_factory".
 *
 * @property int $id 工厂ID
 * @property int $merchant_id 商户ID
 * @property string $factory_code 工厂编号
 * @property string $factory_name 工厂名称
 * @property int $supplier_id 供应商ID
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class Factory extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('factory');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'supplier_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['factory_code'], 'string', 'max' => 30],
            [['factory_name'], 'string', 'max' => 100],
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
            'factory_code' => '工厂编号',
            'factory_name' => '工厂名称',
            'supplier_id' => '供应商ID',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
