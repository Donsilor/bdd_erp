<?php

namespace services\goods;
use common\components\Service;
use common\enums\StatusEnum;
use common\enums\AreaEnum;
use common\helpers\AmountHelper;

use addons\style\common\models\Style;
use addons\style\common\models\StyleMarkup;
use addons\style\common\models\GoodsMarkup;
use addons\style\common\models\Goods;
/**
 * 销售政策(加价率)
 * Class SalepolicyService
 * @package services\common
 */
class SalepolicyService extends Service
{   
    /**
     * 更新商品和款式地区价格数据
     * @param int $style_id
     * @return boolean
     */
    public function syncGoodsMarkup($style_id)
    {
        $style = Style::find()->where(['id'=>$style_id])->one();
        $style_salepolicy = json_decode($style->style_salepolicy,true);
        $goods_salepolicy = json_decode($style->goods_salepolicy,true);
        
        if(empty($style_salepolicy)) {
            $style_salepolicy = [];
            foreach (AreaEnum::getMap() as $area_id=>$area_name) {
                $markup_rate  = 1;
                $markup_value =  0;
                $sale_price = AmountHelper::calcMarkupPrice($style->sale_price,$markup_rate,$markup_value,2);
                $style_salepolicy[$area_id] = [
                    'area_id' =>$area_id,
                    'markup_rate' => $markup_rate,
                    'markup_value'=> $markup_value,
                    'status'=> StatusEnum::ENABLED,
                ];
            } 
            $style->style_salepolicy = json_encode($style_salepolicy);
        }
        
        $goods_list = Goods::find()->select(['id','sale_price'])->where(['style_id'=>$style_id,'status'=>StatusEnum::ENABLED])->asArray()->all();
        $goods_array = [];
        if(!empty($goods_list)) {
            foreach ($goods_list as $key =>$goods){
                $goods_array[$goods['id']] = $goods;
            }
        }
        if(empty($goods_salepolicy) || $style->type_id == \Yii::$app->params['goodsType.diamond']){   
            $goods_salepolicy = [];
            foreach ($goods_array as $goods){
                $goods_id = $goods['id'];
                foreach ($style_salepolicy as $area) {
                    $area_id  = $area['area_id'];
                    $markup_rate  = $area['markup_rate'];
                    $markup_value =  $area['markup_value'];
                    $goods_salepolicy[$area_id][$goods_id] = [
                        'area_id' =>$area_id,
                        'markup_rate' => $markup_rate,
                        'markup_value'=> $markup_value,
                        'status'=> $area['status'],
                    ];
                }
            }
            $style->goods_salepolicy = json_encode($goods_salepolicy);
        }
        
        
        foreach ($style_salepolicy as $styleArea) {
            
            $area_id = (int)$styleArea['area_id'];
            $styleMarkup = StyleMarkup::find()->where(['style_id'=>$style_id,'area_id'=>$area_id])->one();
            if(!$styleMarkup) {
                $styleMarkup = new StyleMarkup();
                $styleMarkup->style_id = $style_id;
                $styleMarkup->area_id  = $area_id;
            }
            $markup_rate = $styleArea['markup_rate'] ?? 1;
            $markup_value= $styleArea['markup_value'] ?? 0;
            $status = $styleArea['status'] ?? StatusEnum::ENABLED;
            $base_price = $style->sale_price;
            $sale_price = AmountHelper::calcMarkupPrice($base_price, $markup_rate, $markup_value,2);
            $styleMarkup->base_price = $base_price;
            $styleMarkup->sale_price = $sale_price;
            $styleMarkup->markup_rate = $markup_rate;
            $styleMarkup->markup_value = $markup_value;
            $styleMarkup->status = $status;
            $styleMarkup->save(false);
            
            $goods_areas = $goods_salepolicy[$area_id]??[];
            foreach ($goods_areas as $goods_id =>$goods_area){
                if(empty($goods_array[$goods_id])) {
                    continue;
                }      
                //基础销售价
                $base_price = $goods_array[$goods_id]['sale_price'];
                $markup_id = $styleMarkup->id;//销售政策ID
                //$markup_rate  = $goodsArea['markup_rate'];//商品加价率
                //$markup_value = $goodsArea['markup_value'];//商品固定值
                $markup_rate  = $styleMarkup->markup_rate;//款号加价率
                $markup_value = $styleMarkup->markup_value;//款号固定值                    
             
                $sale_price = AmountHelper::calcMarkupPrice($base_price, $markup_rate, $markup_value,2);
                
                $goodsMarkup = GoodsMarkup::find()->where(['goods_id'=>$goods_id,'area_id'=>$area_id])->one();
                if(!$goodsMarkup) {
                    $goodsMarkup = new GoodsMarkup();
                    $goodsMarkup->markup_id = $markup_id;
                    $goodsMarkup->goods_id  = $goods_id;
                    $goodsMarkup->area_id  = $area_id;
                }
                $goodsMarkup->base_price  = $base_price;
                $goodsMarkup->markup_rate  = $markup_rate;
                $goodsMarkup->markup_value  = $markup_value;
                $goodsMarkup->sale_price = $sale_price;
                $goodsMarkup->status = $goods_area['status'];
                $goodsMarkup->save(false);

            }

        }       
        $style->save(false);

    }
    
    /**
     * 商品实际销售价
     * @param int $goods_id
     * @param int $area_id
     * return number  -1代表不可销售
     */
    public function getGoodsSalePrice($goods_id,$area_id = null) 
    {
        if(!$area_id) {
            $area_id = $this->getAreaId(); 
        }
        
        $info = Goods::find()
            ->select(['goods.id','goods.sale_price as base_price','markup.sale_price'])
            ->leftJoin(GoodsMarkup::tableName().' markup','goods.id = markup.goods_id and markup.area_id='.$area_id)
            ->where(['goods.id'=>$goods_id,'markup.status'=>StatusEnum::ENABLED])
            ->asArray()
            ->one();
        
        if(!empty($info)) {
            $sale_price = $info['sale_price'] ? $info['sale_price'] : $info['base_price'];
        }else {
            $sale_price = -1;
        }
        return $sale_price;
    }
    
    /**
     * 款实际销售价（地区销售价格）
     * @param int $style_id 款式ID
     * @param int $area_id  地区ID
     * return number  -1代表不可销售
     */
    public function getStyleSalePrice($style_id,$area_id = null)
    {
        if(!$area_id) {
            $area_id = $this->getAreaId(); 
        }
        
        $info = Style::find()
            ->select(['style.id','style.sale_price as base_price','markup.sale_price'])
            ->leftJoin(StyleMarkup::tableName().' markup','style.id = markup.style_id and markup.area_id='.$area_id)
            ->where(['style.id'=>$style_id,'markup.status'=>StatusEnum::ENABLED])
            ->asArray()
            ->one();
        
        if(!empty($info)) {
            $sale_price = $info['sale_price'] ? $info['sale_price'] : $info['base_price'];
        }else {
            $sale_price = -1;
        }
        return $sale_price;
    }
}