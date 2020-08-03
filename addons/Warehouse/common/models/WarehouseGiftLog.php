<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_gift_log".
 *
 * @property int $id ID
 * @property int $gift_id 赠品ID
 * @property string $order_sn 订单号
 * @property string $bill_no 单据编号
 * @property int $adjust_type 调整类型 0扣减 1增加
 * @property int $gift_num 赠品数量
 * @property int $stock_num 库存数量
 * @property string $remark 备注
 * @property int $status 状态
 * @property int $creator_id 操作人
 * @property string $creator
 * @property int $created_at 操作时间
 * @property int $updated_at 更新时间
 */
class WarehouseGiftLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_gift_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gift_id'], 'required'],
            [['gift_id', 'adjust_type', 'gift_num', 'stock_num', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['order_sn', 'bill_no', 'creator'], 'string', 'max' => 30],
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
            'gift_id' => '赠品ID',
            'order_sn' => '订单号',
            'bill_no' => '单据编号',
            'adjust_type' => '调整类型',
            'gift_num' => '赠品数量',
            'stock_num' => '库存数量',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '操作人id',
            'creator' => '操作人',
            'created_at' => '操作时间',
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
            $this->creator_id = Yii::$app->user->getId();
            $this->creator = \Yii::$app->user->identity->username;
        }
        return parent::beforeSave($insert);
    }
    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id']);
    }
    /**
     * 赠品库存
     * @return \yii\db\ActiveQuery
     */
    public function getGift()
    {
        return $this->hasOne(WarehouseGift::class, ['id'=>'gift_id'])->alias('gift');
    }
}
