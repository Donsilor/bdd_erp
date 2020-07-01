<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_stone_log".
 *
 * @property int $id ID
 * @property int $stone_id 石料ID
 * @property string $order_sn 订单号
 * @property string $bill_no 单据编号
 * @property int $adjust_type 调整类型 0扣减 1增加
 * @property int $stone_cnt 调整粒数
 * @property string $stone_weight 石料重量(ct)
 * @property int $stock_cnt 库存粒数
 * @property string $stock_weight 库存重量(ct)
 * @property string $remark 备注
 * @property int $status 状态
 * @property int $creator_id 操作人ID
 * @property string $creator 操作人
 * @property int $created_at 操作时间
 * @property int $updated_at 更新时间
 */
class WarehouseStoneLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_stone_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stone_id'], 'required'],
            [['id', 'stone_id', 'adjust_type', 'stone_cnt', 'stock_cnt', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['stone_weight', 'stock_weight'], 'number'],
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
            'stone_id' => '石料ID',
            'order_sn' => '订单号',
            'bill_no' => '单据编号',
            'adjust_type' => '调整类型',
            'stone_cnt' => '调整粒数',
            'stone_weight' => '调整重量(ct)',
            'stock_cnt' => '库存粒数',
            'stock_weight' => '库存重量(ct)',
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
     * 石料库存
     * @return \yii\db\ActiveQuery
     */
    public function getStone()
    {
        return $this->hasOne(WarehouseStone::class, ['id'=>'stone_id'])->alias('stone');
    }
}
