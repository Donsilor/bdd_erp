<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_stone".
 *
 * @property int $id
 * @property string $shibao
 * @property int $kucun_cnt 库存数量
 * @property int $MS_cnt 买入数量
 * @property int $fenbaoru_cnt 分包转入数量
 * @property int $SS_cnt 送出数量
 * @property int $fenbaochu_cnt 分包转出数量
 * @property int $HS_cnt 还回数量-镶嵌
 * @property int $TS_cnt 退石数量
 * @property int $YS_cnt 遗失数量
 * @property int $SY_cnt 损坏数量
 * @property int $TH_cnt 退货数
 * @property int $RK_cnt 其他入库数量
 * @property int $CK_cnt 其他出库数量
 * @property string $kucun_zhong 库存重量
 * @property string $MS_zhong 买入重量
 * @property string $fenbaoru_zhong 分包转入重量
 * @property string $SS_zhong 送出重量
 * @property string $fenbaochu_zhong 分包转出重量
 * @property string $HS_zhong 还回重量-镶嵌
 * @property string $TS_zhong 退石重量
 * @property string $YS_zhong 遗失重量
 * @property string $SY_zhong 损坏重量
 * @property string $TH_zhong 退货重
 * @property string $RK_zhong 其他入库重量
 * @property string $CK_zhong 其他出库重量
 * @property string $cost_price 原始采购成本
 * @property string $purchase_price 每卡采购价格
 * @property string $sale_price 每卡销售价格
 * @property string $remark 备注
 * @property int $sort 排序
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
            [['shibao'], 'required'],
            [['id','kucun_cnt', 'MS_cnt', 'fenbaoru_cnt', 'SS_cnt', 'fenbaochu_cnt', 'HS_cnt', 'TS_cnt', 'YS_cnt', 'SY_cnt', 'TH_cnt', 'RK_cnt', 'CK_cnt', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['kucun_zhong', 'MS_zhong', 'fenbaoru_zhong', 'SS_zhong', 'fenbaochu_zhong', 'HS_zhong', 'TS_zhong', 'YS_zhong', 'SY_zhong', 'TH_zhong', 'RK_zhong', 'CK_zhong', 'cost_price', 'purchase_price', 'sale_price'], 'number'],
            [['shibao'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
            [['shibao'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shibao' => '名称',
            'kucun_cnt' => '库存数量',
            'MS_cnt' => '买入数量',
            'fenbaoru_cnt' => '分包转入数量',
            'SS_cnt' => '送出数量',
            'fenbaochu_cnt' => '分包转出数量',
            'HS_cnt' => '还回数量-镶嵌',
            'TS_cnt' => '退石数量',
            'YS_cnt' => '遗失数量',
            'SY_cnt' => '损坏数量',
            'TH_cnt' => '退货数',
            'RK_cnt' => '其他入库数量',
            'CK_cnt' => '其他出库数量',
            'kucun_zhong' => '库存重量',
            'MS_zhong' => '买入重量',
            'fenbaoru_zhong' => '分包转入重量',
            'SS_zhong' => '送出重量',
            'fenbaochu_zhong' => '分包转出重量',
            'HS_zhong' => '还回重量-镶嵌',
            'TS_zhong' => '退石重量',
            'YS_zhong' => '遗失重量',
            'SY_zhong' => '损坏重量',
            'TH_zhong' => '退货重',
            'RK_zhong' => '其他入库重量',
            'CK_zhong' => '其他出库重量',
            'cost_price' => '原始采购成本',
            'purchase_price' => '每卡采购价格',
            'sale_price' => '每卡销售价格',
            'remark' => '备注',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
