<?php

namespace addons\Style\common\models;

use addons\Purchase\common\models\PurchaseGoods;
use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "style_qiban".
 *
 * @property int $id 款式ID
 * @property int $merchant_id 商户ID
 * @property string $qiban_name 起版名称
 * @property string $qiban_sn 起版编号
 * @property string $style_sn  款号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property int $style_source_id 款式来源
 * @property int $style_channel_id 款式渠道
 * @property int $style_sex 款式性别 1男 2女 3通用款
 * @property string $style_image 商品主图
 * @property string $sale_price 销售价
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property int $goods_num 商品数量
 * @property int $is_inlay 是否镶嵌
 * @property int $audit_status 款式审核 0待审核，1通过，2不通过
 * @property string $audit_remark 审核失败原因
 * @property int $audit_time 审核时间
 * @property int $auditor_id 审核人
 * @property int $sort 排序
 * @property string $remark 款式备注
 * @property int $status 款式状态 0下架，1正常，-1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Qiban extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return static::tableFullName("qiban");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qiban_sn','style_cate_id','product_type_id','jintuo_type','cost_price','qiban_name'],'required'],
            [['merchant_id', 'style_cate_id', 'product_type_id','is_inlay', 'style_source_id','jintuo_type', 'style_channel_id','qiban_type', 'style_sex', 'goods_num', 'audit_status', 'audit_time', 'auditor_id', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['sale_price', 'market_price', 'cost_price'], 'number'],
            [['qiban_name', 'audit_remark', 'remark','stone_info'], 'string', 'max' => 255],
            [['qiban_sn'], 'string', 'max' => 50],
            [['style_sn'], 'string', 'max' => 30],
            [['style_image'],'parseStyleImages'],
        ];
    }


    /**
     * 款式图库
     */
    public function parseStyleImages()
    {
        $style_image = $this->style_image;
        if(is_array($style_image)){
            $this->style_image = implode(',',$style_image);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'qiban_name' => '起版名称',
            'qiban_sn' => '起版编号',
            'style_sn' => ' 款号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'style_source_id' => '款式来源',
            'style_channel_id' => '归属渠道',
            'qiban_type' => '起版类型',
            'style_sex' => '款式性别',
            'style_image' => '起版主图',
            'sale_price' => '销售价',
            'market_price' => '市场价',
            'cost_price' => '成本价',
            'jintuo_type' => '金托类型',
            'goods_num' => '商品数量',
            'is_inlay' => '是否镶嵌',
            'audit_status' => '审核状态',
            'audit_remark' => '审核失败原因',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
            'sort' => '排序',
            'stone_info' => '石料信息',
            'remark' => '起版备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
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
            $this->creator_id = Yii::$app->user->id;
        }

        $style_image = $this->style_image;
        if(is_array($style_image)){
            $this->style_image = implode(',',$style_image);
        }

        return parent::beforeSave($insert);
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
     * 采购明细一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseGoods()
    {
        return $this->hasOne(PurchaseGoods::class, ['qiban_sn'=>'qiban_sn'])->alias('purchaseGoods');
    }

    /**
     * 款式 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyle()
    {
        return $this->hasOne(Style::class, ['style_sn'=>'style_sn'])->alias('style');
    }
}
