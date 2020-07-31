<?php

namespace addons\Warehouse\services;

use addons\Purchase\common\forms\PurchaseGiftGoodsForm;
use addons\Shop\common\models\Style;
use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\enums\GiftStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use yii\db\Expression;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGiftService extends Service
{
    /**
     *
     * 赠品库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'赠品详情','url'=>Url::to(['gift/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'赠品订单','url'=>Url::to(['gift/order','id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'赠品日志','url'=>Url::to(['gift-log/index','id'=>$id,'tab'=>3,'returnUrl'=>$returnUrl])],
        ];
        return $tabList;
    }
    /**
     *
     * 创建批次号
     * @param WarehouseGift $model
     * @param bool $save
     * @throws
     * @return
     */
    public function createGiftSn($model, $save = true)
    {
        //1.供应商
        $gift_sn = $model->supplier->supplier_tag ?? '00';
        //2.款式类型
        $gift_sn .= $model->cate->tag ?? '00';
        //3.数字编号
        $gift_sn .= str_pad($model->id,6,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->gift_sn = $gift_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $gift_sn;
    }

    /**
     * 商品图片
     * @param WarehouseGift $model
     * @throws
     * @return
     */
    public function getStyleImage($model){
        $style = Style::find()->where(['style_sn'=>$model->style_sn])->one();
        $image = $style->style_image ?? '';
        return $image;
    }

    /**
     *
     * 更改赠品库存
     * @param string $gift_sn
     * @param double $adjust_weight
     * @param integer $adjust_type
     * @throws
     *
     */
    public function adjustGiftStock($gift_sn, $adjust_weight, $adjust_type) {

        $adjust_weight = abs(floatval($adjust_weight));

        $model = WarehouseGift::find()->where(['gift_sn'=>$gift_sn])->one();
        if(empty($model)) {
            throw new \Exception("({$gift_sn})赠品编号不存在");
        }elseif ($model->gift_status != GiftStatusEnum::IN_STOCK && $model->gift_status != GiftStatusEnum::SOLD_OUT) {
            throw new \Exception("({$gift_sn})赠品不是库存中");
        }elseif($adjust_type == AdjustTypeEnum::MINUS){
            if($model->gift_weight < $adjust_weight) {
                throw new \Exception("({$gift_sn})赠品库存不足");
            }
        }
        if($adjust_weight <= 0){
            throw new \Exception("({$gift_sn})赠品调整重量不能为0");
        }
        if($adjust_type == AdjustTypeEnum::ADD) {
            $update = ['gift_weight'=>new Expression("gift_weight+{$adjust_weight}"),'gift_status'=>GiftStatusEnum::IN_STOCK];
            $result = WarehouseGift::updateAll($update,new Expression("gift_sn='{$gift_sn}'"));
            if(!$result) {
                throw new \Exception("({$gift_sn})赠品库存变更失败");
            }
        }else{
            $update = ['gift_weight'=>new Expression("gift_weight-{$adjust_weight}")];
            $result = WarehouseGift::updateAll($update,new Expression("gift_sn='{$gift_sn}' and gift_weight>={$adjust_weight}"));
            if(!$result) {
                throw new \Exception("({$gift_sn})赠品库存不足");
            }
            //更新为已售馨
            if($model->gift_weight <= $adjust_weight){
                $result = WarehouseGift::updateAll(['gift_status'=>GiftStatusEnum::SOLD_OUT],new Expression("gift_sn='{$gift_sn}' and gift_weight <= 0"));
            }
        }
        
    }

}