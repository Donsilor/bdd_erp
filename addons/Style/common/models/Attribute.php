<?php

namespace addons\Style\common\models;

use Yii;

/**
 * 属性表 Model
 *
 * @property int $id 主键
 * @property int $cat_id 分类ID
 * @property int $attr_type 分类类型(1-基础属性,2-销售属性,3-定制属性)
 * @property int $input_type 属性输入框类型(1-输入框,2-下拉框,3-单选,4-多选)
 * @property int $is_require 是否必填(1-是,0-否)
 * @property int $is_system 是否系统配置(1是,0否)
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $sort 排序字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Attribute extends BaseModel
{
    public $attr_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("attribute");
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['id','status', 'sort','created_at', 'updated_at'], 'integer'],
            [['image'], 'string','max'=>100],
            [['code'], 'string','max'=>30],
            [['image'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',     
            'code' => '编码',
            'image' => '图标',
            'status' => '状态',
            'sort' => '排序',    
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 语言扩展表
     * @return \addons\Style\common\models\AttributeLang
     */
    public function langModel()
    {
        return new AttributeLang();
    }
    /**
     * 关联语言一对多
     * @return \yii\db\ActiveQuery
     */
    public function getLangs()
    {
        return $this->hasMany(AttributeLang::class,['master_id'=>'id']);
      
    }
    /**
     * 关联语言一对一
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(AttributeLang::class, ['master_id'=>'id'])->alias('lang')->where(['lang.language'=>Yii::$app->language]);
    }    
}
