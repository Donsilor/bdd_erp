<?php

namespace addons\Purchase\backend\controllers;



use addons\Purchase\common\enums\ApplyConfirmEnum;
use addons\Purchase\common\models\PurchaseApplyGoods;
use addons\Purchase\common\models\PurchaseApplyGoodsAttribute;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\models\StyleChannel;
use addons\Supply\common\enums\GoodsTypeEnum;
use common\enums\FlowStatusEnum;
use common\enums\TargetTypeEnum;
use common\helpers\PageHelper;
use Yii;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\helpers\ResultHelper;
use common\models\backend\Member;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\enums\ApplyStatusEnum;
use addons\Purchase\common\models\PurchaseApply;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;

/**
 * 
 *
 * Class PurchaseController
 * @package backend\modules\goods\controllers
 */
class PurchaseApplyController extends BaseController
{
    use Curd;
    
    /**
     * @var Purchase
     */     
    public $modelClass = PurchaseApply::class;

    public $targetSType = TargetTypeEnum::PURCHASE_APPLY_S_MENT;
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                    'creator' => ['username'],
                    'auditor' => ['username'],
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(PurchaseApply::tableName().'.id');
            $this->actionExport($queryIds);
        }



        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        
        $model = $this->findModel($id);

        list($current_users_arr, $yw_flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->getTargetYType($model->channel_id), $id);
        list(, $sp_flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->targetSType, $id);
        $flow_detail = array_merge($yw_flow_detail,$sp_flow_detail);


        return $this->render($this->action->id, [
            'model' => $model,
            'flow_detail'=>$flow_detail,
            'tab'=>$tab,
            'tabList'=>Yii::$app->purchaseService->apply->menuTabList($id,$this->returnUrl),
            'returnUrl'=>$this->returnUrl,
        ]);
    }    
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            if($isNewRecord){
               $model->apply_status = ApplyStatusEnum::SAVE;
               $model->apply_sn = SnHelper::createPurchaseApplySn();
               $model->creator_id  = \Yii::$app->user->identity->id;               
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord){
                    //日志
                    $log = [
                            'apply_id' => $model->id,
                            'apply_sn' => $model->apply_sn,
                            'log_type' => LogTypeEnum::ARTIFICIAL,
                            'log_module' => "手动创建单据",
                            'log_msg' => "创建采购申请单,单号：".$model->apply_sn
                    ];
                    Yii::$app->purchaseService->apply->createApplyLog($log);
                }             
                
                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                }
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
    /**
     * 批量创建采购单
     */
    public function actionAjaxPurchase()
    {
        $ids = Yii::$app->request->post('ids');
        if(empty($ids)) {            
            return ResultHelper::json(422,'ids参数不能为空');
        }
        $ids = StringHelper::explodeIds($ids);        

        try {
            $trans = \Yii::$app->trans->beginTransaction();
            //批量创建采购单
            Yii::$app->purchaseService->apply->createPurchase($ids);
            $trans->commit();
            return $this->message("保存成功", $this->redirect(['index']), 'success');
        } catch (\Exception $e){
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        
        
    }
    /** 
     * 申请审核
     * @return mixed
     */
    public function actionAjaxApply(){
        
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $this->returnUrl = \Yii::$app->request->referrer;
        
        if($model->apply_status != ApplyStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect($this->returnUrl), 'error');
        }

        $count = PurchaseApplyGoods::find()->where(['apply_id'=>$model->id])->count();
        if($count == 0){
            return $this->message('没有单据明细', $this->redirect($this->returnUrl), 'error');
        }


        try{
            $trans = Yii::$app->db->beginTransaction();
            //审批流程
            $flow = Yii::$app->services->flowType->createFlow($this->getTargetYType($model->channel_id),$id,$model->apply_sn);
           // Yii::$app->services->flowType->createFlow($this->targetSType,$id,$model->apply_sn);

            $model->apply_status = ApplyStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect($this->returnUrl), 'error');
            }
            //日志
            $log = [
                    'apply_id' => $model->id,
                    'apply_sn' => $model->apply_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "申请审核",
                    'log_msg' => "业务部提交申请,审批编号:".$flow->id,
            ];
            Yii::$app->purchaseService->apply->createApplyLog($log);
            
            $trans->commit();
            return $this->message('操作成功', $this->redirect($this->returnUrl), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }


    }


    /**
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();

                $audit = [
                    'audit_status' =>  $model->audit_status ,
                    'audit_time' => time(),
                    'audit_remark' => $model->audit_remark
                ];
                $flow = \Yii::$app->services->flowType->flowAudit($this->getTargetYType($model->channel_id),$id,$audit);                
                //审批完结或者审批不通过才会走下面
                if($flow->flow_status == FlowStatusEnum::COMPLETE || $flow->flow_status == FlowStatusEnum::CANCEL){
                    $model->audit_time = time();
                    $model->auditor_id = \Yii::$app->user->identity->id;
                    if ($model->audit_status == AuditStatusEnum::PASS) {
                        $model->apply_status = ApplyStatusEnum::CONFIRM;
                    } else {
                        $model->apply_status = ApplyStatusEnum::SAVE;
                    }
                    if (false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }                    
                }
               
                //日志
                $log = [
                        'apply_id' => $model->id,
                        'apply_sn' => $model->apply_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "单据审核",
                        'log_msg' => "业务部审核,审批编号:".$flow->id.",审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                Yii::$app->purchaseService->apply->createApplyLog($log);
                
                if($flow->flow_status == FlowStatusEnum::COMPLETE || $flow->flow_status == FlowStatusEnum::CANCEL){
                    $flowS = Yii::$app->services->flowType->createFlow($this->targetSType,$id,$model->apply_sn);
                    //日志
                    $log = [
                            'apply_id' => $model->id,
                            'apply_sn' => $model->apply_sn,
                            'log_type' => LogTypeEnum::SYSTEM,
                            'log_module' => "单据审核",
                            'log_msg' => "业务部提交申请到商品部,审批编号:".$flowS->id,
                    ];
                    Yii::$app->purchaseService->apply->createApplyLog($log);
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }
        try {
            $current_detail_id = Yii::$app->services->flowType->getCurrentDetailId($this->getTargetYType($model->channel_id), $id);
            list($current_users_arr, $yw_flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->getTargetYType($model->channel_id), $id);
            list(, $sp_flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->targetSType, $id);
            $flow_detail = array_merge($yw_flow_detail,$sp_flow_detail);
        }catch (\Exception $e){
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax('audit', [
            'model' => $model,
            'current_users_arr' => $current_users_arr,
            'flow_detail' => $flow_detail,
            'current_detail_id'=> $current_detail_id
        ]);
    }


    /**
     * ajax 商品部审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionFinalAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->final_audit_status == AuditStatusEnum::SAVE) {
            $model->final_audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();

                $count = PurchaseApplyGoods::find()->where(['and',['=','apply_id',$model->id],['<>','confirm_status',ApplyConfirmEnum::CONFIRM]])->count();
                if($count){
                    throw new \Exception("需先确认采购商品明细，才可审核");
                }

                $audit = [
                    'audit_status' =>  $model->audit_status ,
                    'audit_time' => time(),
                    'audit_remark' => $model->audit_remark
                ];
                $flow = \Yii::$app->services->flowType->flowAudit($this->targetSType,$id,$audit);
                //审批完结或者审批不通过才会走下面
                if($flow->flow_status == FlowStatusEnum::COMPLETE || $flow->flow_status == FlowStatusEnum::CANCEL){

                    $model->final_audit_time = time();
                    $model->final_auditor_id = \Yii::$app->user->identity->id;
                    if ($model->final_audit_status == AuditStatusEnum::PASS) {
                        $model->apply_status = ApplyStatusEnum::AUDITED;

                    }
                    if (false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }
                }
                //日志
                $log = [
                        'apply_id' => $model->id,
                        'apply_sn' => $model->apply_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "单据审核",
                        'log_msg' => "商品部审核,审批编号:".$flow->id.",审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                Yii::$app->purchaseService->apply->createApplyLog($log);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }

        try {
            $current_detail_id = Yii::$app->services->flowType->getCurrentDetailId($this->targetSType, $id);
            list(, $yw_flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->getTargetYType($model->channel_id), $id);
            list($current_users_arr, $sp_flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->targetSType, $id);
            $flow_detail = array_merge($yw_flow_detail,$sp_flow_detail);
        }catch (\Exception $e){
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->renderAjax('audit', [
            'model' => $model,
            'current_users_arr' => $current_users_arr,
            'flow_detail' => $flow_detail,
            'current_detail_id' => $current_detail_id
        ]);
    }


    /**
     * 确认
     * @return mixed
     */
    public function actionAffirm(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->apply_status != ApplyStatusEnum::AUDITED){
            return $this->message('单据不是商品部审核状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = Yii::$app->db->beginTransaction();

            Yii::$app->purchaseService->applyGoods->syncApplyToQiban($model->id);

            $model->apply_status = ApplyStatusEnum::FINISHED;
            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            //日志
            $log = [
                'apply_id' => $id,
                'apply_sn' => $model->apply_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "确认单据",
                'log_msg' => "创建人确认审批结果"
            ];
            Yii::$app->purchaseService->apply->createApplyLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }

    }

    /**
     * 关闭
     * @return mixed
     */
    public function actionDelete(){
        
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->apply_status != ApplyStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        $model->apply_status = ApplyStatusEnum::CANCEL;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        //日志
        $log = [
                'apply_id' => $id,
                'apply_sn' => $model->apply_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "取消单据",
                'log_msg' => "取消采购申请单"
        ];
        Yii::$app->purchaseService->apply->createApplyLog($log);
        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        
    }
    /**
     * 分配跟单人
     * @return mixed|string|\yii\web\Response|string
     */
    public function actionAjaxFollower(){
        
        $id = Yii::$app->request->get('id');
        
        //$this->modelClass = PurchaseFollowerForm::class;
        $model = $this->findModel($id);
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                
                //日志
                $log = [
                        'purchase_id' => $id,
                        'apply_sn' => $model->apply_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "分配跟单人",
                        'log_msg' => "分配跟单人：".$model->follower->username ?? ''
                ];
                Yii::$app->purchaseService->apply->createApplyLog($log);                 
                $trans->commit();  
                
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);                
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }



    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '采购申请单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('采购申请单ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);

        $header = [
            ['商品名称', 'goods_name' , 'text'],
            ['商品类型	', 'goods_type' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['起版号', 'qiban_sn' , 'text'],
            ['起版类型', 'qiban_type' , 'text'],
            ['金托类型', 'jintuo_type' , 'text'],
            ['款式分类	', 'style_cate_name' , 'text'],
            ['产品线', 'product_type_name' ,  'text'],
            ['归属渠道', 'style_channel_name' , 'text'],
            ['商品数量', 'goods_num' , 'text'],
            ['成本价', 'cost_price' ,'text'],
            ['材质', 'material' , 'text'],
            ['金重', 'jinzhong' , 'text'],
            ['手寸', 'finger' , 'text'],
            ['链长	', 'chain_length' , 'text'],
            ['镶口', 'xiangkou' , 'text'],
            ['主石类型	', 'main_stone_type' , 'text'],
            ['主石数量', 'main_stone_num' , 'text'],
            ['主石规格', 'main_stone_spec' ,'text'],
            ['副石1类型', 'second_stone1_type' , 'text'],
            ['副石1数量', 'second_stone1_num' , 'text'],
            ['副石1规格', 'second_stone1_spec' , 'text'],
            ['副石2类型', 'second_stone2_type' , 'text'],
            ['副石2数量', 'second_stone2_num' , 'text'],
            ['证书类型', 'dia_cert_type' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['p.apply_sn','p.follower_id','p.apply_status','m.username','sc.name as style_channel_name','type.name as product_type_name','cate.name as style_cate_name','pg.*'];
        $query = PurchaseApply::find()->alias('p')
            ->innerJoin(PurchaseApplyGoods::tableName().' pg','pg.apply_id=p.id')
            ->leftJoin(Member::tableName().' m','m.id=p.follower_id')
            ->leftJoin(ProductType::tableName().' type','type.id=pg.product_type_id')
            ->leftJoin(StyleChannel::tableName().' sc','sc.id=pg.style_channel_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=pg.style_cate_id')
            ->where(['p.id'=>$ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);

        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            $attr = PurchaseApplyGoodsAttribute::find()->where(['id'=>$list['id']])->asArray()->all();
            $attr = ArrayHelper::map($attr,'attr_id','attr_value');

            $list['goods_type'] = GoodsTypeEnum::getValue($list['goods_type']);
            $list['qiban_type'] = QibanTypeEnum::getValue($list['qiban_type']);
            $list['jintuo_type'] = JintuoTypeEnum::getValue($list['jintuo_type']);

            //材质
            $list['material'] = $attr[AttrIdEnum::MATERIAL] ?? '';
            //金重
            $list['jinzhong'] = $attr[AttrIdEnum::JINZHONG] ?? '';

            //手寸
            $finger_str = '';
            if(isset($attr[AttrIdEnum::FINGER])){
                $finger_str .= $attr[AttrIdEnum::FINGER]."/美号";
                $finger_str .= "<br/>";
            }
            if(isset($attr[AttrIdEnum::PORT_NO])){
                $finger_str .= $attr[AttrIdEnum::PORT_NO]."/港号";
            }
            $list['finger'] = $finger_str;

            //链长
            $list['chain_length'] = $attr[AttrIdEnum::CHAIN_LENGTH] ?? '';
            //镶口
            $list['xiangkou'] = $attr[AttrIdEnum::XIANGKOU] ?? '';

            //主石类型
            $list['main_stone_type'] = $attr[AttrIdEnum::MAIN_STONE_TYPE] ?? '';
            //主石数量
            $list['main_stone_num'] = $attr[AttrIdEnum::MAIN_STONE_NUM] ?? '';
            //主石规格
            $color = $attr[AttrIdEnum::DIA_COLOR] ?? "无";
            $clarity = $attr[AttrIdEnum::DIA_CLARITY] ?? "无";
            $cut = $attr[AttrIdEnum::DIA_CUT] ?? "无";
            $polish = $attr[AttrIdEnum::DIA_POLISH] ?? "无";
            $symmetry = $attr[AttrIdEnum::DIA_SYMMETRY] ?? "无";
            $fluorescence = $attr[AttrIdEnum::DIA_FLUORESCENCE] ?? "无";
            $list['main_stone_spec'] =  $color.'/'.$clarity.'/'.$cut.'/'.$polish.'/'.$symmetry.'/'.$fluorescence;
            //副石1类型
            $list['second_stone1_type'] = $attr[AttrIdEnum::SIDE_STONE1_TYPE] ?? '';
            //副石1数量
            $list['second_stone1_num'] = $attr[AttrIdEnum::SIDE_STONE1_NUM] ?? '';
            //副石1规格
            $color = $attr[AttrIdEnum::SIDE_STONE1_COLOR] ?? "无";
            $clarity = $attr[AttrIdEnum::SIDE_STONE1_CLARITY] ?? "无";
            $list['second_stone1_spec'] = $color.'/'.$clarity;
            //副石2类型
            $list['second_stone2_type'] = $attr[AttrIdEnum::SIDE_STONE2_TYPE] ?? '';
            //副石2数量
            $list['second_stone2_num'] = $attr[AttrIdEnum::SIDE_STONE2_NUM] ?? '';
            //证书类型
            $list['dia_cert_type'] = $attr[AttrIdEnum::DIA_CERT_TYPE] ?? '';

        }
        return [$lists,$total];
    }


    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function getTargetYType($channel_id){
        return Yii::$app->purchaseService->apply->getTargetYType($channel_id);
    }

}
