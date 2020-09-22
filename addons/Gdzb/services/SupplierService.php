<?php

namespace addons\Gdzb\services;

use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Gdzb\common\models\Supplier;
use common\helpers\Url;

class SupplierService
{
    /**
     * 供应商 tab
     * @param int $supplier_id 供应商ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($supplier_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'供应商','url'=>Url::to(['supplier/view','id'=>$supplier_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'订单信息','url'=>Url::to(['order', 'supplier_id'=>$supplier_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }
    /**
     * 供应商下拉
     * @param $where
     * @return array
     */
    public function getDropDown($where=[]){
        $model = Supplier::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere($where)
            ->select(['id','contactor'])
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'id', 'contactor');
    }

}