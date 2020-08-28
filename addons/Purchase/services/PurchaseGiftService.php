<?php

namespace addons\Purchase\services;

use Yii;
use common\components\Service;
use addons\Purchase\common\models\PurchaseGift;
use addons\Purchase\common\models\PurchaseGiftGoods;
use addons\Purchase\common\forms\PurchaseGiftGoodsForm;
use addons\Style\common\models\Style;
use common\enums\StatusEnum;
use common\helpers\Url;

/**
 * Class PurchaseGiftService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseGiftService extends Service
{

    /**
     * 赠品采购单菜单
     * @param int $purchase_id 采购单id
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($purchase_id, $returnUrl = null)
    {
        return [
            1 => ['name' => '基础信息', 'url' => Url::to(['purchase-gift/view', 'id' => $purchase_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            2 => ['name' => '采购商品', 'url' => Url::to(['purchase-gift-goods/index', 'purchase_id' => $purchase_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
            3 => ['name' => '日志信息', 'url' => Url::to(['purchase-gift-log/index', 'purchase_id' => $purchase_id, 'tab' => 3, 'returnUrl' => $returnUrl])]
        ];
    }

    /**
     * 赠品采购单汇总
     * @param int $purchase_id
     */
    public function summary($purchase_id)
    {
        $sum = PurchaseGiftGoods::find()
            ->select(['sum(1) as total_num', 'sum(cost_price*goods_num) as total_cost'])
            ->where(['purchase_id' => $purchase_id, 'status' => StatusEnum::ENABLED])
            ->asArray()->one();
        if ($sum) {
            PurchaseGift::updateAll(['total_num' => $sum['total_num'] / 1, 'total_cost' => $sum['total_cost'] / 1], ['id' => $purchase_id]);
        }
    }

    /**
     * 商品图片
     * @param PurchaseGiftGoodsForm $model
     * @return
     */
    public function getStyleImage($model)
    {
        $style = Style::find()->where(['style_sn' => $model->goods_sn])->one();
        $image = $style->style_image ?? '';
        return $image;
    }
}