<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_stone_bill_detail".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property int $source_detail_id 来源明细ID
 * @property string $bill_type 单据类型
 * @property string $shibao 石包名称
 * @property string $cert_id 证书号
 * @property string $carat 石重
 * @property string $color 颜色
 * @property string $clarity 净度
 * @property string $cut 切工
 * @property string $polish 抛光
 * @property string $fluorescence 荧光
 * @property string $symmetry 对称
 * @property int $stone_num 石包总数
 * @property string $stone_weight 石包总重量
 * @property string $purchase_price 每卡采购价格
 * @property string $sale_price 每卡销售价格
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseStoneBillDetail extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_stone_bill_detail');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'bill_type', 'shibao'], 'required'],
            [['bill_id', 'source_detail_id', 'stone_num', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['stone_weight', 'purchase_price', 'sale_price'], 'number'],
            [['bill_type'], 'string', 'max' => 10],
            [['shibao'], 'string', 'max' => 30],
            [['cert_id', 'color', 'clarity', 'cut', 'polish', 'fluorescence', 'symmetry'], 'string', 'max' => 20],
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
            'bill_type' => '单据类型',
            'source_detail_id' => '来源明细ID',
            'shibao' => '石包名称',
            'cert_id' => '证书号',
            'carat' => '石重',
            'color' => '颜色',
            'clarity' => '净度',
            'cut' => '切工',
            'polish' => '抛光',
            'fluorescence' => '荧光',
            'symmetry' => '对称',
            'stone_num' => '石包总数',
            'stone_weight' => '石包总重量',
            'purchase_price' => '每卡采购价格',
            'sale_price' => '每卡销售价格',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
