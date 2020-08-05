<?php

namespace addons\Purchase\common\forms;

use common\helpers\StringHelper;
use Yii;
use addons\Purchase\common\models\PurchaseDefective;
/**
 * 采购收货单 Form
 *
 */
class PurchaseDefectiveForm extends PurchaseDefective
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules() , $rules);
    }

}
