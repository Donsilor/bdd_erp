<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_goods_print".
 *
 * @property int $purchase_goods_id
 * @property string $purchase_group 采购组
 * @property string $processing_order 处理次序
 * @property string $item_number 物料号
 * @property string $image
 * @property string $cate 单类
 * @property string $processing 后加工
 * @property string $customers 出数客户
 * @property string $number 项目号
 * @property string $mounting_method 镶法
 * @property string $circle 圈口
 * @property string $maximum 可改蜡最大
 * @property string $minimum 可改蜡最小
 * @property string $weight 重量
 * @property string $process_desc 工艺描述
 * @property string $special_process 特殊工艺
 * @property string $printing_req 字印要求
 * @property string $size_req 尺寸要求
 * @property string $form 形式
 * @property string $accessories_req 配件要求
 * @property string $factory_model 工厂模号
 * @property string $main_stone_priority 主石优先次序
 * @property string $main_socket_range 主石镶口范围
 * @property string $main_diameter 主石直径
 * @property string $main_stone_remark 主石备注
 * @property string $vice_stone_priority 副石优先次序
 * @property string $vice_socket_range 副石镶口范围
 * @property string $vice_diameter 副石直径
 * @property string $vice_stone_remark 副石备注
 * @property int $sum_num 总数
 * @property string $main_stone_shape 主石形状
 * @property string $stone_weight_range 配石重量区间
 * @property string $main_stone_spec 主石颜色净度优先次序
 * @property string $accessories_remark 配石要求备注
 * @property string $product_req 生产要求
 * @property string $product_desc 货品描述
 * @property string $order_type 订单类型
 * @property string $price_system 价格体系
 * @property string $pricing_type 定价类型
 * @property string $with_stone_req 配石要求
 * @property string $billing_req 发单要求
 * @property string $remark 备注
 */
class PurchaseGoodsPrint extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_goods_print');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_goods_id'], 'required'],
            [['purchase_goods_id', 'sum_num','creator_id','updated_at'], 'integer'],
            [['purchase_group', 'processing_order', 'item_number', 'cate', 'processing', 'customers', 'number', 'mounting_method', 'circle', 'maximum', 'minimum', 'weight', 'factory_model', 'main_stone_priority', 'main_socket_range', 'main_diameter', 'vice_stone_priority', 'vice_socket_range', 'vice_diameter', 'main_stone_shape', 'stone_weight_range', 'order_type'], 'string', 'max' => 30],
            [['image', 'special_process', 'printing_req', 'size_req', 'form', 'accessories_req'], 'string', 'max' => 100],
            [['process_desc', 'main_stone_remark', 'vice_stone_remark', 'main_stone_spec', 'accessories_remark', 'product_req', 'product_desc', 'with_stone_req', 'billing_req', 'remark'], 'string', 'max' => 200],
            [['purchase_goods_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purchase_goods_id' => Yii::t('app', 'Purchase Goods ID'),
            'purchase_group' => Yii::t('app', '采购组'),
            'processing_order' => Yii::t('app', '处理次序'),
            'item_number' => Yii::t('app', '物料号'),
            'image' => Yii::t('app', 'Image'),
            'cate' => Yii::t('app', '单类'),
            'processing' => Yii::t('app', '后加工'),
            'customers' => Yii::t('app', '出数客户'),
            'number' => Yii::t('app', '项目号'),
            'mounting_method' => Yii::t('app', '镶法'),
            'circle' => Yii::t('app', '圈口'),
            'maximum' => Yii::t('app', '可改蜡最大'),
            'minimum' => Yii::t('app', '可改蜡最小'),
            'weight' => Yii::t('app', '重量'),
            'process_desc' => Yii::t('app', '工艺描述'),
            'special_process' => Yii::t('app', '特殊工艺'),
            'printing_req' => Yii::t('app', '字印要求'),
            'size_req' => Yii::t('app', '尺寸要求'),
            'form' => Yii::t('app', '形式'),
            'accessories_req' => Yii::t('app', '配件要求'),
            'factory_model' => Yii::t('app', '工厂模号'),
            'main_stone_priority' => Yii::t('app', '主石优先次序'),
            'main_socket_range' => Yii::t('app', '主石镶口范围'),
            'main_diameter' => Yii::t('app', '主石直径'),
            'main_stone_remark' => Yii::t('app', '主石备注'),
            'vice_stone_priority' => Yii::t('app', '副石优先次序'),
            'vice_socket_range' => Yii::t('app', '副石镶口范围'),
            'vice_diameter' => Yii::t('app', '副石直径'),
            'vice_stone_remark' => Yii::t('app', '副石备注'),
            'sum_num' => Yii::t('app', '总数'),
            'main_stone_shape' => Yii::t('app', '主石形状'),
            'stone_weight_range' => Yii::t('app', '配石重量区间'),
            'main_stone_spec' => Yii::t('app', '主石颜色净度优先次序'),
            'accessories_remark' => Yii::t('app', '配石要求备注'),
            'product_req' => Yii::t('app', '生产要求'),
            'product_desc' => Yii::t('app', '货品描述'),
            'order_type' => Yii::t('app', '订单类型'),
            'with_stone_req' => Yii::t('app', '配石要求'),
            'billing_req' => Yii::t('app', '发单要求'),
            'remark' => Yii::t('app', '备注'),
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'creator_id' => '创建人',
        ];
    }


    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->creator_id = Yii::$app->user->id;

        }
        $this->created_at = time();

        return parent::beforeSave($insert);
    }

    /**
     * 采购单一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseGoods()
    {
        return $this->hasOne(PurchaseGoods::class, ['id'=>'purchase_goods_id'])->alias('purchase_goods');
    }
}
