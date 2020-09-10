<?php

namespace addons\Sales\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\StyleChannel;
use Yii;

/**
 * This is the model class for table "sales_order_goods".
 *
 * @property int $id 订单商品表索引id
 * @property int $merchant_id 商户ID
 * @property int $order_id 订单id
 * @property string $style_sn 款式编号
 * @property string $goods_sn 商品编号
 * @property string $goods_id 现货货号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $goods_image 商品图片
 * @property string $goods_price 商品价格
 * @property string $goods_pay_price 商品实际成交价
 * @property string $goods_discount 优惠金额
 * @property string $goods_spec 商品规格
 * @property string $currency 货币
 * @property double $exchange_rate 汇率
 * @property int $delivery_status 发货状态
 * @property int $distribute_status 配货状态
 * @property string $produce_sn 布产编号
 * @property int $is_stock 是否现货(1是0否)
 * @property int $is_gift 是否赠品
 * @property int $is_return 是否退款
 * @property int $return_id 退款ID
 * @property string $return_no 退款编号
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class OrderGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'order_id', 'style_cate_id', 'product_type_id', 'is_inlay','style_channel_id','jintuo_type', 'qiban_type','style_sex' ,'goods_num', 'delivery_status', 'distribute_status', 'bc_status','is_stock', 'is_gift', 'is_return', 'return_id', 'created_at', 'updated_at','is_apply','is_bc'], 'integer'],
            [['order_id','jintuo_type','goods_name','goods_num','goods_price','goods_pay_price'],'required'],
            [['goods_price', 'goods_pay_price', 'goods_discount', 'exchange_rate','assess_cost'], 'number'],
            [['style_sn', 'goods_sn','qiban_sn'], 'string', 'max' => 50],
            [['goods_id'], 'string', 'max' => 20],
            [['goods_name'], 'string', 'max' => 300],
            [['goods_image'], 'string', 'max' => 100],
            [['remark'], 'string', 'max' => 255],
            [['goods_spec'], 'string', 'max' => 1024],
            [['currency'], 'string', 'max' => 5],
            [['produce_sn', 'return_no', 'out_sku_id','out_ware_id'], 'string', 'max' => 30],
            [['apply_info'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'order_id' => '订单id',
            'style_sn' => '款号',
            'qiban_sn' => '起版号',
            'goods_sn' => '商品编号',
            'goods_id' => '条码号',
            'style_cate_id' => '款式分类',
            'is_inlay' => '是否镶嵌',
            'product_type_id' => '产品线',
            'style_channel_id' => '款式渠道',
            'goods_name' => '商品名称',
            'jintuo_type' => '金托类型',
            'qiban_type' => '起版类型',
            'goods_num' => '商品数量',
            'goods_image' => '商品图片',
            'style_sex' => '款式性别',
            'goods_price' => '商品原价',
            'assess_cost' => '预估成本',
            'goods_pay_price' => '实际成交价',
            'goods_discount' => '优惠金额',
            'goods_spec' => '商品参数',
            'currency' => '货币',
            'exchange_rate' => '汇率',
            'delivery_status' => '发货状态',
            'distribute_status' => '配货状态',
            'bc_status' => '布产状态',
            'produce_sn' => '布产编号',
            'is_stock' => '是否现货',
            'is_gift' => '是否赠品',
            'is_bc' => '是否布产',
            'is_return' => '是否退款',
            'return_id' => '退款ID',
            'return_no' => '退款编号',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'is_apply' => '是否申请修改',
            'remark' => '备注',
            'out_sku_id' => '外部商品SKU',
            'out_ware_id' => '外部商品编号',

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
     * 款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
    }


    /**
     * 采购单一对一
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id'=>'order_id'])->alias('order');
    }


    /**
     * 商品属性列表
     * @return \yii\db\ActiveQuery
     */
    public function getAttrs()
    {
        return $this->hasMany(OrderGoodsAttribute::class, ['id'=>'id'])->alias('attrs')->orderBy('sort asc');
    }
    /**
     * 商品规格
     * @return string
     */
    public function getGoodsSpec()
    {
        $spec_list = [];
        //销售属性
        $goods_name_pices = [];
        if($this->out_ware_id) {
            $goods_name_pices = explode(' ',$this->goods_name);
            if(count($goods_name_pices) >1) {
                unset($goods_name_pices[0]);
                $spec_list['销售属性'] = implode(' ',$goods_name_pices);
            }
        } 
        //商品规格
        $spec_list = $spec_list + (json_decode($this->goods_spec,true) ?? []);        
        $str = '';
        foreach ($spec_list as $key=>$value) {
            $str .= $key.':'.$value."<br/>";
        }
        return $str;
    }
}
