<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_moissanite".
 *
 * @property int $id ID
 * @property string $name 名称
 * @property string $style_sn 款号
 * @property string $type 类型
 * @property string $shape 形状
 * @property string $size 尺寸(mm)
 * @property string $ref_carat 尺寸参考石重(ct)
 * @property string $real_carat 实际石重(ct)
 * @property int $karat_num 克拉数量
 * @property string $karat_price 克拉成本
 * @property string $est_cost 预估成本/ct
 * @property string $color_scope 颜色范围
 * @property string $clarity_scope 净度范围
 * @property string $remark 备注
 * @property int $sort 排序
 * @property int $status 状态
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Moissanite extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_moissanite');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ref_carat', 'real_carat', 'karat_price', 'est_cost'], 'number'],
            [['karat_num', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'style_sn', 'size', 'color_scope', 'clarity_scope'], 'string', 'max' => 30],
            [['type', 'shape'], 'string', 'max' => 10],
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
            'name' => '名称',
            'style_sn' => '款号',
            'type' => '类型',
            'shape' => '形状',
            'size' => '尺寸(mm)',
            'ref_carat' => '尺寸参考石重(ct)',
            'real_carat' => '实际石重(ct)',
            'karat_num' => '克拉数量',
            'karat_price' => '克拉成本',
            'est_cost' => '预估成本/ct',
            'color_scope' => '颜色范围',
            'clarity_scope' => '净度范围',
            'remark' => '备注',
            'sort' => '排序',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
