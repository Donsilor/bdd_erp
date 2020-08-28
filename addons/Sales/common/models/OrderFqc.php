<?php

namespace addons\Sales\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "sales_order_fqc".
 *
 * @property int $id ID
 * @property int $order_id 订单ID
 * @property string $order_sn 订单号
 * @property int $problem_type 问题类型
 * @property int $problem 质检问题
 * @property string $remark 质检备注
 * @property int $is_pass 是否质检通过
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class OrderFqc extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_fqc');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['problem_type'], 'required'],
            [['order_id', 'problem_type', 'problem', 'is_pass', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['order_sn'], 'string', 'max' => 50],
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
            'order_id' => '订单ID',
            'order_sn' => '订单号',
            'problem_type' => '问题类型',
            'problem' => '质检问题',
            'remark' => '质检备注',
            'is_pass' => '是否质检通过',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
     * 关联质检未过原因
     * @return \yii\db\ActiveQuery
     */
    public function getFqc()
    {
        return $this->hasOne(FqcConfig::class, ['id'=>'problem'])->alias('fqc');
    }
}
