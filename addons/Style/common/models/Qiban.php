<?php

namespace addons\Style\common\models;

use addons\Purchase\common\models\PurchaseGoods;
use common\models\backend\Member;
use Yii;
use common\helpers\StringHelper;

/**
 * This is the model class for table "style_qiban".
 *
 * @property int $id 款式ID
 * @property int $merchant_id 商户ID
 * @property string $qiban_name 起版名称
 * @property string $qiban_sn 起版编号
 * @property int $qiban_type 1:有款起版；无款起版
 * @property string $qiban_image_sn 图纸编号
 * @property int $style_id 款式ID
 * @property string $style_sn  款号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property int $jintuo_type 金托类型
 * @property int $style_source_id 款式来源
 * @property int $style_channel_id 款式渠道
 * @property int $style_sex 款式性别 1男 2女 3通用款
 * @property string $style_image 商品主图
 * @property string $style_images 起版图库
 * @property string $sale_price 销售价
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property int $goods_num 商品数量
 * @property int $is_inlay 是否镶嵌 1是 0否
 * @property int $audit_status 款式审核 0待审核，1通过，2不通过
 * @property string $audit_remark 审核失败原因
 * @property int $audit_time 审核时间
 * @property int $auditor_id 审核人
 * @property int $sort 排序
 * @property string $stone_info 石料信息
 * @property string $parts_info 配件信息
 * @property string $remark 款式备注
 * @property int $status 款式状态 0下架，1正常，-1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $format_sn 图纸编号
 * @property string $format_images 图纸图片
 * @property string $format_video 版式视频
 * @property string $format_info 版式工艺信息
 * @property string $format_remark 版式备注
 * @property int $is_apply 是否采购申请 0：不是采购：1 是采购未审核，2是采购已审核
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
            [['merchant_id', 'qiban_type', 'style_id', 'style_cate_id', 'product_type_id', 'jintuo_type', 'style_source_id','qiban_source_id', 'style_channel_id', 'style_sex', 'goods_num', 'is_inlay', 'audit_status', 'audit_time', 'auditor_id', 'sort', 'status', 'creator_id',
                'created_at', 'updated_at', 'is_apply'], 'integer'],
            [['warranty_period'],'safe'],
            [['sale_price', 'market_price','kinto_price','starting_fee', 'cost_price'], 'number'],
            [['format_info'], 'string'],
            [['qiban_name', 'audit_remark', 'stone_info', 'parts_info', 'remark', 'format_remark'], 'string', 'max' => 255],
            [['qiban_sn', 'style_sn', 'format_sn'], 'string', 'max' => 30],
            [['style_image', 'format_images', 'format_video'], 'string', 'max' => 500],
            [['style_images'], 'string', 'max' => 2000],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '款式ID'),
            'merchant_id' => Yii::t('app', '商户ID'),
            'qiban_name' => Yii::t('app', '起版名称'),
            'qiban_sn' => Yii::t('app', '起版编号'),
            'qiban_type' => Yii::t('app', '起版类型'),
            'style_id' => Yii::t('app', '款式ID'),
            'style_sn' => Yii::t('app', ' 款号'),
            'style_cate_id' => Yii::t('app', '款式分类'),
            'product_type_id' => Yii::t('app', '产品线'),
            'jintuo_type' => Yii::t('app', '金托类型'),
            'style_source_id' => Yii::t('app', '款式来源'),
            'qiban_source_id' => Yii::t('app', '起版来源'),
            'style_channel_id' => Yii::t('app', '款式渠道'),
            'style_sex' => Yii::t('app', '款式性别'),
            'style_image' => Yii::t('app', '商品主图'),
            'style_images' => Yii::t('app', '起版图库'),
            'sale_price' => Yii::t('app', '销售价'),
            'market_price' => Yii::t('app', '市场价'),
            'cost_price' => Yii::t('app', '单品单价'),
            'goods_num' => Yii::t('app', '商品数量'),
            'is_inlay' => Yii::t('app', '是否镶嵌'),
            'audit_status' => Yii::t('app', '款式审核'),
            'audit_remark' => Yii::t('app', '审核失败原因'),
            'audit_time' => Yii::t('app', '审核时间'),
            'auditor_id' => Yii::t('app', '审核人'),
            'sort' => Yii::t('app', '排序'),
            'stone_info' => Yii::t('app', '石料规格'),
            'parts_info' => Yii::t('app', '配件信息'),
            'remark' => Yii::t('app', '起版备注'),
            'status' => Yii::t('app', '起版状态'),
            'creator_id' => Yii::t('app', '创建人'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
            'warranty_period' => Yii::t('app', '款式保版期'),
            'format_sn' => Yii::t('app', '图纸编号'),
            'format_images' => Yii::t('app', '图纸图片'),
            'format_video' => Yii::t('app', '版式视频'),
            'format_info' => Yii::t('app', '版式工艺信息'),
            'format_remark' => Yii::t('app', '版式备注'),
            'is_apply' => Yii::t('app', '是否采购申请'),
            'kinto_price' => Yii::t('app', '金托总价（含副石）'),
            'starting_fee' => Yii::t('app', '起版费'),

        ];
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeValidate()
    {
        $style_images = $this->style_images;
        if(is_array($style_images)){
            $this->style_image = $style_images[0] ?? '';
            $this->style_images = join(',',$style_images);
        }

        $this->warranty_period = StringHelper::dateToInt($this->warranty_period);
        return parent::beforeValidate();
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
