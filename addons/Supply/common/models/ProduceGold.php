<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "produce_stone".
 *
 * @property int $id id主键
 * @property int $produce_id 布产id
 * @property string $delivery_no 领料单号
 * @property string $supplier_id 供应商
 * @property string $from_order_sn 来源单号
 * @property string $from_type 来源类型
 * @property string $gold_type 金料类型
 * @property int $caigou_time 采购时间（记录最新的一次采购时间）
 * @property int $songliao_time 已送生产部时间(已送生产部的最新一次时间)
 * @property int $peiliao_time 配料中时间（操作配料中的最新时间）
 * @property string $caigou_user 采购人（操作采购中的人员）
 * @property string $songliao_user 送料人（已送生产部操作人员）
 * @property string $remark 采购备注
 * @property string $peiliao_user 配料人（配料中操作人员）
 * @property int $peiliao_status 配料状态（需建数据字典）
 * @property string $peiliao_remark 配料备注
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
                [['produce_id','from_type','supplier_id' ,'caigou_time', 'songliao_time', 'peiliao_time', 'peiliao_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
                [['from_order_sn','delivery_no', 'caigou_user', 'songliao_user', 'peiliao_user', 'creator_name'], 'string', 'max' => 30],
                [['gold_type'], 'string', 'max' => 10],
                [['gold_weight'], 'number'],
                [['produce_sn'], 'string', 'max' => 30],
                [['gold_spec','peiliao_remark','remark'], 'string', 'max' => 255],
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
                'delivery_no'=> '送料单号',
                'supplier_id'=> '加工商',
                'from_order_sn' => '来源单号',
                'from_type' => '来源类型',
                'gold_type' => '金料类型', 
                'gold_weight' => '金料总重(g)',
                'gold_spec' => '金料规格',
                'caigou_time' => '采购时间',
                'songliao_time' => '送料最新时间',
                'peiliao_time' => '配料最新时间',
                'caigou_user' => '采购人',
                'songliao_user' => '送料人',                
                'peiliao_user' => '配料人',
                'peiliao_status' => '配料状态',
                'peiliao_remark' => '配料备注', 
                'remark' => '采购备注',                
                'creator_id' => '创建人ID',
                'creator_name' => '申请人',
                'created_at' => '申请时间',
                'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 配石明细   一对多
     * @return \yii\db\ActiveQuery
     */
    public function getGoldGoods()
    {
        return $this->hasMany(ProduceGoldGoods::class, ['id'=>'id'])->alias('goldGoods');
    }    
    /**
     * 布产单   一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProduce()
    {
        return $this->hasOne(Produce::class, ['id'=>'produce_id'])->alias('produce');
    }
    /**
     * 对应供应商模型
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
}
