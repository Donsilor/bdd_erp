<?php
/**
 * 供应商
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\SupplierFollower;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;


class SupplierService
{

    /**
     * 布产编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($supplier_id,$returnUrl = null)
    {
        return [
            1=>['name'=>'供应商','url'=>Url::to(['supplier/view','id'=>$supplier_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'跟单人','url'=>Url::to(['follower/index','supplier_id'=>$supplier_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }
    /**
     * 下拉
     * @return array
     */
    public function getDropDown(){

        $model = Supplier::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id','supplier_name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'id', 'supplier_name');
    }

    /**
     * 下拉有效值
     * @return array
     */
    public function getValidDropDown(){

        $model = Supplier::find()
            ->where(['status' => StatusEnum::ENABLED, 'audit_status' => AuditStatusEnum::PASS])
            ->select(['id','supplier_name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'id', 'supplier_name');
    }


    /**
     * 工厂跟单人
     * @return array
     */
    public function getFollower($supplier_id){
        $model = SupplierFollower::find()
            ->where(['supplier_id'=>$supplier_id,'status' => StatusEnum::ENABLED])
            ->select(['member_id','member_name'])
            ->asArray()
            ->all();
        $model = ArrayHelper::map($model,'member_id', 'member_name');
        return $model;
    }




}