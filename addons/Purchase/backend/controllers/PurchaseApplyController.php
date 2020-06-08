<?php

namespace addons\Purchase\backend\controllers;



use Yii;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\models\backend\Member;

use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\enums\ApplyStatusEnum;
use addons\Purchase\common\models\PurchaseApply;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\enums\InlayEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\models\Supplier;

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
        /* if(\Yii::$app->request->get('action') === 'export'){
            $dataProvider->setPagination(false);
            $ids = ArrayHelper::map($dataProvider->models,'id');
            $this->actionExport($ids);
        } */



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
        return $this->render($this->action->id, [
            'model' => $model,
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
        $model->apply_status = ApplyStatusEnum::PENDING;
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect($this->returnUrl), 'error');
        }
        return $this->message('操作成功', $this->redirect($this->returnUrl), 'success');

    }


    /**
     * ajax 批量审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if(!$model->audit_status) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->apply_status = ApplyStatusEnum::CONFIRM;
                }else{
                    $model->apply_status = ApplyStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($model->audit_status == AuditStatusEnum::PASS){
                    Yii::$app->purchaseService->apply->syncPurchaseToProduce($id);
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
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
            return $this->message('采购订单ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['p.apply_sn','p.follower_id','p.apply_status','m.username','type.name as product_type_name','cate.name as style_cate_name','pg.*'];

        $list = Purchase::find()->alias('p')
            ->leftJoin(Member::tableName().' m','m.id=p.follower_id')
            ->leftJoin(Supplier::tableName().' s','s.id=p.supplier_id')
            ->leftJoin(PurchaseGoods::tableName().' pg','pg.purchase_id=p.id')
            ->leftJoin(ProductType::tableName().' type','type.id=pg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=pg.style_cate_id')

            ->where(['p.id'=>$ids])
            ->select($select)
            ->asArray()
            ->all();

        foreach ($list as &$val){
            $attr = PurchaseGoodsAttribute::find()->where(['id'=>$val['id']])->asArray()->all();
            $val['attr'] = ArrayHelper::map($attr,'attr_id','attr_value');
        }

        //print_r($list);exit();

        $header = [
            ['订单编号', 'apply_sn' , 'text'],
            ['跟单人', 'username' , 'text'],
            ['订单状态', 'apply_status' , 'selectd',ApplyStatusEnum::getMap()],
            ['商品名称', 'goods_name' , 'text'],
            ['商品编号', 'style_sn' , 'text'],
            ['起版号', 'qiban_sn' , 'text'],
            ['起版类型', 'qiban_type' , 'selectd',QibanTypeEnum::getMap()],
            ['产品线', 'product_type_name' , 'text'],
            ['款式分类', 'style_cate_name' , 'text'],
            ['款式性别', 'style_sex' , 'selectd',StyleSexEnum::getMap()],
            ['金托类型', 'jintuo_type' , 'selectd',JintuoTypeEnum::getMap()],
            ['是否镶嵌', 'is_inlay' , 'selectd',InlayEnum::getMap()],
            ['成本价', 'cost_price' , 'text'],
            ['商品数量', 'goods_num' , 'text'],
            ['手寸', 'id' , 'function',function($model){
                return $model['attr']['38'] ?? '';
            }],
            ['证书号', 'id' , 'function',function($model){
                return $model['attr']['31'] ?? '';
            }],
            ['主成色', 'id' , 'function',function($model){
                return $model['attr']['10'] ?? '';
            }],
            ['主石类型', 'id' , 'function',function($model){
                return $model['attr']['56'] ?? '';
            }],
            ['主石重', 'id' , 'function',function($model){
                return $model['attr']['59'] ?? '';
            }],
            ['主石数', 'id' , 'function',function($model){
                return $model['attr']['65'] ?? '';
            }],
            ['主石单价', 'main_stone_price' , 'text'],

            ['市场价(标签价)', 'id' , 'function',function($model){
                return $model['attr']['2'] ?? '';
            }],
            ['镶口', 'id' , 'function',function($model){
                return $model['attr']['49'] ?? '';
            }],

            ['副石1类型', 'id' , 'function',function($model){
                return $model['attr']['60'] ?? '';
            }],
            ['副石1重', 'id' , 'function',function($model){
                return $model['attr']['44'] ?? '';
            }],
            ['副石1粒数', 'id' , 'function',function($model){
                return $model['attr']['45'] ?? '';
            }],
            ['副石1单价', 'second_stone_price1' , 'text'],
//            ['副石1净度', 'id' , 'function',function($model){
//                return $model['attr']['47'] ?? '';
//            }],
//            ['副石1颜色', 'id' , 'function',function($model){
//                return $model['attr']['46'] ?? '';
//            }],

            ['副石2类型', 'id' , 'function',function($model){
                return $model['attr']['64'] ?? '';
            }],
            ['副石2粒数', 'id' , 'function',function($model){
                return $model['attr']['62'] ?? '';
            }],
            ['副石2重', 'id' , 'function',function($model){
                return $model['attr']['63'] ?? '';
            }],
            ['副石2单价', 'second_stone_price2' , 'text'],
            ['采购备注', 'remark' , 'text'],
            ['石料信息', 'stone_info' , 'text'],
            ['金损', 'gold_loss' , 'text'],
            ['单件银(金)额', 'gold_cost_price' , 'text'],
            ['配件信息', 'parts_info' , 'text'],
            ['加工费/件', 'jiagong_fee' , 'text'],
            ['镶石费/件', 'xiangqian_fee' , 'text'],
            ['工费总额/件', 'gong_fee' , 'text'],
            ['改图费', 'gaitu_fee' , 'text'],
            ['喷蜡费', 'penla_fee' , 'text'],
            ['单件额', 'unit_cost_price' , 'text'],
            ['工厂成本价', 'factory_cost_price' , 'text'],
            ['金重', 'id' , 'function',function($model){
                return $model['attr']['11'] ?? '';
            }],
            ['毛重', 'id' , 'function',function($model){
                return $model['attr']['11'] ?? '';
            }]

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
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


}
