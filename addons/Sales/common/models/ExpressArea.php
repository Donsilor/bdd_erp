<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_express_area".
 *
 * @property int $id
 * @property int $express_id 快递ID
 * @property string $express_name 快递名称
 * @property string $delivery_area 配送区域
 * @property string $delivery_time 配送时长
 * @property string $first_price 首重价格
 * @property string $supply_price 续重价格
 * @property string $last_first_price 上次首重价格
 * @property string $last_supply_price 上次续重价格
 * @property int $is_holidays 节假日是否派送
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用
 * @property int $sort 排序
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ExpressArea extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('express_area');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['delivery_area'], 'required'],
            [['express_id', 'is_holidays', 'auditor_id', 'audit_status', 'audit_time', 'status', 'sort', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['first_price', 'supply_price', 'last_first_price', 'last_supply_price'], 'number'],
            [['express_name'], 'string', 'max' => 30],
            [['delivery_area'], 'string', 'max' => 160],
            [['delivery_time'], 'string', 'max' => 20],
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
            'express_id' => '快递ID',
            'express_name' => '快递名称',
            'delivery_area' => '配送区域',
            'delivery_time' => '配送时长',
            'first_price' => '首重价格',
            'supply_price' => '续重价格',
            'last_first_price' => '上次首重价格',
            'last_supply_price' => '上次续重价格',
            'is_holidays' => '节假日是否派送',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '备注',
            'status' => '状态',
            'sort' => '排序',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }

        return parent::beforeSave($insert);
    }

    /**
     * 关联管理员一对一
     * @return
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
    }

    /**
     * 物流信息
     * @return \yii\db\ActiveQuery
     */
    public function getExpress()
    {
        return $this->hasOne(Express::class, ['id'=>'express_id'])->alias('express');
    }
}
