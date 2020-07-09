<?php

namespace addons\Style\common\models;
use Yii;

/**
 * 属性规格表（款式分类和属性 组合关系表）Model
 *
 * @property int $id 主键
 * @property int $type_id 产品线ID
 * @property int $attr_id 属性ID
 * @property int $attr_type 分类类型(1-基础属性,2-销售属性,3-定制属性)
 * @property string $attr_values 属性值ID
 * @property int $input_type 属性输入框类型(1-输入框,2-下拉框,3-单选,4-多选)
 * @property int $is_require 是否必填(1-是,0-否)
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $sort 排序字段
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class AttributeSpec extends BaseModel
{
    public $attr_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("attribute_spec");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_cate_id', 'attr_id', 'attr_type','modules', 'input_type', 'is_require','is_inlay', 'status'], 'required'],
            [['style_cate_id', 'attr_id', 'attr_type', 'input_type', 'is_require','is_inlay' ,'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            //[['attr_values'], 'string', 'max' => 500],
            [['attr_id'],'unique', 'targetAttribute'=>['style_cate_id','attr_id'],
              //'targetClass' => '\models\Dishes', // 模型，缺省时默认当前模型。
              'comboNotUnique' => '当前产品线已添加过该属性' //错误信息
            ],
            [['attr_values','modules'],'implodeArray','params'=>['split'=>',']],
//            [['attr_name','language'], 'safe'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_cate_id' => '款式分类',
            'attr_id' => '属性',
            'attr_type' => '属性类型',
            'attr_values' => '属性值',
            'input_type' => '显示类型',
            'is_require' => '必填',
            'is_inlay' => '产品线',
            'status' => '状态',
            'sort' => '排序',
            'modules' => '所属模块',
            'attr_name'=>  '属性名称',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',            
        ];
    }
    
    /**
     * 属性语言一对一
     * @return \yii\db\ActiveQuery
     */
    public function getAttr()
    {
        return $this->hasOne(AttributeLang::class, ['master_id'=>'attr_id'])->alias('attr')->where(['attr.language'=>Yii::$app->params['language']]);
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    }
}
