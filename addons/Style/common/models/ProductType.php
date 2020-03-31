<?php

namespace addons\style\common\models;

use Yii;
use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\traits\Tree;

/**
 * This is the model class for table "{{%addon_article_cate}}".
 *
 * @property int $id 主键
 * @property string $title 标题
 * @property string $tree 树
 * @property int $sort 排序
 * @property int $level 级别
 * @property int $pid 上级id
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class ProductType extends \common\models\base\BaseModel
{
    use Tree, MerchantBehavior;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{style_product_type}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['pid','status'], 'required'],
                [['id','merchant_id','sort', 'level', 'pid', 'status', 'created_at', 'updated_at'], 'integer'],
                [['image'], 'string', 'max' => 100],
                [['tree'], 'string', 'max' => 255],
                [['type_name'], 'safe'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'type_name' => '产品线',
                'image' =>  '图标',
                'sort' => '排序',
                'tree' => '树',
                'level' => '级别',
                'pid' => '父级',
                'status' => '状态',
                'created_at' => '创建时间',
                'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取树状数据
     *
     * @return mixed
     */
    public static function getTree()
    {
        $cates = self::find()
        ->where(['status' => StatusEnum::ENABLED])
        ->andWhere(['merchant_id' => Yii::$app->services->merchant->getId()])
        ->asArray()
        ->all();
        
        return ArrayHelper::itemsMerge($cates);
    }
    
    /**
     * 获取下拉
     *
     * @param string $id
     * @return array
     */
    public static function getDropDownForEdit($id = '')
    {
        $list = self::find()->alias('a')
        ->where(['>=', 'status', StatusEnum::DISABLED])
        ->andWhere(['merchant_id' => Yii::$app->services->merchant->getId()])
        ->andFilterWhere(['<>', 'a.id', $id])
        ->leftJoin(GoodsTypeLang::tableName().' b', 'b.master_id = a.id and b.language = "'.Yii::$app->language.'"')
        ->select(['a.id', 'b.type_name', 'pid', 'level'])
        ->orderBy('sort asc')
        ->asArray()
        ->all();
        
        $models = ArrayHelper::itemsMerge($list);
        $data = ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models,'id', 'type_name'), 'id', 'type_name');
        return ArrayHelper::merge([0 => '顶级分类'], $data);
    }
    
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown()
    {
        $models = self::find()->alias('a')
        ->where(['status' => StatusEnum::ENABLED])
        ->andWhere(['merchant_id' => Yii::$app->services->merchant->getId()])
        ->leftJoin('{{%goods_type_lang}} b', 'b.master_id = a.id and b.language = "'.Yii::$app->language.'"')
        ->select(['a.*', 'b.type_name'])
        ->orderBy('sort asc,created_at asc')
        ->asArray()
        ->all();
        
        $models = ArrayHelper::itemsMerge($models);
        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models,'id', 'type_name'), 'id', 'type_name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'pid']);
    }
    
    /**
     * 语言扩展表
     * @return \addons\style\common\models\AttributeLang
     */
    public function langModel()
    {
        return new ProductTypeLang();
    }
    
    public function getLangs()
    {
        return $this->hasMany(ProductTypeLang::class,['master_id'=>'id']);
        
    }
    
    /**
     * 关联语言一对一
     * @param string $languge
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(ProductTypeLang::class, ['master_id'=>'id'])->alias('lang')->where(['lang.language' => Yii::$app->params['language']]);
    }
    
    
}
