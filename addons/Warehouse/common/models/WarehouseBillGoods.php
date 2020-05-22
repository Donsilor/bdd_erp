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
 * @property int $put_in_type 入库方式
 * @property int $warehouse_id 仓库ID
 * @property int $material 主成色
 * @property double $gold_weight 金重
 * @property double $gold_loss 金损
 * @property double $diamond_carat 钻石大小
 * @property string $diamond_color 钻石颜色
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cert_id 证书号
 * @property string $cost_price 成本价
 * @property string $sale_price 销售价
 * @property string $market_price 市场价（标签价）
 * @property int $pandian_status 盘点状态
 * @property string $box_sn 货品所在柜位号
 * @property string $pandian_box_sn 盘点柜位
 * @property string $pandian_user 盘点人
 * @property double $markup_rate 加价率
 * @property string $goods_remark 商品备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
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
            [['id', 'bill_id', 'goods_id', 'goods_num', 'order_detail_id', 'put_in_type', 'warehouse_id', 'material', 'pandian_status', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['bill_no', 'bill_type', 'goods_id', 'goods_name', 'style_sn'], 'required'],
            [['gold_weight', 'gold_loss', 'diamond_carat', 'cost_price', 'sale_price', 'market_price', 'markup_rate'], 'number'],
            [['bill_no', 'style_sn', 'diamond_cert_id', 'pandian_box_sn', 'pandian_user'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['goods_name'], 'string', 'max' => 160],
            [['diamond_color', 'diamond_clarity', 'box_sn'], 'string', 'max' => 10],
            [['goods_remark'], 'string', 'max' => 255],
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
            'order_detail_id' => '订单商品明细ID',
            'put_in_type' => '入库方式',
            'warehouse_id' => '仓库ID',
            'material' => '主成色',
            'gold_weight' => '金重',
            'gold_loss' => '金损',
            'diamond_carat' => '钻石大小',
            'diamond_color' => '钻石颜色',
            'diamond_clarity' => '钻石净度',
            'diamond_cert_id' => '证书号',
            'cost_price' => '成本价',
            'sale_price' => '销售价',
            'market_price' => '市场价（标签价）',
            'pandian_status' => '盘点状态',
            'box_sn' => '货品所在柜位号',
            'pandian_box_sn' => '盘点柜位',
            'pandian_user' => '盘点人',
            'markup_rate' => '加价率',
            'goods_remark' => '商品备注',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1 删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
