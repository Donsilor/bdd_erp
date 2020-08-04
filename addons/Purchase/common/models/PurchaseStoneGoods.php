<?php

namespace addons\Purchase\common\models;

use addons\Sales\common\models\SaleChannel;
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
 * @property double $stone_weight 单颗石重(ct)
 * @property string $cost_price 石料总额
 * @property string stone_type 石料类型
 * @property string $stone_price 石料价格/克拉
 * @property int $stone_num 石料数量
 * @property string $stone_shape 石料形状
 * @property string $stone_color 石料颜色
 * @property string $stone_clarity 石料净度
 * @property string $stone_cut 切工
 * @property string $stone_symmetry 对称
 * @property string $stone_polish 抛光
 * @property string $stone_fluorescence 荧光
 * @property string $stone_colour 石料色彩
 * @property string $stone_size 石料尺寸
 * @property string $spec_remark 规格备注
 * @property int $channel_id 渠道
 * @property string $cert_type 证书类型
 * @property string $cert_id 证书号
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
            [['purchase_id','goods_name','goods_sn','stone_num'], 'required'],
            [['purchase_id', 'goods_num', 'stone_num', 'channel_id', 'is_apply', 'is_receipt', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'stone_weight', 'cost_price', 'stone_price'], 'number'],
            [['apply_info'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'spec_remark', 'remark'], 'string', 'max' => 255],
            [['stone_shape', 'stone_color', 'stone_clarity', 'stone_cut', 'stone_symmetry', 'stone_polish', 'stone_fluorescence', 'stone_colour', 'cert_type'], 'string', 'max' => 10],
            [['cert_id'], 'string', 'max' => 30],
            [['stone_size'], 'string', 'max' => 100],
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
            'goods_name' => '石料名称',
            'goods_weight' => '石料总重(ct)',
            'goods_num' => '石料数量',
            'cost_price' => '石料总额',
            'stone_type' => '石料类型',
            'stone_weight' => '单颗石重(ct)',
            'stone_price' => '石料单价/ct',
            'stone_num' => '石料粒数',
            'stone_shape' => '石料形状',
            'stone_color' => '颜色',
            'stone_clarity' => '净度',
            'stone_cut' => '切工',
            'stone_symmetry' => '对称',
            'stone_polish' => '抛光',
            'stone_fluorescence' => '荧光',
            'stone_colour' => '石料色彩',
            'stone_size' => '石料尺寸(mm)',
            'cert_type' => '证书类型',
            'cert_id' => '证书号',
            'spec_remark' => '规格备注',
            'channel_id' => '渠道',
            'is_apply' => '是否申请修改',
            'apply_info' => '申请信息',
            'is_receipt' => '是否已收货',
            'status' => '状态',
            'remark' => '石料备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 对应渠道模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id']);
    }
}
