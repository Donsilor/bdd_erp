<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_apply_goods".
 *
 * @property int $id ID
 * @property int $apply_id 采购单ID
 * @property string $goods_sn 款号/起版号
 * @property int $goods_num 商品数量
 * @property string $goods_name 商品名称
 * @property string $style_sn 商品编号
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
            [['apply_id'], 'required'],
            [['apply_id', 'goods_num', 'qiban_type', 'style_cate_id', 'product_type_id', 'style_channel_id', 'style_sex', 'jintuo_type', 'is_inlay', 'is_apply', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cost_price'], 'number'],
            [['apply_info'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'stone_info', 'parts_info', 'remark'], 'string', 'max' => 255],
            [['style_sn', 'qiban_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'apply_id' => Yii::t('app', '采购单ID'),
            'goods_sn' => Yii::t('app', '款号/起版号'),
            'goods_num' => Yii::t('app', '商品数量'),
            'goods_name' => Yii::t('app', '商品名称'),
            'style_sn' => Yii::t('app', '商品编号'),
            'qiban_sn' => Yii::t('app', 'Qiban Sn'),
            'qiban_type' => Yii::t('app', '起版类型 0非起版 1有款起版 2无款起版 '),
            'style_cate_id' => Yii::t('app', '款式分类'),
            'product_type_id' => Yii::t('app', '产品线'),
            'style_channel_id' => Yii::t('app', '归属渠道'),
            'style_sex' => Yii::t('app', '款式性别'),
            'jintuo_type' => Yii::t('app', '金托类型'),
            'is_inlay' => Yii::t('app', '是否镶嵌'),
            'cost_price' => Yii::t('app', '成本价'),
            'is_apply' => Yii::t('app', '是否申请修改'),
            'apply_info' => Yii::t('app', '申请修改数据'),
            'status' => Yii::t('app', '状态： -1已删除 0禁用 1启用'),
            'stone_info' => Yii::t('app', '石料信息'),
            'parts_info' => Yii::t('app', '配件信息'),
            'remark' => Yii::t('app', '采购备注'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }
}
