<?php

namespace addons\Style\common\models;

use addons\Supply\common\models\Factory;
use addons\Supply\common\models\Supplier;
use common\enums\ConfirmEnum;
use common\helpers\StringHelper;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "style_gold_loss_rate".
 *
 * @property int $id
 * @property int $style_id 款号ID
 * @property int $position 石头位置
 * @property int $stone_type 石头类型
 * @property int $creator_id 配置人ID
 * @property int $sort
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class StyleFactory extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_factory");
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id','factory_id'], 'required'],
            [[ 'style_id','factory_id','is_made','is_default', 'creator_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['factory_mo'], 'string', 'max' => 30],
            [['shipping_time'], 'number'],
            [['style_id','factory_id'],'unique','targetAttribute' => [ 'style_id','factory_id'],'comboNotUnique'=>'已经存在'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_id' => '款号ID',
            'factory_id' => '工厂名称',
            'is_made' => '是否支持定制',
            'is_default' => '是否默认',
            'remark' => '备注(计费方式)',
            'factory_mo' => '工厂模号',
            'creator_id' => '配置人',
            'shipping_time' => '出货时间(天)',
            'sort' => '排序',
            'status' => '状态',
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
            $this->creator_id = Yii::$app->user->id;

            //如果第一次添加，则强制默认为第一张
            $style_factory = self::find()->where(['style_id'=>$this->style_id])->all();
            if(empty($style_factory)) $this->is_default = ConfirmEnum::YES;
        }

        if($this->is_default == ConfirmEnum::YES){
            self::updateAll(['is_default'=>ConfirmEnum::NO],['style_id'=>$this->style_id]);
            Style::updateAll(['factory_mo'=>$this->factory_mo, 'factory_id'=>$this->factory_id],['id'=>$this->style_id]);
        }

        $this->shipping_time = StringHelper::dateToInt($this->shipping_time);

        return parent::beforeSave($insert);
    }



    /**
     * 关联款式一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyle()
    {
        return $this->hasOne(Style::class, ['id'=>'style_id'])->alias('style');
    }

    /**
     * 关联工厂一对一
     * @return \yii\db\ActiveQuery
     */
    public function getFactory()
    {
        return $this->hasOne(Factory::class, ['id'=>'factory_id'])->alias('factory');
    }

    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
    }

    /**
     * 关联供应商一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'factory_id'])->alias('supplier');
    }
}
