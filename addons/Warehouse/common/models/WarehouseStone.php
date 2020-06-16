<?php

namespace addons\Warehouse\common\models;

use Yii;
use addons\Supply\common\models\Supplier;

/**
 * This is the model class for table "warehouse_stone".
 *
 * @property int $id
 * @property string $stone_sn 石包编号
 * @property string $stone_name 石包名称
 * @property string $style_sn 石料款号
 * @property string $stone_type 石料类型
 * @property string $supplier_id 供应商
 * @property string $stone_color 石料颜色
 * @property string $stone_clarity 石料净度
 * @property int $stock_cnt 库存数量
 * @property string $stock_weight 库存重量
 * @property int $fenbaoru_cnt 分包转入数量
 * @property string $fenbaoru_weight 分包转入重量
 * @property int $fenbaochu_cnt 分包转出数量
 * @property string $fenbaochu_weight 分包转出重量
 * @property int $ms_cnt 买入数量
 * @property string $ms_weight 买入重量
 * @property int $ss_cnt 送出数量
 * @property string $ss_weight 送出重量
 * @property int $hs_cnt 还回数量-镶嵌
 * @property string $hs_weight 还回重量-镶嵌
 * @property int $ts_cnt 退石数量
 * @property string $ts_weight 退石重量
 * @property int $ys_cnt 遗失数量
 * @property string $ys_weight 遗失重量
 * @property int $sy_cnt 损坏数量
 * @property string $sy_weight 损坏重量
 * @property int $th_cnt 退货数
 * @property string $th_weight 退货重
 * @property int $rk_cnt 其他入库数量
 * @property string $rk_weight 其他入库重量
 * @property int $ck_cnt 其他出库数量
 * @property string $ck_weight 其他出库重量
 * @property string $cost_price 成本价/克拉
 * @property string $sale_price 销售价格/克拉
 * @property string $remark 石包备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseStone extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_stone');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stone_name'], 'required'],
            [['id', 'stock_cnt', 'supplier_id', 'fenbaoru_cnt', 'fenbaochu_cnt', 'ms_cnt', 'ss_cnt', 'hs_cnt', 'ts_cnt', 'ys_cnt', 'sy_cnt', 'th_cnt', 'rk_cnt', 'ck_cnt', 'status', 'created_at', 'updated_at'], 'integer'],
            [['stock_weight', 'fenbaoru_weight', 'fenbaochu_weight', 'ms_weight', 'ss_weight', 'hs_weight', 'ts_weight', 'ys_weight', 'sy_weight', 'th_weight', 'rk_weight', 'ck_weight', 'cost_price', 'stone_price', 'sale_price'], 'number'],
            [['stone_sn', 'stone_name', 'style_sn'], 'string', 'max' => 30],
            [['stone_type', 'stone_color', 'stone_clarity'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
            [['stone_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stone_sn' => '石料编号',
            'stone_name' => '石料名称',
            'style_sn' => '石料款号',
            'stone_type' => '石料类型',
            'supplier_id' => '供应商',
            'stock_cnt' => '库存数量',
            'stock_weight' => '库存重量(ct)',
            'cost_price' => '石料总额',
            'stone_price' => '石料单价(ct)',
            'sale_price' => '销售价格(ct)',
            'stone_color' => '颜色',
            'stone_clarity' => '净度',
            'stone_cut' => '切工',
            'stone_symmetry' => '对称',
            'stone_polish' => '抛光',
            'stone_fluorescence' => '荧光',
            'fenbaoru_cnt' => '分包转入数量',
            'fenbaoru_weight' => '分包转入重量',
            'fenbaochu_cnt' => '分包转出数量',
            'fenbaochu_weight' => '分包转出重量',
            'ms_cnt' => '买入数量',
            'ms_weight' => '买入重量',
            'ss_cnt' => '送出数量',
            'ss_weight' => '送出重量',
            'hs_cnt' => '还回数量-镶嵌',
            'hs_weight' => '还回重量-镶嵌',
            'ts_cnt' => '退石数量',
            'ts_weight' => '退石重量',
            'ys_cnt' => '遗失数量',
            'ys_weight' => '遗失重量',
            'sy_cnt' => '损坏数量',
            'sy_weight' => '损坏重量',
            'th_cnt' => '退货数',
            'th_weight' => '退货重',
            'rk_cnt' => '其他入库数量',
            'rk_weight' => '其他入库重量',
            'ck_cnt' => '其他出库数量',
            'ck_weight' => '其他出库重量',
            'remark' => '石包备注',
            'status' => '状态',
            'created_at' => '创建时间',
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
}
