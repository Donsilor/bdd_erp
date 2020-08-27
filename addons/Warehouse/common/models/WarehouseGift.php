<?php

namespace addons\Warehouse\common\models;

use Yii;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\models\Supplier;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_gift".
 *
 * @property int $id
 * @property string $gift_sn 批次号
 * @property string $style_sn 赠品款号
 * @property string $gift_name 赠品名称
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $chain_length 链长
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石数量
 * @property string $gift_size 赠品尺寸
 * @property int $gift_num 赠品库存
 * @property int $first_num 原库存
 * @property string $gift_weight 赠品重量(g)
 * @property string $gold_price 金价/g
 * @property string $cost_price 金价总额
 * @property string $sale_price 销售价
 * @property int $supplier_id 供应商
 * @property string $purchase_sn 采购单号
 * @property string $receipt_no 收货单号
 * @property int $source_detail_id 商品来源ID
 * @property int $put_in_type 入库方式
 * @property int $warehouse_id 所在仓库
 * @property int $gift_status 赠品状态
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseGift extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_gift');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gift_sn', 'style_sn'], 'required'],
            [['product_type_id', 'style_cate_id', 'style_sex', 'main_stone_num', 'gift_num', 'first_num', 'supplier_id', 'put_in_type', 'warehouse_id', 'source_detail_id', 'gift_status', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gift_weight', 'gold_price', 'cost_price', 'sale_price'], 'number'],
            [['gift_sn', 'style_sn', 'purchase_sn', 'receipt_no'], 'string', 'max' => 30],
            [['gift_name', 'chain_length'], 'string', 'max' => 100],
            [['material_type', 'material_color', 'finger', 'finger_hk', 'main_stone_type', 'gift_size'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gift_sn' => '批次号',
            'style_sn' => '赠品款号',
            'gift_name' => '赠品名称',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'chain_length' => '链长(cm)',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石数量',
            'gift_size' => '赠品尺寸(mm)',
            'gift_num' => '库存数量',
            'gift_weight' => '赠品重量(g)',
            'first_num' => '原库存数量',
            'gold_price' => '金价/g',
            'cost_price' => '成本价',
            'sale_price' => '销售价',
            'supplier_id' => '供应商',
            'put_in_type' => '入库方式',
            'purchase_sn' => '采购单编号',
            'receipt_no' => '收货单编号',
            'source_detail_id' => '商品来源ID',
            'warehouse_id' => '所在仓库',
            'gift_status' => '赠品状态',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人(入库人)',
            'created_at' => '创建时间(入库时间)',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * 供应商 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
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
     * 仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id'])->alias('warehouse');
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
