<?php

namespace addons\Purchase\common\models;

use common\models\backend\Member;
use Yii;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\StyleChannel;

/**
 * This is the model class for table "purchase_apply_goods".
 *
 * @property int $id ID
 * @property int $apply_id 采购单ID
 * @property string $goods_sn 款号/起版号
 * @property int $goods_type 商品类型
 * @property int $goods_num 商品数量
 * @property string $goods_name 商品名称
 * @property string $style_id 款号/起版id
 * @property string $style_sn 款号
 * @property string $qiban_sn
 * @property int $qiban_type 起版类型 0非起版 1有款起版 2无款起版 
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property int $style_channel_id 归属渠道
 * @property int $style_sex 款式性别
 * @property int $jintuo_type 金托类型
 * @property int $is_inlay 是否镶嵌
 * @property string $cost_price 成本价
 * @property int $is_apply 是否申请修改
 * @property string $apply_info 申请修改数据
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $stone_info 石料信息
 * @property string $parts_info 配件信息
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseApplyGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_apply_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apply_id','goods_sn','style_cate_id','product_type_id','jintuo_type'], 'required'],
            [['style_id','apply_id','goods_type', 'goods_num','creator_id','created_at','auditor_id', 'audit_status', 'audit_time',  'qiban_type', 'style_cate_id', 'product_type_id', 'style_channel_id', 'style_sex', 'jintuo_type', 'is_inlay', 'is_apply', 'status', 'created_at', 'updated_at','format_sn'], 'integer'],
            [['cost_price'], 'number'],
            [['apply_info','format_info'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'stone_info', 'parts_info', 'remark','audit_remark','format_remark'], 'string', 'max' => 255],
            [['style_sn', 'qiban_sn'], 'string', 'max' => 30],            
            [['goods_image'], 'string', 'max' => 100],
            [['format_images','format_video'], 'string', 'max' => 500],
            [['goods_images'],'parseGoodsImages'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apply_id' => '采购单ID',
            'goods_sn' => '款号',
            'goods_num' => '商品数量',
            'goods_name' => '商品名称',
            'goods_type'=>'商品类型',
            'goods_image' => '商品图片',
            'goods_images' => '商品图库',
            'style_sn' => '款号',
            'qiban_sn' => '起版号',
            'qiban_type' => '起版类型',
            'style_channel_id' => '归属渠道',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',            
            'style_sex' => '款式性别',
            'jintuo_type' => '金托类型',
            'cost_price' => '成本价',
            'is_inlay' => '是否镶嵌',            
            'is_apply' => '是否申请修改',
            'apply_info' => '修改数据',
            'status' => '状态',
            'stone_info' => '石料信息',
            'parts_info' => '配件信息',
            'remark' => '采购备注',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'creator_id' => '申请人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'format_sn' => '图纸编号',
            'format_images' => '版式图片',
            'format_video' => '上传视频',
            'format_remark' => '版式备注',
        ];
    }
    /**
     * 商品图库
     */
    public function parseGoodsImages()
    {
        $goods_images = $this->goods_images;
        if(is_array($goods_images)){
            $this->goods_image  = $goods_images[0] ?? '';
            $this->goods_images = implode(',',$goods_images);            
        }
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
     * 采购申请单一对一
     * @return \yii\db\ActiveQuery
     */
    public function getApply()
    {
        return $this->hasOne(PurchaseApply::class, ['id'=>'apply_id'])->alias('apply');
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
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
    /**
     * 商品属性列表
     * @return \yii\db\ActiveQuery
     */
    public function getAttrs()
    {
        return $this->hasMany(PurchaseApplyGoodsAttribute::class, ['id'=>'id'])->alias('attrs')->orderBy('sort asc');
    }
    
}
