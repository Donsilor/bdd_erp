<?php

namespace addons\Purchase\common\models;

use addons\Style\common\models\StyleChannel;
use Yii;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\models\Produce;

/**
 * This is the model class for table "purchase_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property string $goods_sn 款号/起版号
 * @property int $goods_type 商品类型 1款号 2起版号
 * @property string $goods_name 商品名称
 * @property string $goods_image 商品图片
 * @property int $style_id 款号/起版ID
 * @property string $style_sn 商品编号
 * @property string $qiban_sn
 * @property int $qiban_type 起版类型 0非起版 1有款起版 2无款起版
 * @property int $peiliao_type 配料类型
 * @property int $style_channel_id
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property int $jintuo_type 金托类型
 * @property int $is_inlay 是否镶嵌
 * @property string $cost_price 成本价
 * @property int $goods_num 商品数量
 * @property int $produce_id 布产ID
 * @property int $is_apply 是否申请修改
 * @property string $apply_info
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $main_stone_price 主石价格(元/克拉)
 * @property string $second_stone_price1 副石1单价
 * @property string $second_stone_price2 副石2单价
 * @property string $stone_info 石料信息
 * @property double $gold_loss 金损
 * @property string $gold_price 金价
 * @property string $gold_cost_price 单件银(金)额
 * @property string $gold_amount 金料额
 * @property string $parts_info 配件信息
 * @property string $jiagong_fee 加工费/件
 * @property string $xiangqian_fee 镶石费/件
 * @property string $gong_fee 工费总额/件(jiagong+xiangqian)
 * @property string $gaitu_fee 改图费
 * @property string $penla_fee 喷蜡费
 * @property string $unit_cost_price 单件额 工费+镶石费+单件银额
 * @property string $factory_cost_price 工厂成本价
 * @property string $product_size 成品尺寸
 * @property string $goods_color 货品外部颜色
 * @property double $single_stone_weight 单件连石重
 * @property string $company_unit_cost 公司单件成本
 * @property string $biaomiangongyi_fee
 * @property string $fense_fee
 * @property string $bukou_fee
 * @property string $cert_fee
 */
class PurchaseGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_name','purchase_id', 'jintuo_type','goods_num','cost_price','peiliao_type'], 'required'],

            [['purchase_id', 'style_id', 'qiban_type','peiliao_type', 'product_type_id','style_channel_id', 'style_cate_id', 'style_sex', 'jintuo_type', 'goods_num','is_inlay' ,'produce_id', 'is_apply', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cost_price', 'main_stone_price','single_stone_weight', 'second_stone_price1','second_stone_price2', 'gold_loss', 'gold_price', 'gold_cost_price', 'jiagong_fee', 'xiangqian_fee', 'gong_fee', 'gaitu_fee', 'penla_fee', 'unit_cost_price', 'factory_cost_price',
                'single_stone_weight','company_unit_cost','gold_amount','biaomiangongyi_fee','fense_fee','bukou_fee','cert_fee','parts_weight','parts_price','factory_total_price','company_total_price','parts_fee'], 'number'],
            [['apply_info'], 'string'],
            [['goods_name', 'remark', 'stone_info', 'parts_info'], 'string', 'max' => 255],
            [['goods_sn'], 'string', 'max' => 60],
            [['product_size','goods_color','goods_image'], 'string', 'max' => 100],
            [['style_sn', 'qiban_sn','factory_mo'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_id' => '采购单ID',
            'goods_type' => '商品类型',
            'goods_name' => '商品名称',
            'goods_sn' => '款号/起版号',
            'style_id' => '商品id',
            'style_sn' => '款式编号',
            'qiban_sn' => '起版编号',
            'qiban_type' => '起版类型',
            'peiliao_type' => '配料类型',
            'product_type_id' => '产品线',
            'style_channel_id' => '所属渠道',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'jintuo_type' => '金托类型',
            'cost_price' => '采购成本单价',
            'goods_num' => '商品数量',
            'is_inlay' => '是否镶嵌',
            'produce_id' => '布产ID',
            'is_apply' => '是否申请修改',
            'apply_info' => 'Apply Info',
            'status' => '状态',
            'remark' => '采购备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'main_stone_price' => '主石价格/ct',
            'second_stone_price1' => '副石1单价/ct',
            'second_stone_price2' => '副石2单价/ct',
            'stone_info' => '石料信息',
            'gold_loss' => '损耗/件',
            'gold_price' => '金价/g',
            'gold_cost_price' => '单件银额/件',
            'parts_info' => '配件信息',
            'jiagong_fee' => '金属加工费/件',
            'xiangqian_fee' => '镶石费/件',
            'gong_fee' => '工费总额/件',
            'parts_weight' => '配件重量',
            'parts_price' => '配件金额',
            'parts_fee' => '配件工费',
            'gaitu_fee' => '改图费/件',
            'penla_fee' => '喷蜡费/件',
            'unit_cost_price' => '单件额/件',
            'factory_cost_price' => '工厂成本价/件',
            'product_size' => '成品尺寸',
            'goods_color' => '货品外部颜色',
            'single_stone_weight' => '单件连石重',
            'company_unit_cost' => '公司单件成本',
            'gold_amount' => '金料额',
            'biaomiangongyi_fee' => '表面工艺工费',
            'fense_fee' => '分色工艺工费',
            'bukou_fee' => '补口工费',
            'cert_fee' => '证书费',
            'factory_mo' => '工厂模号',
            'goods_image' => '商品图片',
            'factory_total_price' => '工厂总成本价',
            'company_total_price' => '公司总成本价',
        ];
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
    /**
     * 采购单一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPurchase()
    {
        return $this->hasOne(Purchase::class, ['id'=>'purchase_id'])->alias('purchase');
    }
    /**
     * 布产单 一对一
     * @return \yii\db\ActiveQuery
    */
    public function getProduce()
    {
        return $this->hasOne(Produce::class, ['id'=>'produce_id'])->alias('produce');
    }
    /**
     * 款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
    }
    /**
     * 商品属性列表
     * @return \yii\db\ActiveQuery
     */
    public function getAttrs()
    {
        return $this->hasMany(PurchaseGoodsAttribute::class, ['id'=>'id'])->alias('attrs')->orderBy('sort asc');
    }



}
