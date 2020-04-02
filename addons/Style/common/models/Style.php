<?php

namespace addons\style\common\models;

use Yii;
use common\helpers\ArrayHelper;

/**
 * 款式表 Model
 *
 * @property int $id 款式ID
 * @property string $style_sn 款式编号
 * @property int $cat_id 产品分类
 * @property int $type_id 产品线
 * @property int $merchant_id 商户ID
 * @property string $style_image 商品主图
 * @property string $style_3ds 360主图
 * @property string $goods_images 商品图库
 * @property string $style_attr 款式属性
 * @property string $style_custom 款式自定义属性
 * @property string $style_salepolicy 款式销售政策
 * @property string $goods_salepolicy 商品销售政策
 * @property string $style_spec 款式规格属性
 * @property string $goods_body 商品内容
 * @property string $mobile_body 手机端商品描述
 * @property string $sale_price 销售价
 * @property string $sale_volume 销量
 * @property string $virtual_volume 虚拟销量
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property string $goods_storage 库存量
 * @property string $goods_clicks 浏览量
 * @property string $virtual_clicks 虚拟浏览量
 * @property int $storage_alarm 库存报警值
 * @property int $is_recommend 商品推荐 1是，0否，默认为0
 * @property int $is_lock 商品锁定 0未锁，1已锁
 * @property int $supplier_id 供应商id
 * @property int $status 款式状态 0下架，1正常，-1删除
 * @property int $verify_status 商品审核 1通过，0未通过，10审核中
 * @property string $verify_remark 审核失败原因
 * @property int $created_at 商品添加时间
 * @property int $updated_at
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
                [['id','product_type_id','style_cate_id','style_source_id','style_channel_id','style_sex','is_made', 'merchant_id','sale_volume','goods_num','status', 'audit_status','created_at', 'updated_at'], 'integer'],
                [['product_type_id','style_cate_id','style_sn','style_sex','style_name'], 'required'],
                [['sale_price', 'market_price', 'cost_price'], 'number'],
                ['sale_price','compare','compareValue' => 0, 'operator' => '>'],
                ['market_price','compare','compareValue' => 0, 'operator' => '>'],
                ['cost_price','compare','compareValue' => 0, 'operator' => '>'],
                ['market_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                ['sale_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                ['cost_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                [['style_sn'], 'string', 'max' => 50],
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
            'style_image' => '款式图片',
            'style_3ds' => '360主图',
            'sale_price' => '销售价',
            'sale_volume' => '销量',
            'market_price' => '市场价',
            'cost_price' =>'成本价',
            'goods_num'=> "商品数量",
            'is_made' => '是否支持定制',   
            'status' => '状态',
            'remark' => '备注',
            'audit_status' => "审核状态",
            'audit_remark' => "审核备注",
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
}
