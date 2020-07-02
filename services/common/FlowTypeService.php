<?php

namespace services\common;
use common\components\Service;
use common\enums\AuditStatusEnum;
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

    public function getFlowDetals($flow_type_id,$target_id){
        $flow = Flow::find()->where(['flow_type'=>$flow_type_id,'target_id' => $target_id])->one();
        if(empty($flow)){
            throw new \Exception('参数错误');
        }
        $current_users = $flow->current_users;
        $current_users_arr = explode(',',$current_users);
        $flow_detail = FlowDetails::find()->where(['flow_id'=>$flow->id])->all();
        return [$current_users_arr ,$flow_detail];
    }

    public function flowAudit($flow_type_id,$target_id, $audit){
        $user_id = \Yii::$app->user->identity->id;
        $flow = Flow::find()->where(['flow_type'=>$flow_type_id,'target_id' => $target_id])->one();
        if(empty($flow)){
            throw new \Exception('参数错误');
        }

        //同步流程明细
        $flow_detail = FlowDetails::find()->where(['flow_id'=>$flow->id,'user_id'=>$user_id])->one();
        $flow_detail->attributes = $audit;
        if(false === $flow_detail->save()){
            throw new \Exception($this->getError($flow_detail));
        }

        if($audit['audit_status'] == AuditStatusEnum::UNPASS){
            $flow->flow_status = FlowStatusEnum::COMPLETE;
        }

        $flow->flow_num = FlowDetails::find()->where(['flow_id'=>$flow->id, 'audit_status'=>AuditStatusEnum::PASS])->count();

        $current_flow_detail = FlowDetails::find()->where(['flow_id'=>$flow->id, 'audit_status'=>AuditStatusEnum::SAVE])->all();
        if(empty($current_flow_detail)){
            $flow->current_users = '';
            //没有未审批的人，表示审批完结
            $flow->flow_status = FlowStatusEnum::COMPLETE;
        }else{
            $flow_method = $flow->flow_method;

            if($flow_method == FlowMethodEnum::IN_ORDER){
                //有序取下一个
                $flow->current_users = (string)$current_flow_detail[0]['user_id'];
            }else{
                //无序显示所有的未审批的
                $user_id_arr = array_values(array_column($current_flow_detail,'user_id'));
                $flow->current_users = explode(',',$user_id_arr);
            }

        }


        if(false === $flow->save()){
            throw new \Exception($this->getError($flow));
        }


        return $flow;


    }




}