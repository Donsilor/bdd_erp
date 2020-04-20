<?php

namespace addons\Style\services;

use common\components\Service;
use addons\Style\common\models\Style;
use addons\Style\common\models\Goods;
use common\enums\StatusEnum;
use addons\Style\common\models\StyleGoods;
use yii\base\Exception;
use addons\Style\common\enums\AttrTypeEnum;


/**
 * Class GoodsService
 * @package services\common
 */
class StyleGoodsService extends Service
{   
    /**
     * 生成款式商品
     * @param unknown $style_id
     * @param unknown $goods_list
     */
    public function createStyleGoods($style_id,$goods_list)
    {
        $style = Style::find()->where(['id'=>$style_id])->one();
        if(empty($style) || empty($goods_list)) {
            return false;
        }
        //批量更新款式商品
        $goods_update = [
                'style_sn'=>$style->style_sn,
                'goods_name'=>$style->style_name,                
                'goods_image'=>$style->style_image,
                'status'=> StatusEnum::DISABLED,
        ];
        StyleGoods::updateAll($goods_update,['style_id'=>$style_id]); 
        $cost_prices = array();
        $goods_num   = 0;
        foreach ($goods_list as $goods) {
            $styleGoods = StyleGoods::find()->where(['style_id'=>$style_id,'spec_key'=>$goods['spec_key']])->one();
            if(!$styleGoods) {
                //新增
                $styleGoods = new StyleGoods();
            }
            $styleGoods->attributes = $goods;
            $styleGoods->style_id = $style->id;
            $styleGoods->style_cate_id = $style->style_cate_id;
            $styleGoods->product_type_id = $style->product_type_id;
            $styleGoods->goods_image  = $style->style_image;//商品默认图片
            $styleGoods->status  = $goods['status']? 1: 0;//商品状态
            if(!$styleGoods->save()) {
                throw new \Exception($this->getError($styleGoods));
            }
            $cost_prices[] = $styleGoods->cost_price;
            $goods_num += $styleGoods->status == 1 ? 1 : 0;
        }
        $cost_price_min = min($cost_prices);
        $cost_price_max = max($cost_prices);
        
        $style->goods_num = $goods_num;
        $style->cost_price = $cost_price_min;
        $style->cost_price_min = $cost_price_min;
        $style->cost_price_max = $cost_price_max;
        if(!$style->save(false)) {
            throw new \Exception($this->getError($style));
        }
    } 
    
    

}