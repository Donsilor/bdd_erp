<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_stone_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property string $goods_sn 款号/起版号
 * @property string $goods_name 商品名称
 * @property double $goods_weight 石料总重(ct)
 * @property int $goods_num 商品数量
 * @property string $cost_price 石料总额
 * @property string stone_type 石料类型
 * @property string $stone_price 石料价格/克拉
 * @property int $stone_num 石料数量
 * @property string $stone_color 石料颜色
 * @property string $stone_clarity 石料净度
 * @property int $is_apply 是否申请修改
 * @property string $apply_info 申请信息
 * @property int $is_receipt 是否申请修改
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseStoneGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_stone_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id','goods_name','stone_type','cost_price','goods_weight','stone_num'], 'required'],
            [['purchase_id', 'goods_num', 'stone_num', 'is_apply', 'is_receipt', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'stone_price'], 'number'],
            [['apply_info'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'remark'], 'string', 'max' => 255],
            [['stone_color', 'stone_clarity'], 'string', 'max' => 10],
            [['put_in_type'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_id' => '采购单',
            'goods_sn' => '石料款号',
            'goods_name' => '商品名称',
            'goods_weight' => '石料总重(ct)',
            'goods_num' => '石包数量',
            'cost_price' => '石包总额',
            'stone_type' => '石料类型',
            'stone_price' => '石料价格/克拉',
            'stone_num' => '石包粒数',
            'stone_color' => '颜色',
            'stone_clarity' => '净度',
            'is_apply' => '是否申请修改',
            'apply_info' => '申请信息',
            'is_receipt' => '是否已收货',
            'status' => '状态',
            'remark' => '石料备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
