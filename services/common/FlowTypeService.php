<?php

namespace services\common;
use common\components\Service;
use common\enums\FlowMethodEnum;
use common\enums\FlowStatus;
use common\enums\FlowStatusEnum;
use common\enums\StatusEnum;
use common\models\common\Flow;
use common\models\common\FlowDetails;
use common\models\common\FlowType;


/**
 * Class FlowTypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class FlowTypeService extends Service
{
    /***
     * 创建具体审批流程
     */
    public function createFlow($flow_type_id,$target_id,$target_no=null){


        $flow_type = FlowType::find()->where(['id'=>$flow_type_id,'status' => StatusEnum::ENABLED])->one();
        if(empty($flow_type)){
            throw new \Exception('审批流程不存在');
        }
        $users = $flow_type->users;
        $users_arr = explode(',',$users);
        if(empty($users_arr)){
            throw new \Exception('请查看审批人员是否添加');
        }

        $flow = new Flow();
        $flow->flow_name = $flow_type->name;
        $flow->cate = $flow_type->cate;
        $flow->flow_type = $flow_type_id;
        $flow->flow_method = $flow_type->method;
        $flow->target_id = $target_id;
        $flow->target_no = $target_no;
        if($flow->flow_method == FlowMethodEnum::IN_ORDER){
            $flow->current_users = $users_arr[0];
        }else{
            $flow->current_users = $users;
        }
        $flow->flow_status = FlowStatusEnum::GO_ON;
        $flow->flow_total = count($users_arr);
        $flow->creator_id = \Yii::$app->user->identity->getId();
        $flow->created_at = time();
        if(false === $flow->save()){
            throw new \Exception($this->getError($flow));
        }


        foreach ($users_arr as $user_id){
            $flow_detail = new FlowDetails();
            $flow_detail->flow_id = $flow->id;
            $flow_detail->user_id = $user_id;
            if(false === $flow_detail->save()){
                throw new \Exception($this->getError($flow_detail));
            }

        }

        return $flow ;


    }


}