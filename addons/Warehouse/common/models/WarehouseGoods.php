<?php

namespace addons\Warehouse\common\models;

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
 * @property string $style_sn 款号
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property int $goods_status 商品状态
 * @property int $supplier_id 供应商ID
 * @property int $put_in_type 入库方式
 * @property int $company_id 公司ID
 * @property int $warehouse_id 仓库
 * @property string $gold_weight 金重
 * @property string $gold_loss 金损
 * @property string $gross_weight 毛重
 * @property string $finger 手寸
 * @property string $order_detail_id
 * @property string $order_sn 订单号
 * @property string $produce_sn 布产号
 * @property string $cert_type 证书类别
 * @property string $cert_id 证书号
 * @property int $goods_num 商品数量
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $diamond_carat 钻石大小
 * @property string $diamond_shape 钻石形状
 * @property string $diamond_color 钻石颜色
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cut 切工
 * @property string $diamond_polish 钻石抛光
 * @property string $diamond_symmetry 钻石对称
 * @property string $diamond_fluorescence 钻石荧光
 * @property string $diamond_discount 钻石折扣
 * @property string $diamond_cert_type 钻石证书类型
 * @property string $diamond_cert_id 钻石证书号
 * @property int $jintuo_type 金托类型
 * @property string $market_price 市场价(标签价)
 * @property string $cost_price 成本价(标签价)
 * @property string $xiangkou 戒托镶口
 * @property string $length 长度
 * @property int $weixiu_status 维修状态
 * @property int $weixiu_warehouse_id 维修入库仓库id
 * @property string $parts_gold_weight 配件金重
 * @property int $parts_num 配件数量
 * @property int $main_stone_type 主石类型
 * @property int $main_stone_num 主石粒数
 * @property string $second_stone_type1 副石1类型
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重
 * @property string $second_stone_price1 副石1总计价
 * @property string $second_stone_color1
 * @property string $second_stone_clarity1
 * @property string $second_stone_shape1
 * @property string $second_stone_type2 副石2类型
 * @property int $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重
 * @property string $second_stone_price2 副石2总计价
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
            [['product_type_id','style_sex' ,'style_cate_id', 'style_channel_id','goods_status', 'supplier_id', 'put_in_type','qiban_type', 'company_id', 'warehouse_id', 'goods_num', 'jintuo_type', 'weixiu_status', 'weixiu_warehouse_id', 'parts_num', 'main_stone_type', 'main_stone_num', 'second_stone_num1', 'second_stone_num2', 'creator_id','apply_id','auditor_id','audit_time','audit_status', 'created_at', 'updated_at','is_inlay'], 'integer'],
            [['goods_id','company_id', 'warehouse_id', 'jintuo_type'], 'required'],
            [['gold_weight','suttle_weight', 'gold_loss', 'diamond_carat', 'market_price','cost_price', 'xiangkou', 'bukou_fee','gong_fee','biaomiangongyi_fee','parts_gold_weight','main_stone_price', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2', 'second_stone_price2','gold_price','gold_amount', 'chain_long'], 'number'],
            [['goods_name', 'cert_id', 'length','kezi'], 'string', 'max' => 100],
            [['style_sn','goods_id','qiban_sn'], 'string', 'max' => 30],
            [['gross_weight', 'produce_sn', 'diamond_cert_id','second_cert_id1','second_stone_sn1','main_stone_sn'], 'string', 'max' => 20],
            [['finger', 'order_detail_id', 'material', 'material_type', 'material_color', 'diamond_clarity','diamond_shape','diamond_color', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_discount', 'diamond_cert_type', 'second_stone_type1',
                'second_stone_color1', 'second_stone_clarity1', 'second_stone_shape1', 'second_stone_type2','chain_type','cramp_ring','talon_head_type','xiangqian_craft'], 'string', 'max' => 10],
            [['order_sn'], 'string', 'max' => 40],
            [['cert_type','factory_mo'], 'string', 'max' => 50],
            [['audit_remark','remark'], 'string', 'max' => 255],
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
            'goods_id' => '商品编码',
            'goods_name' => '商品名称',
            'style_sn' => '款式编号',
            'qiban_sn' => '起版编号',
            'factory_mo' => '模号',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'qiban_type' => '起版类型',
            'style_channel_id' => '所属渠道',
            'goods_status' => '商品状态',
            'supplier_id' => '供应商',
            'put_in_type' => '入库方式',
            'company_id' => '公司',
            'warehouse_id' => '仓库',
            'gold_weight' => '金重',
            'suttle_weight' => '净重',
            'gold_loss' => '金损',
            'gold_price' => '金价',
            'gold_amount' => '金料额',
            'gross_weight' => '总重',
            'finger' => '手寸(美)',
            'finger_hk' => '手寸(港)',
            'product_size' => '尺寸',
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
            'diamond_discount' => '钻石折扣',
            'diamond_cert_type' => '主石证书类型',
            'diamond_cert_id' => '主石证书号',
            'jintuo_type' => '金托类型',
            'market_price' => '市场价(标签价)',
            'cost_price' => '成本价',
            'xiangkou' => '戒托镶口',
            'bukou_fee' => '补口费',
            'biaomiangongyi_fee' => '工艺费',
            'xianqian_fee' => '镶石费',
            'gong_fee' => '工费',
            'length' => '长度',
            'weixiu_status' => '维修状态',
            'weixiu_warehouse_id' => '维修入库仓库',
            'parts_gold_weight' => '配件金重',
            'parts_num' => '配件数量',
            'main_stone_sn' => '主石编号',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石粒数',
            'main_stone_price' => '主石成本',
            'second_cert_id1' => '副石1证书号',
            'second_stone_sn1' => '副石1编号',
            'second_stone_type1' => '副石1类型',
            'second_stone_num1' => '副石1粒数',
            'second_stone_weight1' => '副石1重',
            'second_stone_price1' => '副石1总计价',
            'second_stone_color1' => '副石1颜色',
            'second_stone_clarity1' => '副石1净度',
            'second_stone_shape1' => '副石1形状',
            'second_stone_type2' => '副石2类型',
            'second_stone_num2' => '副石2粒数',
            'second_stone_weight2' => '副石2重',
            'second_stone_price2' => '副石2总计价',
            'is_inlay' => '是否镶嵌',
            'chain_long' => '链长(mm)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'talon_head_type' => '爪头形状',
            'xiangqian_craft' => '爪头形状',
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
        return $this->hasOne(StyleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
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
