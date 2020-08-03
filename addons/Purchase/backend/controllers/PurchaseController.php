<?php

namespace addons\Purchase\backend\controllers;


use common\enums\FlowStatusEnum;
use common\enums\TargetType;
use common\enums\TargetTypeEnum;
use common\helpers\PageHelper;
use Yii;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\models\backend\Member;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;
use addons\Purchase\common\forms\PurchaseFollowerForm;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\models\Supplier;

/**
 *
 *
 * Class PurchaseController
 * @package backend\modules\goods\controllers
 */
class PurchaseController extends BaseController
{
    use Curd;

    /**
     * @var Purchase
     */
    public $modelClass = Purchase::class;

    //审批流程
    public $targetType = TargetTypeEnum::PURCHASE_MENT;

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
                'follower' => ['username'],
                'creator' => ['username'],
                'auditor' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(Purchase::tableName().'.id');
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
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>Yii::$app->request->get('tab',1),
            'tabList'=>Yii::$app->purchaseService->purchase->menuTabList($id,$this->returnUrl),
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
        $isNewRecord = $model->isNewRecord;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($isNewRecord){
                    $model->purchase_sn = SnHelper::createPurchaseSn();
                    $model->creator_id  = \Yii::$app->user->identity->id;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord) {
                    //日志
                    $log = [
                            'purchase_id' => $model->id,
                            'purchase_sn' => $model->purchase_sn,
                            'log_type' => LogTypeEnum::ARTIFICIAL,
                            'log_module' => "创建单据",
                            'log_msg' => "创建采购单，单号:".$model->purchase_sn
                    ];
                    Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log);
                    $trans->commit();
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    $trans->commit();
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }        
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 申请审核
     * @return mixed
     */
    public function actionAjaxApply(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->purchase_status != PurchaseStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect($this->returnUrl), 'error');
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            //审批流程
            $flow = Yii::$app->services->flowType->createFlow($this->targetType,$id,$model->purchase_sn);

            $model->purchase_status = PurchaseStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'purchase_id' => $model->id,
                'purchase_sn' => $model->purchase_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "申请审核",
                'log_msg' => "采购单提交申请，审批编号:".$flow->id,
            ];
            Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log);
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
    public function actionClose(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->purchase_status != PurchaseStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        
        try{
            
            $trans = Yii::$app->db->beginTransaction();
            $model->purchase_status = PurchaseStatusEnum::CANCEL;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                    'purchase_id' => $model->id,
                    'purchase_sn' => $model->purchase_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "关闭单据",
                    'log_msg' => "关闭单据"
            ];
            Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
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
        $model->audit_status = AuditStatusEnum::PASS;
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
                $flow = \Yii::$app->services->flowType->flowAudit($this->targetType,$id,$audit);
                //审批完结或者审批不通过才会走下面
                if($flow->flow_status == FlowStatusEnum::COMPLETE || $flow->flow_status == FlowStatusEnum::CANCEL){
                    $model->audit_time = time();
                    $model->auditor_id = \Yii::$app->user->identity->id;
                    if($model->audit_status == AuditStatusEnum::PASS){
                        $model->purchase_status = PurchaseStatusEnum::CONFIRM;
                    }else{
                        $model->purchase_status = PurchaseStatusEnum::SAVE;
                    }
                    if(false === $model->save()){
                        throw new \Exception($this->getError($model));
                    }
                    if($model->audit_status == AuditStatusEnum::PASS){
                        Yii::$app->purchaseService->purchase->syncProduce($id);
                    }
                }

                //日志
                $log = [
                    'purchase_id' => $model->id,
                    'purchase_sn' => $model->purchase_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "单据审核",
                    'log_msg' => "采购申请单审核,审批编号:".$flow->id.",审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log);

                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }
        try {
            $current_detail_id = Yii::$app->services->flowType->getCurrentDetailId($this->targetType, $id);
            list($current_users_arr, $flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->targetType, $id);
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
     * 分配跟单人
     * @return mixed|string|\yii\web\Response|string
     */
    public function actionAjaxFollower(){

        $id = Yii::$app->request->get('id');

        $this->modelClass = PurchaseFollowerForm::class;
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
                    'purchase_id' => $model->id,
                    'purchase_sn' => $model->purchase_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "分配跟单人",
                    'log_msg' => "分配跟单人：".$model->follower->username ?? ''
                ];
                Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log);
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
        $name = '采购订单明细';
        if(!is_object($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('采购订单ID不为空', $this->redirect(['index']), 'warning');
        }

        list($list,) = $this->getData($ids);

        $header = [
//            ['订单编号', 'purchase_sn' , 'text'],
//            ['供应商', 'supplier_name' , 'text'],
//            ['跟单人', 'username' , 'text'],
//            ['采购单状态', 'purchase_status' , 'selectd',PurchaseStatusEnum::getMap()],
            ['款号', 'style_sn' , 'text'],
            ['品类', 'style_cate_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['货品名称', 'goods_name' , 'text'],
            ['件数', 'goods_num' , 'text'],
            ['材质', 'material' , 'text'],
            ['货品外部颜色', 'goods_color' , 'text'],
            ['手寸', 'finger' ,  'text'],
            ['成品尺寸', 'product_size' , 'text'],
            ['主石类型', 'main_stone_type' , 'text'],
            ['主石重ct', 'main_stone_weight' ,'text'],
            ['主石数量(粒)', 'main_stone_num' , 'text'],
            ['石总数(粒）', 'main_stone_num_sum' , 'text'],
            ['石总重ct', 'main_stone_weight_sum' , 'text'],
            ['主石单价', 'main_stone_price' , 'text'],
            ['主石金额', 'main_stone_price_sum' , 'text'],
            ['副石类型', 'second_stone_type1' , 'text'],
            ['副石重ct', 'second_stone_weight' , 'text'],
            ['副石粒数(粒)', 'second_stone_num' ,'text'],
            ['副石总数(粒）', 'second_stone_num_sum' , 'text'],
            ['副石总重ct', 'second_stone_weight_sum' , 'text'],
            ['副石单价', 'second_stone_price1' , 'text'],
            ['副石金额', 'second_stone_price_sum' , 'text'],
            ['石料信息', 'stone_info' , 'text'],
            ['单件连石重(g)', 'single_stone_weight' , 'text'],
            ['连石总重(g)', 'single_stone_weight_sum' , 'text'],
            ['净重/单件(g)', 'gold_weight' , 'text'],
            ['总净重(g)', 'gold_weight_sum' , 'text'],
            ['损耗', 'gold_loss' , 'text'],
            ['银(金)价', 'gold_price' , 'text'],
            ['单件银(金)额', 'gold_cost_price' , 'text'],
            ['金料额', 'gold_amount' , 'text'],
            ['配件信息', 'parts_info' , 'text'],
            ['工艺描述', 'face' ,'text'],
            ['工费/件', 'jiagong_fee' , 'text'],
            ['镶石费/件', 'xiangqian_fee' , 'text'],
            ['工费总额/件', 'gong_fee' , 'text'],
            ['改图费', 'gaitu_fee' , 'text'],
            ['喷蜡费', 'penla_fee' , 'text'],
            ['单件额', 'unit_cost_price' , 'text'],
            ['工厂总额', 'factory_cost_price_sum' , 'text'],
            ['公司成本总额', 'company_unit_cost_sum' , 'text'],
        ];        
        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
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
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total

        ]);
    }

    private function getData($ids){
        $select = ['p.purchase_sn','p.supplier_id','p.follower_id','p.purchase_status','m.username','s.supplier_name','type.name as product_type_name','cate.name as style_cate_name','pg.*'];
        $query = Purchase::find()->alias('p')
            ->innerJoin(PurchaseGoods::tableName().' pg','pg.purchase_id=p.id')
            ->leftJoin(Member::tableName().' m','m.id=p.follower_id')
            ->leftJoin(Supplier::tableName().' s','s.id=p.supplier_id')
            ->leftJoin(ProductType::tableName().' type','type.id=pg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=pg.style_cate_id')
            ->where(['p.id'=>$ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'goods_num_count' => 0,
            'main_stone_weight_count' => 0,
            'main_stone_num_count' => 0,
            'main_stone_num_sum_count' => 0,
            'main_stone_weight_sum_count' => 0,
            'main_stone_price_sum_count' => 0,
            'second_stone_weight_count' => 0,
            'second_stone_num_count' => 0,
            'second_stone_num_sum_count' => 0,
            'second_stone_weight_sum_count' => 0,
            'second_stone_price_sum_count' => 0,
            'jiagong_fee_count' => 0,
            'xiangqian_fee_count' => 0,
            'gong_fee_count' => 0,
            'gaitu_fee_count' => 0,
            'penla_fee_count' => 0,
            'unit_cost_price_count' => 0,
            'factory_cost_price_sum_count' => 0,
            'company_unit_cost_sum_count' => 0,
        ];
        foreach ($lists as &$list){
            $attr = PurchaseGoodsAttribute::find()->where(['id'=>$list['id']])->asArray()->all();
            $attr = ArrayHelper::map($attr,'attr_id','attr_value');
            //材质
            $list['material'] = $attr[AttrIdEnum::MATERIAL] ?? 0;
            //手寸
            $list['finger'] = $attr[AttrIdEnum::FINGER] ?? 0;
            //工艺描述
            $list['face'] = $attr[AttrIdEnum::FACEWORK] ?? 0;
            //主石
            $list['main_stone_type'] = $attr[AttrIdEnum::MAIN_STONE_TYPE] ?? 0;
            $list['main_stone_num'] = $attr[AttrIdEnum::MAIN_STONE_NUM] ?? 0;
            $list['main_stone_num'] = empty($list['main_stone_num'])? 0: $list['main_stone_num']; //值为空默认0
            $list['main_stone_num_sum'] = $list['main_stone_num'] * $list['goods_num'];
            $list['main_stone_weight'] = $attr[AttrIdEnum::DIA_CARAT] ?? 0;
            $list['main_stone_weight'] = empty($list['main_stone_weight'])? 0: $list['main_stone_weight']; //值为空默认0
            $list['main_stone_weight_sum'] = $list['main_stone_weight'] * $list['main_stone_num_sum'];
            $list['main_stone_price_sum'] = $list['main_stone_price'] * $list['main_stone_num_sum'];

            //副石
            $list['second_stone_type1'] = $attr[AttrIdEnum::SIDE_STONE1_TYPE] ?? 0;
            $list['second_stone_num'] = $attr[AttrIdEnum::SIDE_STONE1_NUM] ?? 0;
            $list['second_stone_num'] = empty($list['second_stone_num'])? 0: $list['second_stone_num'];//值为空默认0
            $list['second_stone_num_sum'] = $list['second_stone_num'] * $list['goods_num'];
            $list['second_stone_weight'] = $attr[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? 0;
            $list['second_stone_weight'] = empty($list['second_stone_weight'])? 0: $list['second_stone_weight'];//值为空默认0
            $list['second_stone_weight_sum'] = $list['second_stone_weight'] * $list['second_stone_num_sum'];
            $list['second_stone_price_sum'] = $list['second_stone_price1'] * $list['second_stone_num_sum'];

            //连石总重(g)
            $list['single_stone_weight_sum'] = $list['single_stone_weight'] * $list['goods_num'];

            //净重/单件(g) 总净重(g) ---金重
            $list['gold_weight'] = isset($list['gold_weight']) && !empty($list['gold_weight']) ?? 0;
            $list['gold_weight_sum'] = $list['gold_weight'] * $list['goods_num'];

            //工厂总额
            $list['factory_cost_price_sum'] = $list['factory_total_price'];
            //公司成本总额
            $list['company_unit_cost_sum'] = $list['company_total_price'];

            //统计
            $total['goods_num_count'] += $list['goods_num'];  //件数
            $total['main_stone_weight_count'] += $list['main_stone_weight']; //主石石重
            $total['main_stone_num_count'] += $list['main_stone_num']; //主石数量
            $total['main_stone_num_sum_count'] += $list['main_stone_num_sum']; //主石总数(粒）
            $total['main_stone_weight_sum_count'] += $list['main_stone_weight_sum']; //主石总重ct
            $total['main_stone_price_sum_count'] += $list['main_stone_price_sum']; //主石金额
            $total['second_stone_weight_count'] += $list['second_stone_weight']; //副石石重
            $total['second_stone_num_count'] += $list['second_stone_num']; //副石数量
            $total['second_stone_num_sum_count'] += $list['second_stone_num_sum']; //副石总数(粒）
            $total['second_stone_weight_sum_count'] += $list['second_stone_weight_sum']; //副石总重ct
            $total['second_stone_price_sum_count'] += $list['second_stone_price_sum']; //副石金额
            $total['jiagong_fee_count'] += $list['jiagong_fee']; //工费
            $total['xiangqian_fee_count'] += $list['xiangqian_fee']; //镶石费
            $total['gong_fee_count'] += $list['gong_fee']; //工费总额
            $total['gaitu_fee_count'] += $list['gaitu_fee']; //改图费
            $total['penla_fee_count'] += $list['penla_fee']; //喷蜡费
            $total['unit_cost_price_count'] += $list['unit_cost_price']; //单件额
            $total['factory_cost_price_sum_count'] += $list['factory_cost_price_sum']; //工厂总额
            $total['company_unit_cost_sum_count'] += $list['company_unit_cost_sum']; //公司成本总额
        }
        return [$lists,$total];
    }


}
