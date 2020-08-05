<?php

namespace addons\Purchase\common\forms;

use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use Yii;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
/**
 * 不良返厂单明细 Form
 *
 */
class PurchaseStoneDefectiveGoodsForm extends PurchaseDefectiveGoods
{
    public $xuhaos;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules() , $rules);
    }

    /**
     * 批量获取序号
     */
    public function getXuhaos()
    {
        return StringHelper::explodeIds($this->xuhaos);
    }
}
