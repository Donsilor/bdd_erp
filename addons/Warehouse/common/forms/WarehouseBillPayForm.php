<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 供应商结算 Form
 *
 */
class WarehouseBillPayForm extends WarehouseBill
{
    public $ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules(), $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels(), [

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        if ($this->ids) {
            return StringHelper::explode($this->ids);
        }
        return [];
    }
}
