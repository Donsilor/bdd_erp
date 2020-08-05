<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "warehouse_bill_goods_a".
 *
 * @property int $id
 * @property int $bill_id 单据ID
 * @property string $goods_id 货号
 * @property string $goods_name 商品名称
 * @property string $xiangkou 镶口
 * @property string $finger 手寸
 * @property string $product_size 尺寸
 * @property string $gold_weight 金重
 * @property string $suttle_weight 净重
 * @property string $gold_loss 金损
 * @property string $gold_price 金价
 * @property string $gold_amount 金料额
 * @property string $main_stone_sn 主石编号
 * @property int $main_stone_num 主石粒数
 * @property string $main_stone_type 主石类型
 * @property string $main_stone_price 主石成本
 * @property string $diamond_shape 砖石形状
 * @property string $diamond_carat 钻石大小
 * @property string $diamond_color 钻石颜色
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cut 钻石切工
 * @property string $diamond_polish 钻石抛光
 * @property string $diamond_symmetry 钻石对称
 * @property string $diamond_fluorescence 钻石荧光
 * @property string $diamond_cert_type 钻石证书类型
 * @property string $diamond_cert_id 钻石证书号
 * @property string $second_stone_sn1 副石1编号
 * @property string $second_stone_type1 副石1类型
 * @property string $second_stone_shape1 副石1形状
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重
 * @property string $second_stone_color1 副石1颜色
 * @property string $second_stone_clarity1 副石1净度
 * @property string $second_stone_price1 副石1总计价
 * @property string $second_stone_type2 副石2类型
 * @property string $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重
 * @property string $second_stone_price2
 * @property string $parts_gold_weight 配件金重
 * @property string $parts_price 配件金额
 * @property string $gong_fee 工费
 * @property string $bukou_fee 补口费
 * @property string $xianqian_fee 镶石费
 * @property string $cert_fee 证书费
 * @property string $biaomiangongyi_fee 表面工艺费
 * @property string $cost_price 成本价
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBillGoodsA extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_goods_a');
    }

    public function behaviors()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id'], 'required'],
            [['bill_id', 'main_stone_num', 'second_stone_num1','auditor_id', 'audit_status', 'audit_time',], 'integer'],
            [['xiangkou', 'gold_weight', 'suttle_weight', 'gold_loss', 'gold_price', 'gold_amount', 'main_stone_price', 'diamond_carat', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2', 'second_stone_price2', 'parts_gold_weight', 'parts_price', 'gong_fee', 'bukou_fee', 'xianqian_fee', 'cert_fee', 'biaomiangongyi_fee', 'cost_price'], 'number'],
            [['goods_id','style_sn'], 'string', 'max' => 30],
            [['goods_name', 'product_size'], 'string', 'max' => 100],
            [['finger', 'main_stone_type', 'diamond_shape', 'diamond_color', 'diamond_clarity', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_cert_type', 'second_stone_type1', 'second_stone_shape1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_type2', 'second_stone_num2'], 'safe'],
            [['main_stone_sn', 'diamond_cert_id', 'second_stone_sn1'], 'string', 'max' => 20],
            [['audit_remark'], 'string', 'max' => 255]

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bill_id' => Yii::t('app', '单据ID'),
            'goods_id' => Yii::t('app', '货号'),
            'style_sn' => Yii::t('app', '款号'),
            'goods_name' => Yii::t('app', '商品名称'),
            'xiangkou' => Yii::t('app', '镶口'),
            'finger' => Yii::t('app', '手寸'),
            'product_size' => Yii::t('app', '尺寸'),
            'gold_weight' => Yii::t('app', '金重'),
            'suttle_weight' => Yii::t('app', '净重'),
            'gold_loss' => Yii::t('app', '金损'),
            'gold_price' => Yii::t('app', '金价'),
            'gold_amount' => Yii::t('app', '金料额'),
            'main_stone_sn' => Yii::t('app', '主石编号'),
            'main_stone_num' => Yii::t('app', '主石粒数'),
            'main_stone_type' => Yii::t('app', '主石类型'),
            'main_stone_price' => Yii::t('app', '主石成本'),
            'diamond_shape' => Yii::t('app', '主石形状'),
            'diamond_carat' => Yii::t('app', '主石大小'),
            'diamond_color' => Yii::t('app', '主石颜色'),
            'diamond_clarity' => Yii::t('app', '主石净度'),
            'diamond_cut' => Yii::t('app', '主石切工'),
            'diamond_polish' => Yii::t('app', '主石抛光'),
            'diamond_symmetry' => Yii::t('app', '主石对称'),
            'diamond_fluorescence' => Yii::t('app', '主石荧光'),
            'diamond_cert_type' => Yii::t('app', '主石证书类型'),
            'diamond_cert_id' => Yii::t('app', '主石证书号'),
            'second_stone_sn1' => Yii::t('app', '副石1编号'),
            'second_stone_type1' => Yii::t('app', '副石1类型'),
            'second_stone_shape1' => Yii::t('app', '副石1形状'),
            'second_stone_num1' => Yii::t('app', '副石1粒数'),
            'second_stone_weight1' => Yii::t('app', '副石1重'),
            'second_stone_color1' => Yii::t('app', '副石1颜色'),
            'second_stone_clarity1' => Yii::t('app', '副石1净度'),
            'second_stone_price1' => Yii::t('app', '副石1总计价'),
            'second_stone_type2' => Yii::t('app', '副石2类型'),
            'second_stone_num2' => Yii::t('app', '副石2粒数'),
            'second_stone_weight2' => Yii::t('app', '副石2重'),
            'second_stone_price2' => Yii::t('app', '副石2总计价'),
            'parts_gold_weight' => Yii::t('app', '配件金重'),
            'parts_price' => Yii::t('app', '配件金额'),
            'gong_fee' => Yii::t('app', '工费'),
            'bukou_fee' => Yii::t('app', '补口费'),
            'xianqian_fee' => Yii::t('app', '镶石费'),
            'cert_fee' => Yii::t('app', '证书费'),
            'biaomiangongyi_fee' => Yii::t('app', '表面工艺费'),
            'cost_price' => Yii::t('app', '成本价'),
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
        ];
    }


    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(WarehouseGoods::class, ['goods_id'=>'goods_id'])->alias('goods');
    }
}
