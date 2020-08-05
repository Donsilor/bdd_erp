<?php

namespace addons\Warehouse\common\models;

use Faker\Provider\Base;
use Yii;

/**
 * This is the model class for table "warehouse_bill_goods".
 *
 * @property int $id
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property int $goods_id 货号
 * @property string $goods_name 商品名称
 * @property string $style_sn 款号
 * @property int $goods_num 商品数量
 * @property int $order_detail_id 订单商品明细ID
 * @property int $source_detail_id 来源明细ID
 * @property int $put_in_type 入库方式
 * @property int $warehouse_id 仓库ID
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property double $gold_weight 金重
 * @property double $gold_loss 金损
 * @property double $diamond_carat 钻石大小
 * @property string $diamond_color 钻石颜色
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cert_id 证书号
 * @property string $diamond_cert_type 证书类型
 * @property string $cost_price 成本价
 * @property string $sale_price 销售价
 * @property string $market_price 市场价
 * @property double $markup_rate 加价率
 * @property string $goods_remark 商品备注
 * @property int $status 单据明细状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBillGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bill_id', 'goods_num', 'order_detail_id', 'source_detail_id', 'put_in_type', 'warehouse_id','from_warehouse_id','to_warehouse_id', 'status','creator_id', 'created_at', 'updated_at'], 'integer'],
            [['bill_no', 'bill_type', 'goods_name', 'style_sn'], 'required'],
            [['gold_weight', 'gold_loss', 'diamond_carat', 'cost_price', 'sale_price', 'market_price', 'markup_rate'], 'number'],
            [['bill_no', 'goods_id', 'style_sn', 'diamond_cert_id'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['goods_name'], 'string', 'max' => 160],
            [['material', 'material_type', 'material_color','diamond_color', 'diamond_clarity', 'diamond_cert_type'], 'string', 'max' => 10],
            [['goods_remark'], 'string', 'max' => 255],
            [['bill_id','goods_id'], 'unique','targetAttribute'=>['bill_id','goods_id']],
            [['returned_time', 'supplier_id', 'from_warehouse_id', 'to_warehouse_id', 'creator_id', 'auditor_id'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_id' => '单据ID',
            'bill_no' => '单据编号',
            'bill_type' => '单据类型',
            'goods_id' => '货号',
            'goods_name' => '商品名称',
            'style_sn' => '款号',
            'goods_num' => '商品数量',
            'order_detail_id' => '订单商品明细',
            'source_detail_id' => '来源明细ID',
            'put_in_type' => '入库方式',
            'warehouse_id' => '仓库',
            'from_warehouse_id' => '来源仓库',
            'to_warehouse_id' => '目标仓库',
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'gold_weight' => '金重',
            'gold_loss' => '金损',
            'diamond_carat' => '钻石大小',
            'diamond_color' => '钻石颜色',
            'diamond_clarity' => '钻石净度',
            'diamond_cert_id' => '证书号',
            'diamond_cert_type' => '证书类型',
            'cost_price' => '成本价',
            'sale_price' => '销售价',
            'market_price' => '市场价',
            'markup_rate' => '加价率',
            'goods_remark' => '商品备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
     * 入库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getToWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'to_warehouse_id'])->alias('toWarehouse');
    }
    /**
     * 出库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getFromWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'from_warehouse_id'])->alias('fromWarehouse');
    }
    /**
     * 单据
     * @return \yii\db\ActiveQuery
     */
    public function getBill(){
        return $this->hasOne(WarehouseBill::class, ['id'=>'bill_id'])->alias('bill');
    }
    /**
     * 库存 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(WarehouseGoods::class, ['goods_id'=>'goods_id'])->alias('goods');
    }
    /**
     * 盘点单附属表
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsW()
    {
        return $this->hasOne(WarehouseBillGoodsW::class, ['id'=>'id'])->alias('goodsW');
    }
    /**
     * 借货单附属表
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsJ()
    {
        return $this->hasOne(WarehouseBillGoodsJ::class, ['id'=>'id'])->alias('goodsJ');
    }
}
