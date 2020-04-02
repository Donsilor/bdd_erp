<?php

namespace addons\style\common\models;

use Yii;
use common\helpers\ArrayHelper;

/**
 * 款式表 Model
 *
 * @property int $id 款式ID
 * @property string $style_sn 款式编号
 * @property int $cat_id 产品分类
 * @property int $type_id 产品线
 * @property int $merchant_id 商户ID
 * @property string $style_image 商品主图
 * @property string $style_3ds 360主图
 * @property string $goods_images 商品图库
 * @property string $style_attr 款式属性
 * @property string $style_custom 款式自定义属性
 * @property string $style_salepolicy 款式销售政策
 * @property string $goods_salepolicy 商品销售政策
 * @property string $style_spec 款式规格属性
 * @property string $goods_body 商品内容
 * @property string $mobile_body 手机端商品描述
 * @property string $sale_price 销售价
 * @property string $sale_volume 销量
 * @property string $virtual_volume 虚拟销量
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property string $goods_storage 库存量
 * @property string $goods_clicks 浏览量
 * @property string $virtual_clicks 虚拟浏览量
 * @property int $storage_alarm 库存报警值
 * @property int $is_recommend 商品推荐 1是，0否，默认为0
 * @property int $is_lock 商品锁定 0未锁，1已锁
 * @property int $supplier_id 供应商id
 * @property int $status 款式状态 0下架，1正常，-1删除
 * @property int $verify_status 商品审核 1通过，0未通过，10审核中
 * @property string $verify_remark 审核失败原因
 * @property int $created_at 商品添加时间
 * @property int $updated_at
 */
class Style extends BaseModel
{
    
    public $attr_require;//必填属性
    public $attr_custom;//选填属性
        
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return static::tableFullName("style");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['type_id', 'merchant_id','sale_volume','virtual_volume','virtual_clicks','goods_clicks','goods_storage','goods_clicks', 'storage_alarm', 'is_recommend', 'is_lock', 'supplier_id', 'status', 'audit_status','onsale_time', 'created_at', 'updated_at'], 'integer'],
                [['type_id','style_sn','sale_price','goods_storage'], 'required'],
                [['sale_price', 'market_price', 'cost_price'], 'number'],
                ['sale_price','compare','compareValue' => 0, 'operator' => '>'],
                ['market_price','compare','compareValue' => 0, 'operator' => '>'],
                ['cost_price','compare','compareValue' => 0, 'operator' => '>'],
                ['market_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                ['sale_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                ['cost_price','compare','compareValue' => 1000000000, 'operator' => '<'],
                [['style_sn'], 'string', 'max' => 50],
                [['style_image','style_3ds'], 'string', 'max' => 100],
                [['audit_remark'], 'string', 'max' => 255], 
                [['style_sn'],'unique'],                
                [['attr_require'], 'required','isEmpty'=>function($value){
                    return false;
                }], 
                /* [['sale_price','goods_storage'], 'required','isEmpty'=>function($value){
                    return $value <= 0 ? true: false;
                }], */
                //['attr_require','validAttrRequire'],
        ];
    }    
    /* public function validAttrRequire($attribute, $params)
    {
        
        if($this->$attribute && is_array($this->$attribute)){
            foreach ($this->$attribute as $key=>$val){
                if($val == ""){
                    $this->addError($attribute."[{$key}]","当前属性不能为空222");
                }
            }            
        }        
    } */        

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $currency = \Yii::$app->params['currency'];
        return [
            'id' => Yii::t('goods', 'ID'),
            'style_sn' => Yii::t('goods', '款式编号'),
            'cat_id' => Yii::t('goods', '款式分类'),
            'type_id' => Yii::t('goods', '产品线'),
            'merchant_id' => Yii::t('goods', '商户ID'),
            'style_image' => Yii::t('goods', '商品图片'),
            'style_3ds' => Yii::t('goods', '360主图'),
            'goods_images' => Yii::t('goods', '商品图片'),
            'style_attr' => Yii::t('goods', '款式属性'),            
            'style_spec' => Yii::t('goods', '款式规格'),
            'style_salepolicy' => Yii::t('goods', '销售政策'),
            'sale_price' => Yii::t('goods', '销售价')."({$currency})",
            'sale_volume' => Yii::t('goods', '销量'),
            'virtual_volume'=>  Yii::t('goods', '虚拟销量'),
            'market_price' => Yii::t('goods', '市场价')."({$currency})",
            'cost_price' => Yii::t('goods', '成本价')."({$currency})",
            'goods_storage'=>  Yii::t('goods', '库存'),
            'goods_clicks'=>  Yii::t('goods', '浏览量'),
            'virtual_clicks'=>  Yii::t('goods', '虚拟浏览量'),
            'storage_alarm' => Yii::t('goods', 'Storage Alarm'),
            'is_recommend' => Yii::t('goods', 'Is Recommend'),
            'is_lock' => Yii::t('goods', 'Is Lock'),
            'supplier_id' => Yii::t('goods', 'Supplier ID'),
            'status' => Yii::t('goods', '上架状态'),
            'verify_status' => Yii::t('goods', 'Verify Status'),
            'verify_remark' => Yii::t('goods', 'Verify Remark'),
            'onsale_time' => Yii::t('goods', '上架时间'),
            'created_at' => Yii::t('goods', '创建时间'),
            'updated_at' => Yii::t('goods', '更新时间'),
            //自定义属性    
            'attr_require' => Yii::t('goods', '当前属性'),
            'attr_custom' => Yii::t('goods', '当前属性'),
        ];
    }
    
    /**
     * 语言扩展表
     * @return \addons\style\common\models\AttributeLang
     */
    public function langModel()
    {
        return new StyleLang();
    }
    /**
     * 关联语言一对多
     * @return \yii\db\ActiveQuery
     */
    public function getLangs()
    {
        return $this->hasMany(StyleLang::class,['master_id'=>'id'])->alias('langs');
        
    }
    /**
     * 关联语言一对一
     * @return \yii\db\ActiveQuery
     */
    public function getLang($language = null)
    {
        return $this->hasOne(StyleLang::class, ['master_id'=>'id'])->alias('lang')->where(['lang.language'=>Yii::$app->params['language']]);
    }
    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(TypeLang::class, ['master_id'=>'type_id'])->alias('type')->where(['type.language'=>Yii::$app->params['language']]);
    }
    /**
     * 款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(CategoryLang::class, ['master_id'=>'cat_id'])->alias('cate')->where(['cate.language'=>Yii::$app->params['language']]);
    }    
}
