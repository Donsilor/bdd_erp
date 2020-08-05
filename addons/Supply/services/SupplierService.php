<?php

namespace addons\Supply\services;

use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\SupplierFollower;
use common\models\backend\Member;
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
            2=>['name'=>'跟单人','url'=>Url::to(['follower/index','supplier_id'=>$supplier_id,'tab'=>2,'returnUrl'=>$returnUrl])],
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
            ->select(['id','supplier_name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'id', 'supplier_name');
    }
    /**
     * 工厂跟单人
     * @return array
     */
    public function getFollowers($supplier_id){
        $model = SupplierFollower::find()->alias('a')
            ->leftJoin(Member::tableName().' m','m.id = a.member_id')
            ->where(['a.supplier_id'=>$supplier_id,'a.status' => StatusEnum::ENABLED])
            ->select(['a.member_id','m.username as member_name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'member_id', 'member_name');
    }
}