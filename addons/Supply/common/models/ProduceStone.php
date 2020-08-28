<?php

namespace addons\Supply\common\models;

use Yii;
use addons\Warehouse\common\models\WarehouseStone;

/**
 * This is the model class for table "produce_stone".
 *
 * @property int $id id主键
 * @property int $produce_id 布产id
 * @property string $order_sn 订单号
 * @property string $delivery_no 送石单号
 * @property int $supplier_id 供应商
 * @property string $secai 石头色彩
 * @property string $color 石头颜色
 * @property string $clarity 石头净度
 * @property string $shape 石头形状
 * @property string $cert_type 证书类型
 * @property string $cert_no 证书号
 * @property string $carat 石头大小
 * @property int $stone_sn 石包编号
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
 * @property string $peishi_remark 配石备注
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
            [['stone_num'], 'required'],
            [['stone_weight','carat'], 'number'],
            [['produce_id','is_increase','from_type', 'stone_num', 'stone_position','supplier_id', 'caigou_time', 'songshi_time', 'peishi_time', 'peishi_status','audit_status', 'audit_time','creator_id', 'created_at', 'updated_at'], 'integer'],
            [['from_order_sn','stone_sn','delivery_no','cert_no' ,'caigou_user', 'songshi_user', 'peishi_user','audit_user', 'creator_name'], 'string', 'max' => 30],
            [['secai','color', 'clarity', 'shape', 'cert_type', 'stone_type'], 'string', 'max' => 10],
            [['produce_sn'], 'string', 'max' => 30],
            [['remark','stone_spec','audit_remark','peishi_remark'], 'string', 'max' => 255],
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
            'from_type' => '来源类型',
            'from_order_sn' => '来源订单号', 
            'delivery_no'=>'领(送)石单',
            'supplier_id'=>'加工商', 
            'secai' => '石头色彩',
            'carat' => '石头重量(ct)',
            'color' => '石头颜色',
            'clarity' => '石头净度',
            'shape' => '石头形状',
            'cert_type' => '证书类型',
            'cert_no' => '证书号',            
            'stone_spec'=>'石头规格',
            'stone_num' => '石头数量',
            'stone_weight' => '石头总重(ct)',
            'stone_type' => '石头类型',
            'stone_position' => '石头位置',
            'stone_sn' =>'石包编号',    
            'caigou_time' => '采购时间',
            'songshi_time' => '送石最新时间',
            'peishi_time' => '配石最新时间',
            'caigou_user' => '采购人',
            'songshi_user' => '送石人',
            'audit_status' => '审核状态',
            'audit_user' => '审核人',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',            
            'peishi_user' => '配石人',
            'peishi_status' => '配石状态',
            'peishi_remark' => '配石备注',
            'remark' => '采购备注',
            'is_increase' => '补石单',
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
    public function getStoneGoods()
    {
        return $this->hasMany(ProduceStoneGoods::class, ['id'=>'id'])->alias('stoneGoods');
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
