<?php

namespace addons\Gdzb\services;

use addons\Gdzb\common\models\Follower;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

class FollowerService
{

    /**
     * 供应商下拉
     * @param $where
     * @return array
     */
    public function getDropDown($where=[]){
        $model = Follower::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere($where)
            ->select(['member_id','member_name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'member_id', 'member_name');
    }

}