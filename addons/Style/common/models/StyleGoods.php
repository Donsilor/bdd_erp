<?php

namespace addons\style\common\models;

use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property int $id 商品id(SKU)
 * @property int $style_id 款式id
 * @property string $goods_sn 商品编号
 * @property int $goods_type 商品类型
 * @property string $goods_image 商品主图
 * @property int $merchant_id 商户ID
 * @property int $type_id 产品线id
 * @property string $sale_price 商品价格
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property string $promotion_price 促销价格
 * @property int $promotion_type 促销类型 0无促销，1抢购，2限时折扣
 * @property int $storage_alarm 库存报警值
 * @property int $goods_clicks 商品点击数量
 * @property int $sale_volume 销售数量
 * @property int $goods_collects 收藏数量
 * @property int $goods_comments 评价数
 * @property int $goods_stars 好评星级
 * @property int $goods_storage 商品库存
 * @property int $status 商品状态 0下架，1上架，10违规（禁售）
 * @property int $verify_status 商品审核 1通过，0未通过，10审核中
 * @property string $verify_remark
 * @property int $created_at 商品添加时间
 * @property int $updated_at 商品编辑时间
 * @property string $spec_key 规格值唯一key(规格值ID逗号隔开的字符串)
 */
class StyleGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.style_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id', 'type_id','status'], 'required'],
            [['style_id', 'type_id', 'merchant_id', 'type_id','promotion_type', 'storage_alarm', 'goods_clicks', 'sale_volume', 'goods_collects', 'goods_comments', 'goods_stars', 'goods_storage', 'status', 'verify_status', 'created_at', 'updated_at'], 'integer'],
            [['sale_price', 'market_price', 'promotion_price'], 'number'],
            ['sale_price','compare','compareValue' => 0, 'operator' => '>'],
            ['market_price','compare','compareValue' => 0, 'operator' => '>'],
            ['cost_price','compare','compareValue' => 0, 'operator' => '>'],
            [['goods_sn'], 'string', 'max' => 50],
            [['goods_image', 'verify_remark'], 'string', 'max' => 100],
            [['spec_key','id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods', 'ID'),
            'merchant_id' => Yii::t('goods', 'Merchant ID'),
            'style_id' => Yii::t('goods', '款式ID'),
            'goods_sn' => Yii::t('goods', '商品编号'),
            'type_id' => Yii::t('goods', 'Goods Type'),
            'goods_image' => Yii::t('goods', '商品图片'),
            'type_id' => Yii::t('goods', 'Type ID'),
            'cost_price' => Yii::t('goods', 'Cost Price'),
            'sale_price' => Yii::t('goods', '基础销售价'),
            'market_price' => Yii::t('goods', 'Market Price'),
            'promotion_price' => Yii::t('goods', 'Promotion Price'),
            'promotion_type' => Yii::t('goods', 'Promotion Type'),
            'storage_alarm' => Yii::t('goods', 'Storage Alarm'),
            'goods_clicks' => Yii::t('goods', 'Goods Clicks'),
            'sale_volume' => Yii::t('goods', 'Sale Volume'),
            'goods_collects' => Yii::t('goods', 'Goods Collects'),
            'goods_comments' => Yii::t('goods', 'Goods Comments'),
            'goods_stars' => Yii::t('goods', 'Goods Stars'),
            'goods_storage' => Yii::t('goods', 'Goods Storage'),
            'status' => Yii::t('goods', '上架状态'),
            'verify_status' => Yii::t('goods', 'Verify Status'),
            'verify_remark' => Yii::t('goods', 'Verify Remark'),
            'created_at' => Yii::t('goods', 'Created At'),
            'updated_at' => Yii::t('goods', 'Updated At'),
        ];
    }


    /**
     * 对应款式模型
     * @return \yii\db\ActiveQuery
     */
    public function getStyle()
    {
        return $this->hasOne(Style::class, ['id'=>'style_id']);
    }

    /**
     * 对应款式多语言模型
     * @return \yii\db\ActiveQuery
     */
    public function getStyleLang()
    {
        return $this->hasOne(StyleLang::class, ['master_id'=>'style_id'])->where(['goods_style_lang.language'=>Yii::$app->params['language']]);
    }


    /**
     * 对应商品加价率模型
     * @return \yii\db\ActiveQuery
     */
    public function getMarkup()
    {
        if(Yii::$app->params['areaId']){
            return $this->hasOne(GoodsMarkup::class, ['goods_id'=>'id'])->where(['goods_markup.area_id'=>Yii::$app->params['areaId']]);
        }else{
            return $this->hasOne(GoodsMarkup::class, ['goods_id'=>'id']);
        }

    }

}
