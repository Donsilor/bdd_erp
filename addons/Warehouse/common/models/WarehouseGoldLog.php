<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_gold_log".
 *
 * @property int $id ID
 * @property int $gold_id 金料ID
 * @property string $order_sn 订单号
 * @property string $bill_no 单据编号
 * @property int $adjust_type 调整类型 0扣减 1增加
 * @property string $gold_weight 金料重量
 * @property string $stock_weight 库存重量
 * @property string $remark 备注
 * @property int $status 状态
 * @property int $creator_id 操作人ID
 * @property string $creator 操作人
 * @property int $created_at 操作时间
 * @property int $updated_at 更新时间
 */
class WarehouseGoldLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_gold_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gold_id'], 'required'],
            [['id', 'gold_id', 'adjust_type', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'stock_weight'], 'number'],
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
            'gold_id' => '金料ID',
            'order_sn' => '订单号',
            'bill_no' => '单据编号',
            'adjust_type' => '调整类型',
            'gold_weight' => '调整重量(g)',
            'stock_weight' => '库存重量(g)',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '操作人ID',
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
     * 金料库存
     * @return \yii\db\ActiveQuery
     */
    public function getGold()
    {
        return $this->hasOne(WarehouseGold::class, ['id'=>'gold_id'])->alias('gold');
    }
}
