<?php

namespace addons\Finance\services;

use addons\Finance\common\models\SalesDetail;
use common\components\Service;
use addons\Finance\common\forms\OrderPayForm;
use yii\db\Exception;


/**
 * Class OrderPayService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class SaleDetailService extends Service
{

    
    /**
     * 生成或者更新
     * @param OrderPayForm $form
     */
    public function editSaleDetail($form)
    {
        try{
            if(!isset($form['order_id'])) {
                throw new \Exception("参数order_id不存在");
            }
            if(!isset($form['order_detail_id'])) {
                throw new \Exception("参数order_detail_id不存在");
            }

            $saleDetail = SalesDetail::find()->where(['order_id'=>$form['order_id'],'order_detail_id'=>$form['order_detail_id']])->one();
            if(!$saleDetail){;
                $saleDetail = new SalesDetail();
            }
            $saleDetail->attributes = $form;
            $saleDetail->created_at = time();
            $saleDetail->updated_at = time();
            $saleDetail->creator_id = \Yii::$app->user->id;
            if (false === $saleDetail->save()) {
                throw new \Exception($this->getError($saleDetail));
            }
            return $saleDetail;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
            throw $e;
        }


    }
    
    
}