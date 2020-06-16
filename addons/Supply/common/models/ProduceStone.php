<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "produce_stone".
 *
 * @property int $id id主键
 * @property int $produce_id 布产id
 * @property string $order_sn 订单号
 * @property string $color 石头颜色
 * @property string $clarity 石头净度
 * @property string $shape 石头形状
 * @property string $cert_type 证书类型
 * @property string $cert_no 证书号
 * @property string $carat 石头大小
 * @property int $stone_num 石头数量(布产商品数量*石头粒数)
 * @property string $stone_type 石头类型
 * @property int $stone_position 石头位置 0：主石 ，1：副石1，2：副石2，3：副石3
 * @property int $caigou_time 采购时间（记录最新的一次采购时间）
 * @property int $songshi_time 已送生产部时间(已送生产部的最新一次时间)
 * @property int $peishi_time 配石中时间（操作配石中的最新时间）
 * @property string $caigou_user 采购人（操作采购中的人员）
 * @property string $songshi_user 送石人（已送生产部操作人员）
 * @property string $remark 配石备注
 * @property string $peishi_user 配石人（配石中操作人员）
 * @property int $peishi_status 配石状态（需建数据字典）
 * @property int $creator_id 创建人ID
 * @property string $creator_name 创建人
 * @property int $created_at 添加时间
 * @property int $updated_at
 */
class ProduceStone extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_stone');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produce_id', 'stone_num', 'stone_position', 'caigou_time', 'songshi_time', 'peishi_time', 'peishi_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['order_sn', 'caigou_user', 'songshi_user', 'peishi_user', 'creator_name'], 'string', 'max' => 30],
            [['color', 'clarity', 'shape', 'cert_type', 'cert_no', 'carat', 'stone_type'], 'string', 'max' => 50],
            [['remark','stone_spec'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => "ID",
            'produce_id' => '布产ID',
            'produce_sn' => '布产编号',
            'order_sn' => '订单号',
            'color' => '石头颜色',
            'clarity' => '石头净度',
            'shape' => '石头形状',
            'cert_type' => '证书类型',
            'cert_no' => '证书号',
            'carat' => '石头大小',
            'stone_spec'=>'石头规格',
            'stone_num' => '石头数量',
            'stone_type' => '石头类型',
            'stone_position' => '石头位置',
            'caigou_time' => '采购时间',
            'songshi_time' => '送石最新时间',
            'peishi_time' => '配石最新时间',
            'caigou_user' => '采购人',
            'songshi_user' => '送石人',
            'remark' => '配石备注',
            'peishi_user' => '配石人',
            'peishi_status' => '配石状态',
            'creator_id' => '创建人ID',
            'creator_name' => '创建人',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}
