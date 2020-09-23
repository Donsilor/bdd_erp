<?php

namespace addons\Gdzb\common\models;

use common\models\backend\Member;
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
class Follower extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('follower');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id','member_name'], 'required'],
            [['merchant_id',  'member_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['member_name'], 'string', 'max' => 30],
            [['member_id'], 'unique'],
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
            'member_id' => '客服ID',
            'member_name' => '客服',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }


    /**
     * 对应管理员模型
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id'=>'member_id'])->alias('member');
    }


}
