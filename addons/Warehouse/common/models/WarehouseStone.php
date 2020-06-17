<?php

namespace addons\Warehouse\common\models;

use Yii;
use addons\Supply\common\models\Supplier;

/**
 * This is the model class for table "warehouse_stone".
 *
 * @property int $id ID
 * @property string $stone_sn 石料编号
 * @property string $stone_name 石料名称
 * @property string $stone_type 石料类型
 * @property int $stone_status 石料状态
 * @property int $supplier_id 供应商
 * @property string $style_sn 石料款号
 * @property int $stock_cnt 库存数量
 * @property string $stock_weight 库存重量(ct)
 * @property string $stone_color 颜色
 * @property string $stone_clarity 净度
 * @property string $stone_cut 切工
 * @property string $stone_symmetry 对称
 * @property string $stone_polish 抛光
 * @property string $stone_fluorescence 荧光
 * @property int $fenbaoru_cnt 分包转入数量
 * @property string $fenbaoru_weight 分包转入重量(ct)
 * @property int $fenbaochu_cnt 分包转出数量
 * @property string $fenbaochu_weight 分包转出重量(ct)
 * @property int $ms_cnt 买入数量
 * @property string $ms_weight 买入重量(ct)
 * @property int $ss_cnt 送出数量
 * @property string $ss_weight 送出重量(ct)
 * @property int $hs_cnt 还回数量-镶嵌
 * @property string $hs_weight 还回重量-镶嵌(ct)
 * @property int $ts_cnt 退石数量
 * @property string $ts_weight 退石重量(ct)
 * @property int $ys_cnt 遗失数量
 * @property string $ys_weight 遗失重量(ct)
 * @property int $sy_cnt 损坏数量
 * @property string $sy_weight 损坏重量(ct)
 * @property int $th_cnt 退货数
 * @property string $th_weight 退货重(ct)
 * @property int $rk_cnt 其他入库数量
 * @property string $rk_weight 其他入库重量(ct)
 * @property int $ck_cnt 其他出库数量
 * @property string $ck_weight 其他出库重量(ct)
 * @property string $cost_price 石料总额
 * @property string $stone_price 石料单价(ct)
 * @property string $sale_price 销售价
 * @property int $warehouse_id 所在仓库
 * @property string $remark 备注
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
            [['stone_sn', 'stone_name', 'stone_type'], 'required'],
            [['supplier_id', 'stock_cnt', 'fenbaoru_cnt', 'fenbaochu_cnt', 'ms_cnt', 'ss_cnt', 'hs_cnt', 'ts_cnt', 'ys_cnt', 'sy_cnt', 'th_cnt', 'rk_cnt', 'ck_cnt', 'warehouse_id', 'stone_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['stock_weight', 'fenbaoru_weight', 'fenbaochu_weight', 'ms_weight', 'ss_weight', 'hs_weight', 'ts_weight', 'ys_weight', 'sy_weight', 'th_weight', 'rk_weight', 'ck_weight', 'cost_price', 'stone_price', 'sale_price'], 'number'],
            [['stone_sn', 'stone_name', 'style_sn'], 'string', 'max' => 30],
            [['stone_type', 'stone_color', 'stone_clarity', 'stone_cut', 'stone_symmetry', 'stone_polish', 'stone_fluorescence'], 'string', 'max' => 10],
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
            'stone_sn' => '石料编号',
            'stone_name' => '石料名称',
            'stone_type' => '石料类型',
            'stone_status' => '石料状态',
            'supplier_id' => '供应商',
            'style_sn' => '石料款号',
            'stock_cnt' => '库存数量',
            'stock_weight' => '库存重量(ct)',
            'stone_color' => '颜色',
            'stone_clarity' => '净度',
            'stone_cut' => '切工',
            'stone_symmetry' => '对称',
            'stone_polish' => '抛光',
            'stone_fluorescence' => '荧光',
            'fenbaoru_cnt' => '分包转入数量',
            'fenbaoru_weight' => '分包转入重量(ct)',
            'fenbaochu_cnt' => '分包转出数量',
            'fenbaochu_weight' => '分包转出重量(ct)',
            'ms_cnt' => '买入数量',
            'ms_weight' => '买入重量(ct)',
            'ss_cnt' => '送出数量',
            'ss_weight' => '送出重量(ct)',
            'hs_cnt' => '还回数量-镶嵌',
            'hs_weight' => '还回重量-镶嵌(ct)',
            'ts_cnt' => '退石数量',
            'ts_weight' => '退石重量(ct)',
            'ys_cnt' => '遗失数量',
            'ys_weight' => '遗失重量(ct)',
            'sy_cnt' => '损坏数量',
            'sy_weight' => '损坏重量(ct)',
            'th_cnt' => '退货数',
            'th_weight' => '退货重(ct)',
            'rk_cnt' => '其他入库数量',
            'rk_weight' => '其他入库重量(ct)',
            'ck_cnt' => '其他出库数量',
            'ck_weight' => '其他出库重量(ct)',
            'cost_price' => '石料总额',
            'stone_price' => '石料单价(ct)',
            'sale_price' => '销售价',
            'warehouse_id' => '所在仓库',
            'remark' => '备注',
            'status' => '状态 1启用 0禁用 -1删除',
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
