<?php

namespace addons\Style\common\models;

use Yii;
use common\models\backend\Member;
use addons\Supply\common\models\Supplier;

/**
 * 款式表 Model
 *
 * @property int $id 款式ID
 * @property int $merchant_id 商户ID
 * @property string $style_sn 款式编号
 * @property int $style_cate_id 产品分类
 * @property int $product_type_id 产品线
 * @property int $style_sex 性别
 * @property string $style_name 名称
 * @property string $style_image 商品主图
 * @property string $style_material 款式材质
 * @property string $sale_price 销售价
 * @property int $factory_id 默认工厂
 * @property string $factory_mo 工厂模号
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property string $cost_price_min 成本价最小值
 * @property string $cost_price_max 成本价最大值
 * @property string $goods_num 商品数量
 * @property string $is_inlay 是否镶嵌
 * @property string $is_autosn 是否自动编款
 * @property int $style_channel_id 款式渠道
 * @property int $is_lock 商品锁定 0未锁，1已锁
 * @property int $is_gift 是否赠品
 * @property int $supplier_id 供应商id
 * @property int $status 款式状态 0下架，1正常，-1删除
 * @property int $audit_status 商品审核 1通过，0未通过，10审核中
 * @property int $auditor_id 审核人
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核失败原因
 * @property int $creator_id 添加人
 * @property int $created_at 添加时间
 * @property int $updated_at 更新时间
 */
class Style extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return static::tableFullName("style");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['id','factory_id','style_material','product_type_id','style_cate_id','style_source_id','style_channel_id','style_sex','is_made', 'merchant_id','sale_volume','goods_num','is_inlay','is_autosn', 'is_gift', 'status', 'audit_status','creator_id','auditor_id','audit_time','created_at', 'updated_at'], 'integer'],
                [['style_material','product_type_id','style_channel_id','style_cate_id','style_sex','style_name'], 'required'],
                [['sale_price', 'market_price', 'cost_price','cost_price_min','cost_price_max'], 'number'],
                ['cost_price','compare','compareValue' => 0, 'operator' => '>'],
                ['cost_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                [['style_sn'], 'string', 'max' => 50],
                [['factory_mo'], 'string', 'max' => 30],
                [['style_image','style_3ds'], 'string', 'max' => 100],
                [['audit_remark','remark','style_name'], 'string', 'max' => 255],
                [['style_sn'],'unique'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => "ID",
            'merchant_id' => '商户',
            'style_sn' => '款式编号',
            'style_name' => '款式名称',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'style_source_id' => '款式来源',
            'style_channel_id' =>'归属渠道',
            'style_sex' => '款式性别',
            'style_material' => '款式材质',
            'factory_id' => '默认工厂',
            'factory_mo' => '默认工厂模号',
            'style_image' => '款式图片',
            'sale_price' => '销售价',
            'sale_volume' => '销量',
            'market_price' => '市场价',
            'cost_price' =>'成本价',
            'goods_num'=> "Sku数量",
            'is_inlay'=> "是否镶嵌",
            'is_autosn'=> "款号生成方式",
            'is_made' => '是否支持定制',
            'is_gift' => '是否赠品',
            'audit_status' => "审核状态",
            'audit_remark' => "审核备注",
            'audit_time' => "审核时间",
            'auditor_id' => "审核人",
            'creator_id'=>'创建人', 
            'status' => '状态',
            'remark' => '备注', 
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }    
    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias('type');
    }
    /**
     * 款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    } 
    /**
     * 款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
    }    
    /**
     * 款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(StyleSource::class, ['id'=>'style_source_id'])->alias('source');
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
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
    /**
     * 关联工厂一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'factory_id'])->alias('supplier');
    }
    
}
