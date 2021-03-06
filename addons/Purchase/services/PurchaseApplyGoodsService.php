<?php

namespace addons\Purchase\services;

use addons\Purchase\common\enums\ApplyConfirmEnum;
use addons\Purchase\common\models\PurchaseApplyGoods;
use addons\Purchase\common\models\PurchaseApplyGoodsAttribute;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\Style;
use common\enums\AuditStatusEnum;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\Purchase;
use addons\Style\common\enums\IsApply;
use addons\Style\common\enums\QibanSourceEnum;
use addons\Purchase\common\models\PurchaseApply;

/**
 * Class PurchaseGoodsService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseApplyGoodsService extends Service
{    

    public function getStyleImage($model){
        
        if($model->qiban_sn){
            $qiban = Qiban::find()->where(['qiban_sn'=>$model->qiban_sn])->one();
            $image = !empty($qiban->style_image)?explode(',', $qiban->style_image):[];
            $image = $image ? $image[0] :'' ;

        }else{
            $style = Style::find()->where(['style_sn'=>$model->style_sn])->one();
            $image = $style->style_image ?? '';
        }
        return $image;

    }

    /**
     * 
     * @param PurchaseApply $apply
     * @throws \Exception
     */
    public function syncApplyToQiban($apply){

        $apply_goods = PurchaseApplyGoods::find()->where(['apply_id'=>$apply->id])->all();
        foreach ($apply_goods as $model){
            if($model->confirm_status != ApplyConfirmEnum::CONFIRM){
                throw new \Exception("明细{$model->id}没有被确认");
            }
            //起版商品同步到起版表中
            if($model->qiban_type != QibanTypeEnum::NON_VERSION){
                //版式图片同步到商品图片中
                if($model->qiban_type == QibanTypeEnum::NO_STYLE && $model->format_images != ''){
                    $model->goods_images = $model->format_images;
                    $format_images = explode(',', $model->format_images);
                    $model->goods_image = $format_images[0];
                }
                $goods = [
                    'qiban_name' => $model->goods_name,
                    'qiban_type' => $model->qiban_type,
                    'qiban_source_id' => QibanSourceEnum::BUSINESS_APPLI,                    
                    'style_id' => $model->style_id,
                    'style_sn' => $model->style_sn,
                    'style_cate_id' => $model->style_cate_id,
                    'product_type_id' => $model->product_type_id,
                    'jintuo_type' => $model->jintuo_type,
                    'style_channel_id' => $model->style_channel_id,
                    'style_sex' => $model->style_sex,
                    'style_image' => $model->goods_image,
                    'style_images' => $model->goods_images,
                    'cost_price' => $model->cost_price,
                    'goods_num' => $model->goods_num,
                    'is_inlay' => $model->is_inlay,
                    'stone_info' => $model->stone_info,
                    'parts_info' => $model->parts_info,
                    'remark' => $model->remark,
                    'creator_id' => $model->creator_id,
                    'format_sn' => $model->format_sn,
                    'format_images' => $model->format_images,
                    'format_video' => $model->format_video,
                    'format_info' => $model->format_info,
                    'format_remark' => $model->format_remark,
                    'is_apply' => IsApply::Wait,
                    'apply_sn' => $apply->apply_sn,
                    'audit_status' => AuditStatusEnum::PENDING,
                ];
                $goods_attrs = [];
                $attrs = PurchaseApplyGoodsAttribute::find()->where(['id'=>$model->id])->all();
                foreach ($attrs as $attr) {
                    $goods_attrs[] = [
                         'attr_id' => $attr->attr_id,
                         'attr_values' => $attr->attr_value_id == 0 ? $attr->attr_value : $attr->attr_value_id.'',
                         'sort' =>  $attr->sort,
                    ];
                }
                $qiban = \Yii::$app->styleService->qiban->createQiban($goods ,$goods_attrs);
                if($qiban) {
                    $model->qiban_sn = $qiban->qiban_sn;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model),422);
                }

            }
        }

    }

    
}