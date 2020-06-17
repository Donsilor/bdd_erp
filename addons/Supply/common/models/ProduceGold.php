<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "produce_stone".
 *
 * @property int $id id主键
 * @property int $produce_id 布产id
 * @property string $order_sn 订单号
 * @property string $material_type 材质类型
 * @property int $caigou_time 采购时间（记录最新的一次采购时间）
 * @property int $songliao_time 已送生产部时间(已送生产部的最新一次时间)
 * @property int $peiliao_time 配料中时间（操作配料中的最新时间）
 * @property string $caigou_user 采购人（操作采购中的人员）
 * @property string $songliao_user 送料人（已送生产部操作人员）
 * @property string $remark 备注
 * @property string $peiliao_user 配料人（配料中操作人员）
 * @property int $peiliao_status 配料状态（需建数据字典）
 * @property int $creator_id 创建人ID
 * @property string $creator_name 创建人
 * @property int $created_at 添加时间
 * @property int $updated_at
 */
class ProduceGold extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_gold');
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['produce_id', 'caigou_time', 'songliao_time', 'peiliao_time', 'peiliao_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
                [['order_sn', 'caigou_user', 'songliao_user', 'peiliao_user', 'creator_name'], 'string', 'max' => 30],
                [['gold_type'], 'string', 'max' => 10],
                [['gold_weight'], 'number'],
                [['gold_spec','remark'], 'string', 'max' => 255],
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
                'gold_type' => '金料类型', 
                'gold_weight' => '金料总重',
                'gold_spec' => '金料规格',
                'caigou_time' => '采购时间',
                'songliao_time' => '送料最新时间',
                'peiliao_time' => '配料最新时间',
                'caigou_user' => '采购人',
                'songliao_user' => '送料人',                
                'peiliao_user' => '配料人',
                'peiliao_status' => '配料状态',
                'remark' => '备注',                
                'creator_id' => '创建人ID',
                'creator_name' => '创建人',
                'created_at' => '添加时间',
                'updated_at' => '更新时间',
        ];
    }
}
