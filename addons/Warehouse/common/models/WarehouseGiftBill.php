<?php

namespace addons\Warehouse\common\models;

use addons\Sales\common\models\SaleChannel;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "warehouse_gift_bill".
 *
 * @property int $id ID
 * @property int $gift_id 赠品ID
 * @property string $bill_no 单据编号
 * @property int $bill_type 单据类型
 * @property int $num 数量
 * @property int $stock_num 库存数量
 * @property int $channel_id 渠道
 * @property int $bill_status 状态
 * @property int $creator_id 操作人
 * @property string $remark 备注
 * @property int $created_at 操作时间
 * @property int $updated_at 更新时间
 */
class WarehouseGiftBill extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_gift_bill');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gift_id'], 'required'],
            [['gift_id', 'bill_type', 'num', 'stock_num', 'channel_id', 'bill_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['bill_no'], 'string', 'max' => 30],
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
            'bill_no' => '单据编号',
            'bill_type' => '单据类型',
            'num' => '调整数量',
            'stock_num' => '库存数量',
            'channel_id' => '渠道',
            'bill_status' => '状态',
            'creator_id' => '操作人',
            'remark' => '备注',
            'created_at' => '操作时间',
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
     * 对应快递模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('saleChannel');
    }
}
