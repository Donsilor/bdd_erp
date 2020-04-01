<?php

namespace addons\Style\services;
use common\components\Attr;
use common\components\Service;
use addons\style\common\models\Attribute;
use addons\style\common\models\GoodsMarkup;
use addons\style\common\models\Style;
use common\enums\InputTypeEnum;
use addons\style\common\models\Goods;
use common\helpers\ArrayHelper;
use addons\style\common\models\GoodsLang;
use common\enums\StatusEnum;
use addons\style\common\models\AttributeIndex;
use common\enums\AttrTypeEnum;
use addons\style\common\models\StyleLang;
use addons\style\common\models\Diamond;
use addons\style\common\models\DiamondLang;
use common\enums\DiamondEnum;
use addons\style\common\models\StyleMarkup;
use function GuzzleHttp\json_encode;
use yii\db\Expression;


/**
 * Class GoodsService
 * @package services\common
 */
class GoodsService extends Service
{    
    
    /**
     * 款式信息 分解 同步到goods
     * @param int $style_id
     * @param Goods $goodsModel
     */
    public function syncStyleToGoods($style_id){
        
        $styleModel = Style::find()->where(['id'=>$style_id])->one();
        $spec_array = json_decode($styleModel->style_spec,true);
        if(!empty($spec_array['c'])){
            $goods_list = $spec_array['c'];
            //$specb_list = $spec_array['b'];
        }else{
            $goods_list = [
                 [
                     'goods_sn' =>$styleModel->style_sn,
                     'sale_price' =>$styleModel->sale_price,
                     'cost_price' =>$styleModel->cost_price,
                     'market_price' =>$styleModel->market_price,
                     'goods_storage' =>$styleModel->goods_storage,
                     'status' =>$styleModel->status,  
                 ]                
            ];
        }
        $default_data = $this->formatStyleAttrs($styleModel,true);
        //款式商品属性索引表 更新入库
        $attr_index_list = $default_data['attr_index']??[];
        AttributeIndex::deleteAll(['style_id'=>$styleModel->id]);
        foreach ($attr_index_list as $attributes){
              $model = new AttributeIndex();
              $attributes['style_id'] = $styleModel->id;
              $attributes['type_id'] = $styleModel->type_id;
              $model->attributes = $attributes;
              $model->save(false);
        }
        //商品更新
        $sale_prices= [];
        foreach ($goods_list as $key=>$goods){
            //禁用没有填写商品编号的，过滤掉
            if(empty($goods['goods_sn']) && empty($goods['status'])){
                continue;
            }
            $goodsModel = Goods::find()->where(['style_id'=>$style_id,'spec_key'=>$key])->one();
            if(!$goodsModel || empty($goods['goods_sn'])) {
                //新增
                $goodsModel = new Goods();
            }
            $goodsModel->style_id = $styleModel->id;//款式ID
            $goodsModel->type_id  = $styleModel->type_id;//产品线ID
            $goodsModel->goods_image  = $styleModel->style_image;//商品默认图片
            $goodsModel->goods_sn = $goods['goods_sn'];//商品编码            
            $goodsModel->sale_price = $goods['sale_price']??0;//销售价 
            $goodsModel->market_price = $goods['market_price']??0; //成本价
            $goodsModel->cost_price = $goods['cost_price']??0;//成本价
            $goodsModel->goods_storage = $goods['goods_storage']??0;//库存
            $goodsModel->status = $goods['status']??0;//上下架状态 
            $goodsModel->spec_key = $key;
            
            if(!empty($default_data['style_spec_b'][$key])){
                $goods_specs = $default_data['style_spec_b'][$key];
                $goodsModel->goods_spec = json_encode($goods_specs['spec_keys']);
            }            
            $goodsModel->save(false);  
            $sale_prices[] = $goodsModel->sale_price;
        }
        //更新最小价格
        $min_sale_price = min($sale_prices);
        if($min_sale_price > 1) {
            $styleModel->sale_price = $min_sale_price;
        }
        $styleModel->save(false);
        //计算更新商品加价销售价
        \Yii::$app->services->salepolicy->syncGoodsMarkup($style_id);
    }
    /**
    * 款式属性格式化
    * @param Style $styleModel  款式model实例
    * @param string $is_attrindex 是否属性索引 
    * @param string $language 语言
    * @return array[]|string[][]|unknown[][]|\common\helpers\unknown[][]|unknown[]
    */
    public function formatStyleAttrs($styleModel, $is_attrindex= false , $language= null)
    {
        $type_id = $styleModel->type_id;
        $style_attr = json_decode($styleModel->style_attr,true);
        $style_spec = json_decode($styleModel->style_spec,true);
        $spec_array = array();
        if(!empty($style_attr)) {
            $spec_array['style_attr'] = $style_attr;
        }
        if(!empty($style_spec['a'])) {
            $spec_array['style_spec_a'] = $style_spec['a'];
        }
        if(!empty($style_spec['b'])) {
            $spec_array['style_spec_b'] = $style_spec['b'];
        }
        if(!empty($style_spec['c'])) {
            $spec_array['style_spec_c'] = $style_spec['c'];
        }
        $format_data = [];
        foreach ($spec_array as $key =>$spec){
            
            if($key == 'style_spec_b' || $key == 'style_spec_c'){
                $format_data[$key] = $spec;
                continue;
            }else {
                $attr_ids = array_keys($spec);
                $attr_list = \Yii::$app->services->goodsAttribute->getSpecAttrList($attr_ids,$type_id,1,$language);
                foreach ($attr_list as $attr){
                    $attr_id = $attr['id'];
                    $is_text = InputTypeEnum::isText($attr['input_type']);
                    $is_single = InputTypeEnum::isSingle($attr['input_type']);
                    //$attr['is_text'] = $is_text;
                    //$attr['is_single'] = $is_single;
                    $attr['value_id'] = 0;
                    $attr['value'] = $spec[$attr_id];
                    $attr['all'] = [];
                    if(!$is_text){
                        $attr['value_id'] = $spec[$attr_id];//属性值ID列表
                        $attr['value'] = \Yii::$app->services->goodsAttribute->getValuesByValueIds($attr['value_id'],$language);
                        $attr['all'] = \Yii::$app->services->goodsAttribute->getValuesByAttrId($attr_id,StatusEnum::ENABLED,$language);
                    }
                    $format_data[$key][$attr['id']] = $attr;
                }
            }

        }
        $style_spec_a = $format_data['style_spec_a'] ??[];
        $style_spec_b = $spec_array['style_spec_b'] ??[];
        if(!empty($style_spec_a)) {
            //处理style_spec_b
            $attr_map = array_column($style_spec_a,'attr_name','id');
            $value_map  = array_column($style_spec_a,'all','id');
            $value_map = ArrayHelper::multiToArray($value_map);
            foreach ($style_spec_b as $key=>$spec){
                $attr_ids = explode(',',$spec['ids']);
                $value_ids = explode(',',$spec['vids']);
                $spec_name = [];
                $spec_value = [];
                foreach ($attr_ids as $attr_id){                
                    $spec_name[$attr_id] = $attr_map[$attr_id]??'';
                }                
                foreach ($value_ids as $k=>$value_id){
                    $spec_value[$value_id] = $value_map[$value_id]??'';
                }
                $spec_keys = array_combine($attr_ids,$value_ids);
                $spec_names = array_combine($spec_name, $spec_value);
                $format_data['style_spec_b'][$key] = [
                       'spec_name'=>$spec_name,
                       'spec_value'=>$spec_value,
                       'spec_keys'=> $spec_keys,
                       'spec_names'=> $spec_names,
                ];
            }
        }
        if($is_attrindex == true) {
            //属性索引
            $format_data['attr_index'] = $this->formatGoodsAttrIndex($format_data);
        }
        return $format_data;
    } 
    /**
     * 款式属性索引格式化
     * @param array $data
     */
    public function formatGoodsAttrIndex($data)
    {
        $index_list = [];
        if(!empty($data['style_attr']) && is_array($data['style_attr'])) {
            foreach ($data['style_attr'] as $attr){
                $attr_list = [];
                if(is_array($attr['value'])){
                    foreach ($attr['value'] as $val_id=>$val_name){
                        $index_list[] = [
                                'attr_name'=>$attr['attr_name'],
                                'attr_id' =>$attr['id'],
                                'attr_type'=>$attr['attr_type'],
                                'attr_value_id'=>$val_id,
                                'attr_value'=> null,
                        ];
                    }                    
                }else if(trim($attr['value']) != ''){
                    $index_list[] = [
                            'attr_name'=>$attr['attr_name'],
                            'attr_id' =>$attr['id'],
                            'attr_type'=>$attr['attr_type'],
                            'attr_value_id'=>$attr['value_id'],
                            'attr_value'=>$attr['value'],
                    ];
                }
            }
            
        }
        
        if(!empty($data['style_spec_b']) && is_array($data['style_spec_b'])) {
            foreach ($data['style_spec_b'] as $key=>$attr){
                $goods = $data['style_spec_c'][$key];
                if(empty($goods['status']) || empty($goods['goods_sn'])){
                    continue;
                }
                if(is_array($attr['spec_keys'])){
                    foreach ($attr['spec_keys'] as $attr_id=>$val_id){
                        $index_list['spec_'.$attr_id.'_'.$val_id] = [
                                'attr_name'=>$attr['spec_name'][$attr_id],
                                'attr_id' =>$attr_id,
                                'attr_type'=>AttrTypeEnum::TYPE_SALE,
                                'attr_value_id'=>$val_id,
                                'attr_value'=> null,
                        ];
                    }                    
                }
            }
            
        }
        
        return $index_list;
    }
    /**
     * 查询商品详情
     * @param unknown $goods_id
     * @param number $goods_type
     */
    public function getGoodsInfo($goods_id , $goods_type = 0, $format_attr = true, $language= null, $area_id = null)
    {
        if(!$language) {
            $language = \Yii::$app->params['language'];
        }
        if(!$area_id) {
            $area_id = $this->getAreaId(); 
        }
        //如果是裸钻
        if($goods_type == \Yii::$app->params['goodsType.diamond']) {
            $goods = Diamond::find()->alias('g')
                        ->select(['g.*','IFNULL(m.sale_price,g.sale_price) as sale_price','if(markup.status=0 or m.status =0,0,g.status) as status','g.goods_sn as style_sn','lang.goods_name','lang.goods_body','g.goods_num as goods_storage'])
                        ->innerJoin(DiamondLang::tableName().' lang',"g.id=lang.master_id and lang.language='{$language}'")
                        ->leftJoin(StyleMarkup::tableName().' markup', 'g.style_id=markup.style_id and markup.area_id='.$area_id)
                        ->leftJoin(GoodsMarkup::tableName().' m','g.goods_id=m.goods_id and m.area_id='.$area_id)
                        ->where(['g.goods_id'=>$goods_id])
                        ->asArray()->one();
            if($goods) {
                 $goods_attr = [
                        DiamondEnum::CARAT=>$goods['carat'],
                        DiamondEnum::COLOR=>$goods['color'],
                        DiamondEnum::CLARITY=>$goods['clarity'],
                        DiamondEnum::CUT=>$goods['cut']                                
                ];
                $goods['goods_attr'] = json_encode($goods_attr); 
                $goods['goods_spec'] = null;
                $goods['id'] = $goods['goods_id'];
            }
            
        }else {
            $query = Goods::find()->alias('g')
            ->select(['g.*','sl.style_name as goods_name','IFNULL(m.sale_price,g.sale_price) as sale_price','if(markup.status=0 or m.status =0 or g.status =0,0,s.status) as status','s.style_sn','s.status as style_status','sl.goods_body','s.style_attr as goods_attr'])
                    ->innerJoin(Style::tableName()." s","g.style_id=s.id")
                    ->innerJoin(StyleLang::tableName()." sl","s.id=sl.master_id and sl.language='{$language}'")
                    ->leftJoin(StyleMarkup::tableName().' markup', 's.id=markup.style_id and markup.area_id='.$area_id)
                    ->leftJoin(GoodsMarkup::tableName().' m','g.id=m.goods_id and m.area_id='.$area_id)
                    ->where(['g.id'=>$goods_id]);
            
            $goods = $query->asArray()->one();
            $goods['status'] = $goods['style_status'] == StatusEnum::ENABLED ? $goods['status']:StatusEnum::DISABLED;
       }  
       
       if(!empty($goods) && $format_attr == true ) { 
           
           $goods['lang'] = [
                 'goods_attr' => $this->formatGoodsAttr($goods['goods_attr'], $goods['type_id'],$language),
                 'goods_spec' => $this->formatGoodsSpec($goods['goods_spec'], $language)
           ];
       }
       return $goods;
    }
    /**
     * 格式化商品属性
     * @param array $goods_attr
     * @param array $goods_type
     * @param string $language
     * @return array
     */
    public function formatGoodsAttr($goods_attr, $goods_type, $language = null) 
    {
        if(!$language) {
            $language = \Yii::$app->params['language'];
        }
        
        if(!is_array($goods_attr)) {
            $goods_attr = json_decode($goods_attr,true);
        }
        if(!is_array($goods_attr)) {
            return [];
        }
        $attr_ids = array_keys($goods_attr);
        $attr_list = \Yii::$app->services->goodsAttribute->getSpecAttrList($attr_ids,$goods_type,StatusEnum::ENABLED,$language);
        
        $attr_data = [];
        foreach ($attr_list as $attr){
            $attr_id = $attr['id'];
            $value = $goods_attr[$attr_id];
            if($value == ""){
                continue;
            }
            $is_text = InputTypeEnum::isText($attr['input_type']);
            $is_single = InputTypeEnum::isSingle($attr['input_type']);            
            if(!$is_text){
                if(is_array($value)) {
                    foreach ($value as $value_id) {
                        $attr['value'][$value_id] = \Yii::$app->attr->valueName($value_id ,$language);
                    }
                }else {
                    $attr['value'][$value] = \Yii::$app->attr->valueName($value ,$language);
                }
            } else {
                $attr['value'] = [$attr_id=>$value];
            }
            $attr_data[$attr_id] = $attr;
        }
        return $attr_data;  
    }
    /**
     * 获取商品规格属性
     * @param unknown $goods_spec
     * @param unknown $language
     * @return unknown[][]|mixed[][]|boolean[][]|string[][]
     */
    public function formatGoodsSpec($goods_spec, $language = null)
    {
        if(!is_array($goods_spec)) {
            $goods_spec = json_decode($goods_spec,true);
        }
        if(!is_array($goods_spec)) {
            return [];
        }
        $spec_data = [];
        foreach ($goods_spec as $attr_id=>$value_id){
            $attr_name = \Yii::$app->attr->attrName($attr_id ,$language);
            $value_name = \Yii::$app->attr->valueName($value_id ,$language);
            $spec_data[] = [
                    'attr_id'=>$attr_id,
                    'value_id'=>$value_id,
                    'attr_name'=>$attr_name,
                    'attr_value'=>$value_name,
            ];
        }
        return $spec_data;
    }
    /**
     * 规格属性合并
     * @param unknown $goods_attr
     * @param unknown $goods_spec
     * @param unknown $goods_type
     * @param unknown $language
     */
    public function formatGoodsAttrAndSpec($goods_attr, $goods_spec, $goods_type, $language = null) 
    {
        $data = array();
        $goods_attr = $this->formatGoodsAttr($goods_attr, $goods_type,$language);
        foreach ($goods_attr as $vo) {
            $data['goods_attr'][$vo['id']] = [
                'attr_id' =>$vo['id'],
                'value_id'=>implode("/", array_keys($vo['value'])),
                'attr_name'=>$vo['attr_name'],
                'attr_value'=>implode("/", $vo['value'])                    
            ];
        }
        $data['goods_spec'] = $this->formatGoodsSpec($goods_spec,$language);
        return $data;
    }
    
    /**
     * 获取款和对应的商品 （前端api专用）
     * @param unknown $style_id
     * @param unknown $language
     * @return 
     */
    public function formatStyleGoodsById($style_id, $language = null, $area_id=null){

        $ip = \Yii::$app->request->userIP;
        if(empty($area_id)){
            $area_id = $this->getAreaId(); 
        }

        if(empty($language)){
            $language = \Yii::$app->params['language'];
        }
        $style_spec_array = [
            '10' =>array(
                 'attr_name'=>'materials',
                 'key_name'=>'material',
             ),//成色
             '38'=>array(
                 'attr_name'=>'sizes',
                 'key_name'=>'size',
             ),  // 尺寸
        ];
        $query = Style::find()->alias('m')
            ->leftJoin(StyleLang::tableName().' lang',"m.id=lang.master_id and lang.language='".$language."'")
            ->leftJoin(StyleMarkup::tableName().' markup', 'm.id=markup.style_id and markup.area_id='.$area_id)
            ->where(['m.id'=>$style_id])
            ->andWhere(['or',['=','markup.status',1],['IS','markup.status',new \yii\db\Expression('NULL')]]);
        $style_model =  $query->one();
        $format_style_attrs = $this->formatStyleAttrs($style_model);
//        return $format_style_attrs;
        $model = $query ->select(['m.id','m.style_sn','m.status','m.goods_images','m.type_id','m.style_3ds','m.style_image','IFNULL(markup.sale_price,m.sale_price) as sale_price','lang.goods_body','lang.style_name','lang.meta_title','lang.meta_word','lang.meta_desc'])
            ->asArray()->one();

        //规格属性
        $style = array();
        $style['id'] = $model['id'];
        $style['goodsName'] = $model['style_name'];
        $style['goodsCode'] = $model['style_sn'];
        $style['goodsImages'] = $model['goods_images'];
        $style['salePrice'] = $this->exchangeAmount($model['sale_price']);
        $style['coinType'] = $this->getCurrencySign();
        $style['goods3ds'] = $model['style_3ds'];
        $style['goodsDesc'] = $model['goods_body'];
        $style['categoryId'] = $model['type_id'];
        $style['goodsGiaImage'] = null;
        $style['goodsMod'] = $model['type_id'] == 12 ? 1: 2;
        $style['goodsStatus'] = $model['status']== 1? 2:1;
        $style['htmlUrl'] = null;
        $style['metaDesc'] = $model['meta_desc'];
        $style['metaTitle'] = $model['meta_title'];
        $style['metaWord'] = $model['meta_word'];
        $style['qrCode'] = '';
        $style['recommends'] = null;
        $style['templateId'] = null;
        $style['ip'] = $ip;
        $style['area_id'] = $area_id;

//        if(isset($format_style_attrs['style_spec_a'])){
//            $format_style_spec = $format_style_attrs['style_spec_a'];
//            foreach ($style_spec_array as $key => $val){
//                if(isset($format_style_spec[$key]['value'])){
//                    foreach ($format_style_spec[$key]['value'] as $k => $v){
//                        $attr = array();
//                        $attr['id'] = $k;
//                        $attr['image'] = \Yii::$app->services->goodsAttribute->getAttrImageByValueId($k);
//                        $attr['name'] = $v;
//                        $style[$val['attr_name']][] = $attr;
//                    }
//                }else{
//                    $style[$val['attr_name']] = null;
//                }
//            }
//        }

        //基础属性
        if(isset($format_style_attrs['style_attr'])){
            $style_attr = $format_style_attrs['style_attr'];
            foreach ($style_attr as $attr){
                //对售後服務特殊处理
                if($attr['id'] == 52){
                    $style['goodsServices'] = join(',', $attr['value_id']);
                    $style['goodsServicesJsons'] = \Yii::$app->services->goodsAttribute->getAttrValuesByValueIds($attr['value_id']);
                    continue;
                }

                $attr_value = $attr['value'];
                $attr_value_id = $attr['value_id'];
                if(empty($attr_value)) {
                    continue;
                }
                if(is_array($attr_value)){
                    $attr_value = implode('|',$attr_value);
                }
                if(is_array($attr_value_id)){
                    $attr_value_id = implode('|',$attr_value_id);
                }
                $style['specs'][] = [
                    'categoryId'=>$attr['attr_type'],
                    'configAttrId'=>$attr_value_id,
                    'configId'=>$attr['id'],
                    //'configInputType'=>$attr['input_type'],
                    'configName'=>$attr['attr_name'],
                    'configAttrVal'=>$attr_value,
                    'goodsId'=>$style_id,
                   // 'configRequired'=>$attr['is_require'],
                   //'queryColumn'=>null,
                   //'id'=>'',
                   //'sort'=>null
                ];
            }
        }

        //商品

        $goods_array = Goods::find()->alias('g')
            ->leftJoin(GoodsMarkup::tableName().' markup', 'g.id=markup.goods_id and markup.area_id='.$area_id)
            ->where(['g.style_id'=>$style_id ,'g.status'=>StatusEnum::ENABLED])
            ->andWhere(['or',['=','markup.status',1],['IS','markup.status',new \yii\db\Expression('NULL')]])
            ->select(['g.id','type_id','goods_sn','IFNULL(markup.sale_price,g.sale_price) as sale_price','goods_storage','warehouse','goods_spec'])
            ->asArray()
            ->all();
        $details = array();
        $totalStock = 0;

        $check_goods_spec_ids = array();
        foreach ($goods_array  as $key => $val){
            $goods_spec = json_decode($val['goods_spec']);
            $goods_spec = (array)$goods_spec;
            foreach ($style_spec_array as $k => $v){
                if(isset($goods_spec[$k])){
                    $goods_spec_id = (int)$goods_spec[$k];

                    if(!in_array($goods_spec_id,$check_goods_spec_ids)){
                        $check_goods_spec_ids[] = $goods_spec_id;
                        $attr = array();
                        $attr['id'] = $goods_spec_id;
                        $attr['image'] = \Yii::$app->services->goodsAttribute->getAttrImageByValueId($goods_spec_id);
                        $attr['name'] = \Yii::$app->attr->valueName($goods_spec_id);
                        $style[$v['attr_name']][] = $attr;
                    }


                    $details[$key][$v['key_name']] = $goods_spec_id;
                }else{
                    $details[$key][$v['key_name'] ]= null;
                }
            }


//            if(!empty($goods_spec)){
//                foreach ($goods_spec as $k=>$v){
//                    $details[$key][$style_spec_array[$k]['key_name']] = (int)$v;
//                }
//            }

            $details[$key]['barCode'] = null;
            $details[$key]['productNumber'] = null;
            $details[$key]['stock'] = $val['goods_storage'];
            $details[$key]['warehouse'] = $val['warehouse'];
            $details[$key]['categoryId'] = $model['type_id'];
            $details[$key]['goodsDetailsCode'] = $val['goods_sn'];
            $details[$key]['retailMallPrice'] = (float)$this->exchangeAmount($val['sale_price']);
            $details[$key]['retailPrice'] = null;
            $details[$key]['goodsId'] = $style_id;
            $details[$key]['id'] = $val['id'];
            $totalStock += $val['goods_storage'];

        }

        //对尺寸进行排序
        if(isset($style['sizes'])){
            $names = array_column($style['sizes'],'name');
            array_multisort($names,SORT_ASC,$style['sizes']);
        }




        $style['details'] = $details;
        $style['totalStock'] = $totalStock;

        return $style;

    }    
    /**
     * 下单商品库存更改
     * @param unknown $goods_id  商品ID
     * @param unknown $quantity  变化数量
     * @param unknown $for_sale 销售
     */
    public function updateGoodsStorageForOrder($goods_id,$quantity,$goods_type)
    {        
        if($goods_type == \Yii::$app->params['goodsType.diamond']){
            \Yii::$app->services->diamond->updateGoodsStorageForOrder($goods_id, $quantity);
        }else {
            $data = [
                'goods_storage'=> new Expression("goods_storage+({$quantity})"),
                'sale_volume'  =>new Expression("sale_volume-({$quantity})")
            ];            
            Goods::updateAll($data,['id'=>$goods_id]);
            Style::updateAll($data,['in','id',Goods::find()->select(['style_id'])->where(['id'=>$goods_id])]);
        }
    }

}