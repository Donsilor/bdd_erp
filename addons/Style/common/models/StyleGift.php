<?php

namespace addons\Style\common\models;

use Yii;
use common\models\backend\Member;
use addons\Sales\common\models\SaleChannel;

/**
 * This is the model class for table "style_gift".
 *
 * @property int $id ID
 * @property int $style_id 款式ID
 * @property string $style_sn 款号
 * @property string $gift_name 赠品名称
 * @property string $style_image 款式图片
 * @property string $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $goods_size 成品尺寸(mm)
 * @property string $finger 手寸(美号)
 * @property string $finger_hk 手寸(港号)
 * @property string $chain_length 链长(cm)
 * @property string $cost_price 成本价
 * @property string $market_price 市场价
 * @property string $sale_price 销售价
 * @property int $channel_id 销售渠道
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用
 * @property int $sort 排序
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class StyleGift extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('gift');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id', 'style_sex', 'channel_id', 'auditor_id', 'audit_status', 'audit_time', 'status', 'sort', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            //[['style_sn'], 'required'],
            [['cost_price', 'market_price', 'sale_price'], 'number'],
            [['style_sn'], 'string', 'max' => 30],
            [['gift_name', 'goods_size'], 'string', 'max' => 100],
            [['style_image', 'audit_remark', 'remark'], 'string', 'max' => 255],
            [['style_cate_id', 'material_type', 'material_color', 'finger', 'finger_hk', 'chain_length'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_id' => '款式ID',
            'style_sn' => '款号',
            'gift_name' => '赠品名称',
            'style_image' => '款式图片',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'goods_size' => '成品尺寸(mm)',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'chain_length' => '链长(cm)',
            'cost_price' => '预估成本价',
            'market_price' => '市场价',
            'sale_price' => '销售价',
            'channel_id' => '销售渠道',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '备注',
            'status' => '状态',
            'sort' => '排序',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
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
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
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
     * 销售渠道
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('saleChannel');
    }
}
