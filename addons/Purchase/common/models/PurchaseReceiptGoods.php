<?php

namespace addons\Purchase\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "purchase_receipt_goods".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property string $produce_sn 布产单编号
 * @property string $xuhao 序号
 * @property string $goods_status 收货单货品状态
 * @property string $barcode 条形码编号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $style_sn 款式编号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $factory_mo 工厂模号
 * @property double $finger 指圈
 * @property string $xiangkou 镶口
 * @property int $material 主成色
 * @property double $gold_weight 主成色重
 * @property string $gold_price 主成色买入单价
 * @property double $gold_loss 金损
 * @property int $jintuo_type 金托类型（成品/空托）
 * @property double $gross_weight 毛重
 * @property double $suttle_weight 净重
 * @property string $cost_price 成本价
 * @property string $market_price 市场价
 * @property string $sale_price 销售价
 * @property string $cert_id 证书号
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
 * @property string $goods_remark 商品备注
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $main_stone 主石
 * @property int $main_stone_num 主石数量
 * @property double $main_stone_weight 主石重
 * @property int $main_stone_color 主石颜色
 * @property int $main_stone_clarity 主石净度
 * @property string $main_stone_price 主石买入单价
 * @property string $second_cert_id1 副石证书号
 * @property int $second_stone1 副石1
 * @property int $second_stone_num1 副石1数量
 * @property double $second_stone_weight1 副石1重量
 * @property string $second_stone_price1 副石1买入单价
 * @property int $second_stone2 副石2
 * @property int $second_stone_num2 副石2数量
 * @property double $second_stone_weight2 副石2重量
 * @property string $second_stone_price2 副石2买入单价
 * @property int $second_stone3 副石3
 * @property int $second_stone_num3 副石3数量
 * @property double $second_stone_weight3 副石3重量
 * @property string $second_stone_price3 副石3买入单价
 * @property double $markup_rate 加价率
 * @property string $gong_fee 工费
 * @property double $parts_weight 配件重量
 * @property string $parts_price 配件金额
 * @property string $parts_fee 配件工费
 * @property string $xianqian_fee 镶嵌工费
 * @property int $biaomiangongyi 表面工艺
 * @property string $biaomiangongyi_fee 表面工艺工费
 * @property string $fense_fee 分色工艺工费
 * @property string $bukou_fee 补口工费
 * @property string $cert_fee 证书费
 * @property string $extra_stone_fee 超石费
 * @property string $tax_fee 税费
 * @property string $other_fee 其他费用
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseReceiptGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'receipt_id', 'purchase_sn'], 'required'],
            [['receipt_id', 'style_sex', 'goods_num', 'xuhao', 'goods_status', 'iqc_reason', 'style_cate_id', 'product_type_id', 'style_channel_id','put_in_type', 'to_warehouse_id', 'material', 'jintuo_type', 'main_stone', 'main_stone_num', 'main_stone_color', 'main_stone_clarity', 'second_stone1', 'second_stone_num1', 'second_stone2', 'second_stone_num2', 'second_stone3', 'second_stone_num3', 'biaomiangongyi', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['finger', 'gold_weight', 'gold_price', 'gold_loss', 'gross_weight', 'suttle_weight', 'cost_price', 'market_price', 'sale_price', 'main_stone_weight', 'main_stone_price', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2', 'second_stone_price2', 'second_stone_weight3', 'second_stone_price3', 'markup_rate', 'gong_fee', 'parts_weight', 'parts_price', 'parts_fee', 'xianqian_fee', 'biaomiangongyi_fee', 'fense_fee', 'bukou_fee', 'cert_fee', 'extra_stone_fee', 'tax_fee', 'other_fee','gold_amount'], 'number'],
            [['purchase_sn', 'produce_sn', 'factory_mo', 'cert_id', 'second_cert_id1'], 'string', 'max' => 30],
            [['barcode','product_size'], 'string', 'max' => 100],
            [['goods_name', 'goods_remark', 'iqc_remark'], 'string', 'max' => 255],
            [['style_sn'], 'string', 'max' => 50],
            [['main_cert_id','second_cert_id1','second_stone_sn1','main_stone_sn'], 'string', 'max' => 20],
            [['xiangkou'], 'string', 'max' => 10],
            [['supplier_id', 'receipt_no'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receipt_id' => '采购收货单ID',
            'purchase_sn' => '采购单编号',
            'produce_sn' => '布产单编号',
            'xuhao' => '序号',
            'goods_status' => '货品状态',
            'barcode' => '条形码编号',
            'goods_name' => '商品名称',
            'goods_num' => '数量',
            'style_sn' => '款式编号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'style_sex' => '款式性别',
            'style_channel_id' => '所属渠道',
            'factory_mo' => '工厂模号',
            'finger' => '指圈',
            'xiangkou' => '镶口',
            'material' => '主成色',
            //'material_type' => '材质',
            //'material_color' => '材质颜色',
            'gold_weight' => '主成色重',
            'gold_price' => '主成色买入单价',
            'gold_loss' => '金损',
            'gold_amount' => '金料额',
            'jintuo_type' => '金托类型（成品/空托）',
            'gross_weight' => '毛重',
            'suttle_weight' => '净重',
            'cost_price' => '成本价',
            'market_price' => '市场价',
            'sale_price' => '销售价',
            'cert_id' => '证书号',
            'product_size' => '成品尺寸',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'goods_remark' => '商品备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'main_stone' => '主石',
            'main_stone_cert_id' => '主石证书号',
            'main_stone_sn' => '主石编号',
            'main_stone_num' => '主石数量',
            'main_stone_weight' => '主石重',
            'main_stone_color' => '主石颜色',
            'main_stone_clarity' => '主石净度',
            'main_stone_price' => '主石买入单价',
            'second_cert_id1' => '副石证书号',
            'second_stone_sn1' => '副石1编号',
            'second_stone1' => '副石1类型',
            'second_stone_num1' => '副石1数量',
            'second_stone_weight1' => '副石1重量',
            'second_stone_price1' => '副石1买入单价',
            'second_stone2' => '副石2',
            'second_stone_num2' => '副石2数量',
            'second_stone_weight2' => '副石2重量',
            'second_stone_price2' => '副石2买入单价',
            'second_stone3' => '副石3',
            'second_stone_num3' => '副石3数量',
            'second_stone_weight3' => '副石3重量',
            'second_stone_price3' => '副石3买入单价',
            'markup_rate' => '加价率',
            'gong_fee' => '工费',
            'parts_weight' => '配件重量',
            'parts_price' => '配件金额',
            'parts_fee' => '配件工费',
            'xianqian_fee' => '镶嵌工费',
            'biaomiangongyi' => '表面工艺',
            'biaomiangongyi_fee' => '表面工艺工费',
            'fense_fee' => '分色工艺工费',
            'bukou_fee' => '补口工费',
            'cert_fee' => '证书费',
            'extra_stone_fee' => '超石费',
            'tax_fee' => '税费',
            'other_fee' => '其他费用',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联采购收货单明细表
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt(){
        return $this->hasOne(PurchaseReceipt::class, ['id'=>'receipt_id'])->alias('receipt');
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
     * 关联质检未过原因
     * @return \yii\db\ActiveQuery
     */
    public function getFqc()
    {
        return $this->hasOne(PurchaseFqcConfig::class, ['id'=>'iqc_reason'])->alias('fqc');
    }
}
