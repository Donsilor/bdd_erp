<?php

namespace addons\Warehouse\common\models;

use addons\Sales\common\models\SaleChannel;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\StyleChannel;
use addons\Supply\common\models\Supplier;
use common\models\backend\Member;
use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "warehouse_goods".
 *
 * @property int $id
 * @property string $goods_id 库存货号
 * @property string $goods_name 商品名称
 * @property string $goods_image 商品图片
 * @property string $product_size 尺寸
 * @property string $style_sn 款号
 * @property string $qiban_sn 起版号
 * @property int $goods_source 库存来源  ①快捷入库 ②采购单采购③客订单采购④批量导入
 * @property int $qiban_type 起版类型
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_channel_id 所属渠道
 * @property int $style_sex 款式性别
 * @property int $goods_status 商品状态
 * @property int $supplier_id 供应商ID
 * @property int $put_in_type 入库方式
 * @property int $company_id 公司ID
 * @property int $warehouse_id 仓库
 * @property string $gold_weight 金重
 * @property string $suttle_weight 净重
 * @property string $gold_price 金价
 * @property string $gold_amount 金料额
 * @property string $gold_loss 金损
 * @property string $gross_weight 毛重（连石重）
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $order_detail_id 订单明细ID
 * @property string $order_sn 订单号
 * @property string $produce_sn 布产号
 * @property string $cert_type 证书类别
 * @property string $cert_id 证书号
 * @property int $goods_num 商品数量
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $diamond_carat 主石大小
 * @property string $diamond_color 主石颜色
 * @property string $diamond_shape 主石形状
 * @property string $diamond_clarity 主石净度
 * @property string $diamond_cut 主石切工
 * @property string $diamond_polish 主石抛光
 * @property string $diamond_symmetry 主石对称
 * @property string $diamond_fluorescence 主石荧光
 * @property string $diamond_discount 主石折扣
 * @property string $diamond_cert_type 主石证书类型
 * @property string $diamond_cert_id 主石证书号
 * @property int $jintuo_type 金托类型
 * @property string $market_price 市场价(标签价)
 * @property string $cost_price 成本价
 * @property string $gong_fee 工费
 * @property string $extra_stone_fee 超石费
 * @property string $bukou_fee 补口工费
 * @property string $tax_fee 税费
 * @property string $xianqian_fee 镶石费
 * @property string $cert_fee 证书费
 * @property string $markup_rate 加价率
 * @property string $fense_fee 分色/分件费
 * @property string $other_fee 其他费用
 * @property string $biaomiangongyi_fee 表面工艺工费
 * @property string $total_gong_fee 总工费
 * @property string $xiangkou 戒托镶口
 * @property string $length 长度
 * @property int $weixiu_status 维修状态
 * @property int $weixiu_warehouse_id 维修入库仓库id
 * @property string $parts_gold_weight 配件金重
 * @property string $parts_price 配件金额
 * @property string $parts_fee 配件工费
 * @property int $parts_num 配件数量
 * @property string $goods_color 货品外部颜色
 * @property string $main_stone_sn 主石编号
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石粒数
 * @property string $main_stone_price 主石买入单价
 * @property string $main_stone_colour 主石色彩
 * @property string $main_stone_size 主石规格
 * @property string $remark 商品备注
 * @property string $second_stone_sn1 副石1编号
 * @property string $second_cert_id1 副石1证书号
 * @property string $second_stone_type1 副石1类型
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重
 * @property string $second_stone_price1 副石1买入单价
 * @property string $second_stone_color1 副石1颜色
 * @property string $second_stone_clarity1 副石1净度
 * @property string $second_stone_shape1 副石1形状
 * @property string $second_stone_size1 副石1规格
 * @property string $second_stone_type2 副石2类型
 * @property int $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重
 * @property string $second_stone_shape2 副石2形状
 * @property string $second_stone_clarity2 副石2净度
 * @property string $second_stone_color2 副石2颜色
 * @property string $second_stone_price2 副石2总计价
 * @property string $second_stone_size2 副石2规格
 * @property string $second_stone_type3 副石3类型
 * @property int $second_stone_num3 副石3数量
 * @property string $second_stone_weight3 副石3重量(ct)
 * @property string $second_stone_price3 副石3买入单价
 * @property int $apply_id 当前编辑人
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $kezi 刻字
 * @property string $factory_mo 模号
 * @property string $factory_cost 工厂成本
 * @property int $is_inlay 是否镶嵌
 * @property string $chain_long 链长(mm)
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $talon_head_type 爪头形状
 * @property string $xiangqian_craft 镶嵌工艺
 * @property string $biaomiangongyi 表面工艺
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_type_id','style_sex' ,'style_cate_id', 'style_channel_id','goods_status', 'supplier_id', 'put_in_type','qiban_type', 'company_id', 'warehouse_id', 'goods_num', 'jintuo_type', 'weixiu_status', 'weixiu_warehouse_id', 'parts_num', 'main_stone_type',
                'main_stone_num', 'second_stone_num1', 'second_stone_num2','second_stone_num3', 'creator_id','apply_id','auditor_id','audit_time','audit_status', 'created_at', 'updated_at','is_inlay','goods_source','main_peishi_type','peiliao_type','peijian_type',
                'peijian_cate','second_peishi_type1','second_peishi_type2','parts_num','sales_time','peiliao_way','peijian_way','main_peishi_way','second_peishi_way1','second_peishi_way2'], 'integer'],
            [['goods_id','warehouse_id', 'jintuo_type'], 'required'],
            [['gold_weight','suttle_weight', 'gold_loss', 'diamond_carat', 'market_price','cost_price','outbound_cost', 'factory_cost', 'xiangkou', 'bukou_fee','gong_fee','biaomiangongyi_fee','parts_gold_weight','main_stone_price', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2',
                'second_stone_price2','second_stone_weight3','second_stone_price3' ,'gold_price','gold_amount','markup_rate','parts_fee','fense_fee','cert_fee','extra_stone_fee','tax_fee','other_fee','total_gong_fee','parts_price','xianqian_price','peishi_fee','peishi_amount','penrasa_fee',
                'edition_fee','parts_amount','ke_gong_fee','main_stone_cost','second_stone1_cost','second_stone2_cost'], 'number'],
            [['goods_name', 'cert_id', 'length','kezi', 'main_stone_size','second_stone_size1','goods_color'], 'string', 'max' => 100],
            [['style_sn','goods_id','qiban_sn'], 'string', 'max' => 30],
            [['gross_weight', 'produce_sn', 'diamond_cert_id','second_cert_id1','second_stone_sn1','main_stone_sn','parts_material'], 'string', 'max' => 20],
            [['finger','finger_hk','order_detail_id', 'material', 'material_type', 'material_color', 'diamond_clarity','diamond_shape','diamond_color', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_discount', 'diamond_cert_type', 'second_stone_type1',
                'second_stone_color1', 'second_stone_clarity1', 'second_stone_shape1', 'second_stone_type2','second_stone_sn2','chain_type','cramp_ring','talon_head_type','xiangqian_craft' ,'main_stone_colour','second_stone_shape2','second_stone_color2','second_stone_clarity2',
                'second_stone_type3','biaomiangongyi','second_stone_colour1','second_stone_colour2'], 'string', 'max' => 10],
            [['order_sn'], 'string', 'max' => 40],
            [['cert_type','factory_mo','chain_long'], 'string', 'max' => 50],
            [['audit_remark','remark','shiliao_remark'], 'string', 'max' => 255],
            [['goods_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '条码号',
            'goods_name' => '商品名称',
            'style_sn' => '款号',
            'qiban_sn' => '起版号',
            'factory_mo' => '模号',
            'goods_image' => '商品图片',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'qiban_type' => '起版类型',
            'style_channel_id' => '所属渠道',
            'goods_status' => '商品状态',
            'supplier_id' => '供应商',
            'goods_source' => '库存来源',
            'put_in_type' => '入库方式',
            'company_id' => '公司',
            'warehouse_id' => '仓库',
            'gold_weight' => '金重(g)',
            'suttle_weight' => '连石重(g)',
            'gold_loss' => '损耗(%)',
            'gold_price' => '金价',
            'gold_amount' => '金料额',
            'gross_weight' => '含耗重(g)',
            'finger' => '手寸(美)',
            'finger_hk' => '手寸(港)',
            'product_size' => '成品尺寸',
            'order_detail_id' => 'Order Detail ID',
            'order_sn' => '订单号',
            'produce_sn' => '布产号',
            'cert_type' => '证书类别',
            'cert_id' => '证书号',
            'goods_num' => '商品数量',
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'diamond_carat' => '主石大小',
            'diamond_clarity' => '主石净度',
            'diamond_cut' => '主石切工',
            'diamond_shape' => '主石形状',
            'diamond_color' => '主石颜色',
            'diamond_polish' => '主石抛光',
            'diamond_symmetry' => '主石对称',
            'diamond_fluorescence' => '主石荧光',
            'diamond_discount' => '主石折扣',
            'diamond_cert_type' => '主石证书类型',
            'diamond_cert_id' => '主石证书号',
            'jintuo_type' => '金托类型',
            'market_price' => '市场价(标签价)',
            'cost_price' => '成本价',
            'outbound_cost' => '出库成本',
            'xiangkou' => '戒托镶口',
            'factory_cost' => '工厂成本',
            'bukou_fee' => '补口费',
            'tax_fee' => '税费',
            'other_fee' => '其他费用',
            'biaomiangongyi_fee' => '表面工艺费',
            'xianqian_fee' => '镶石费',
            'extra_stone_fee' => '超石费',
            'gong_fee' => '工费',
            'cert_fee' => '证书费',
            'fense_fee' => '分色费',
            'parts_fee' => '配件工费',
            'total_gong_fee' => '总工费',
            'length' => '尺寸',
            'weixiu_status' => '维修状态',
            'weixiu_warehouse_id' => '维修入库仓库',
            'parts_gold_weight' => '配件金重',
            'parts_num' => '配件数量',
            'main_stone_sn' => '主石编号',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石粒数',
            'main_stone_price' => '主石单价/ct',
            'main_stone_colour' => '主石色彩',
            'main_stone_size' => '主石规格',
            'second_cert_id1' => '副石1证书号',
            'second_stone_sn1' => '副石1编号',
            'second_stone_type1' => '副石1类型',
            'second_stone_num1' => '副石1粒数',
            'second_stone_weight1' => '副石1重',
            'second_stone_price1' => '副石1单价',
            'second_stone_color1' => '副石1颜色',
            'second_stone_clarity1' => '副石1净度',
            'second_stone_shape1' => '副石1形状',
            'second_stone_size1' => '副石1规格',
            'second_stone_sn2' => '副石2编号',
            'second_stone_type2' => '副石2类型',
            'second_stone_num2' => '副石2粒数',
            'second_stone_weight2' => '副石2重',
            'second_stone_shape2' => '副石2形状',
            'second_stone_color2' => '副石2颜色',
            'second_stone_clarity2' => '副石2净度',
            'second_stone_price2' => '副石2单价',
            'second_stone_size2' => '副石2规格',
            'second_stone_type3' => '副石3类型',
            'second_stone_num3' => '副石3数量',
            'second_stone_weight3' => '副石3重量(ct)',
            'second_stone_price3' => '副石3买入单价',
            'is_inlay' => '是否镶嵌',
            'chain_long' => '链长(mm)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'biaomiangongyi' => '表面工艺',
            'talon_head_type' => '爪头形状',
            'goods_color' => '货品外部颜色',
            'xiangqian_craft' => '镶嵌工艺',
            'markup_rate' => '加价率',
            'remark' => '商品备注',
            'apply_id' => '当前申请人',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'kezi' => '刻字',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'main_peishi_type' => '主石配石类型',
            'second_peishi_type1' => '副石1配石类型',
            'second_peishi_type2' => '副石2配石类型',
            'second_stone_colour1' => '副石1色彩',
            'second_stone_colour2' => '副石2色彩',
            'peiliao_type' => '配料类型',
            'peijian_type' => '配件类型',
            'shiliao_remark' => '石料备注',
            'peijian_cate' => '配件分类',
            'parts_material' => '配件材质',
            'parts_amount' => '配件额',
            'parts_price' => '配件金价',
            'xianqian_price' => '镶石单价/颗',
            'peishi_fee' => '配石工费/ct',
            'peishi_amount' => '配石费',
            'penrasa_fee' => '喷拉沙费',
            'edition_fee' => '版费',
            'sales_time' => '销售时间',
            'ke_gong_fee' => '克/工费',
            'peiliao_way' => '配料方式',
            'peijian_way' => '配件方式',
            'main_peishi_way' => '主石配石方式',
            'second_peishi_way1' => '副石1配石方式',
            'second_peishi_way2' => '副石2配石方式',
            'main_stone_cost' => '主石成本',
            'second_stone1_cost' => '副石1成本',
            'second_stone2_cost' => '副石2成本',
        ];
    }


    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias('productType');
    }

    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id']);
    }

    /**
     * 款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
    }
    /**
     * 关联供应商一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id']);
    }

    /**
     * 关联仓库一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id']);
    }

    /**
     * 关联维修仓库一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWeixiuWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'weixiu_warehouse_id'])->alias('weixiuWarehouse');
    }

    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id']);
    }

    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id']);
    }
    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getApply()
    {
        return $this->hasOne(Member::class, ['id'=>'apply_id'])->alias('apply');
    }
}
