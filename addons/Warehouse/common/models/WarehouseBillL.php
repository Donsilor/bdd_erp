<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_l".
 *
 * @property int $id 单据ID
 * @property string $total_factory_cost 工厂成本总计
 * @property string $total_pure_gold 折足重总计
 * @property int $goods_type 商品类型
 * @property int $show_basic 基本信息
 * @property int $show_attr 属性信息
 * @property int $show_gold 金料信息
 * @property int $show_main_stone 主石信息
 * @property int $show_second_stone1 副石1信息
 * @property int $show_second_stone2 副石2信息
 * @property int $show_second_stone3 副石3信息
 * @property int $show_parts 配件信息
 * @property int $show_fee 工费信息
 * @property int $show_price 价格信息
 * @property int $show_all 全部信息
 */
class WarehouseBillL extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_l');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'goods_type', 'show_basic', 'show_attr', 'show_gold', 'show_main_stone', 'show_second_stone1', 'show_second_stone2', 'show_second_stone3', 'show_parts', 'show_fee', 'show_price', 'show_all'], 'integer'],
            [['total_factory_cost', 'total_pure_gold'], 'number'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '单据ID',
            'total_factory_cost' => '工厂成本总计',
            'total_pure_gold' => '折足重总计',
            'goods_type' => '商品类型',
            'show_all' => '全部信息',
            'show_basic' => '基本信息',
            'show_attr' => '属性信息',
            'show_gold' => '金料信息',
            'show_main_stone' => '主石信息',
            'show_second_stone1' => '副石1信息',
            'show_second_stone2' => '副石2信息',
            'show_second_stone3' => '副石3信息',
            'show_parts' => '配件信息',
            'show_fee' => '工费信息',
            'show_price' => '价格信息',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [];
    }
}
