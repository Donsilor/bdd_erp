<?php

namespace addons\Report\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Report\common\forms\CateSalesForm;

/**
 * Class CateSalesForm
 * @package services\common
 */
class CateSalesService extends Service
{
    /**
     * @throws
     */
    public function getCateSalesReport($uid = null)
    {
        //1.取订单商品数据
        $select = [];
        $query = Order::find()->alias('o')
            ->leftJoin(OrderGoods::tableName() . ' og', 'o.id=og.order_id')
            ->leftJoin(ProductType::tableName() . ' type', 'type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=g.style_cate_id')
            ->select($select);
        $query->andFilterWhere(['o.order_status' => 1]);
        $goods = $query->asArray()->all();
        print_r($goods);die;
    }
}