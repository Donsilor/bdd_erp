<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "warehouse_parts_log".
 *
 * @property int $id ID
 * @property int $parts_id 配件ID
 * @property string $order_sn 订单号
 * @property string $bill_no 单据编号
 * @property int $adjust_type 调整类型 0扣减 1增加
 * @property int $parts_num 配件数量
 * @property int $stock_num 库存数量
 * @property string $remark 备注
 * @property int $status 状态
 * @property int $creator_id 操作人
 * @property string $creator
 * @property int $created_at 操作时间
 * @property int $updated_at 更新时间
 */
class WarehousePartsLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_parts_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parts_id'], 'required'],
            [['parts_id', 'adjust_type', 'parts_num', 'stock_num', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
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
            'parts_id' => '配件ID',
            'order_sn' => '订单号',
            'bill_no' => '单据编号',
            'adjust_type' => '调整类型 0扣减 1增加',
            'parts_num' => '配件数量',
            'stock_num' => '库存数量',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '操作人',
            'creator' => 'Creator',
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
     * 配件库存
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasOne(WarehouseParts::class, ['id'=>'parts_id'])->alias('parts');
    }
}
