<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\SalesReturn;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;

/**
 * 退款单 Form
 */
class ReturnForm extends SalesReturn
{
    public $ids;
    public $to_warehouse_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['to_warehouse_id'], 'integer'],
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'to_warehouse_id' => '入库仓库',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }

}
