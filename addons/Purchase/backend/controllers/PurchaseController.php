<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\InlayEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\enums\GoodsTypeEnum;
use addons\Supply\common\models\Supplier;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\models\backend\Member;
use function PHPSTORM_META\map;
use Yii;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;
use addons\Purchase\common\forms\PurchaseFollowerForm;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;

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
    /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::GOODS;

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
        $tab = Yii::$app->request->get('tab',1);

        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>Yii::$app->purchaseService->purchase->menuTabList($id,$this->purchaseType,$this->returnUrl),
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
                $model->purchase_sn = SnHelper::createPurchaseSn();
                $model->creator_id  = \Yii::$app->user->identity->id;
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
                return $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
            if($isNewRecord) {
                return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
            }else{
                return $this->redirect(Yii::$app->request->referrer);
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

        if($model->purchase_status != PurchaseStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect($this->returnUrl), 'error');
        }
        $model->purchase_status = PurchaseStatusEnum::PENDING;
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect($this->returnUrl), 'error');
        }
        return $this->message('操作成功', $this->redirect($this->returnUrl), 'success');

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
        $model->purchase_status = PurchaseStatusEnum::CANCEL;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        //日志
        $log = [
            'purchase_id' => $id,
            'purchase_sn' => $model->purchase_sn,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_module' => "关闭单据",
            'log_msg' => "关闭单据"
        ];
        Yii::$app->purchaseService->purchase->createPurchaseLog($log);
        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');

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
                    $model->purchase_status = PurchaseStatusEnum::CONFIRM;
                }else{
                    $model->purchase_status = PurchaseStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($model->audit_status == AuditStatusEnum::PASS){
                    Yii::$app->purchaseService->purchase->syncPurchaseToProduce($id);
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
                    'purchase_sn' => $model->purchase_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "分配跟单人",
                    'log_msg' => "分配跟单人：".$model->follower->username ?? ''
                ];
                Yii::$app->purchaseService->purchase->createPurchaseLog($log);
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

        $list = $this->getData($ids);

        $header = [
            ['订单编号', 'purchase_sn' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['跟单人', 'username' , 'text'],
            ['采购单状态', 'purchase_status' , 'selectd',PurchaseStatusEnum::getMap()],
            ['款号', 'style_sn' , 'text'],
            ['产品分类', 'style_cate_name' , 'text'],
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
            ['副石1类型', 'second_stone_type1' , 'text'],
            ['副石1重ct', 'second_stone_weight' , 'text'],
            ['副石1粒数(粒)', 'second_stone_num' ,'text'],
            ['副石总数(粒）', 'second_stone_num_sum' , 'text'],
            ['副石总重ct', 'second_stone_weight_sum' , 'text'],
            ['副石1单价', 'second_stone_price1' , 'text'],
            ['副石金额', 'second_stone_price_sum' , 'text'],
            ['石料信息', 'stone_info' , 'text'],
            ['单件连石重(g)', 'single_stone_weight' , 'text'],
            ['连石总重(g)', 'single_stone_weight_sum' , 'text'],
            ['净重/单件(g)', 'gold_weight' , 'text'],
            ['总净重(g)', 'gold_weight_sum' , 'text'],
            ['损耗', 'gold_loss' , 'text'],
            ['银(金)价', 'gold_price' , 'text'],
            ['单件银(金)额', 'gold_cost_price' , 'text'],
            ['金料额', 'cost_price' , 'text'],
            ['配件信息', 'parts_info' , 'text'],
            ['工艺描述', 'face' ,'text'],
            ['加工费/件', 'jiagong_fee' , 'text'],
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
        $lists = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists
        ]);
    }

    private function getData($ids){
        $select = ['p.purchase_sn','p.supplier_id','p.follower_id','p.purchase_status','m.username','s.supplier_name','type.name as product_type_name','cate.name as style_cate_name','pg.*'];

        $list = Purchase::find()->alias('p')
            ->innerJoin(PurchaseGoods::tableName().' pg','pg.purchase_id=p.id')
            ->leftJoin(Member::tableName().' m','m.id=p.follower_id')
            ->leftJoin(Supplier::tableName().' s','s.id=p.supplier_id')
            ->leftJoin(ProductType::tableName().' type','type.id=pg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=pg.style_cate_id')
            ->where(['p.id'=>$ids])
            ->select($select)
            ->asArray()
            ->all();

        foreach ($list as &$val){
            $attr = PurchaseGoodsAttribute::find()->where(['id'=>$val['id']])->asArray()->all();
            $attr = ArrayHelper::map($attr,'attr_id','attr_value');
            //材质
            $val['material'] = $attr[AttrIdEnum::MATERIAL] ?? 0;
            //手寸
            $val['finger'] = $attr[AttrIdEnum::FINGER] ?? 0;
            //工艺描述
            $val['face'] = $attr[AttrIdEnum::FACEWORK] ?? 0;
            //主石
            $val['main_stone_type'] = $attr[AttrIdEnum::MAIN_STONE_TYPE] ?? 0;
            $val['main_stone_num'] = $attr[AttrIdEnum::MAIN_STONE_NUM] ?? 0;
            $val['main_stone_num'] = empty($val['main_stone_num'])? 0: $val['main_stone_num']; //值为空默认0
            $val['main_stone_num_sum'] = $val['main_stone_num'] * $val['goods_num'];
            $val['main_stone_weight'] = $attr[AttrIdEnum::DIA_CARAT] ?? 0;
            $val['main_stone_weight'] = empty($val['main_stone_weight'])? 0: $val['main_stone_weight']; //值为空默认0
            $val['main_stone_weight_sum'] = $val['main_stone_weight'] * $val['main_stone_num_sum'];
            $val['main_stone_price_sum'] = $val['main_stone_price'] * $val['main_stone_num_sum'];

            //副石
            $val['second_stone_type1'] = $attr[AttrIdEnum::SIDE_STONE1_TYPE] ?? 0;
            $val['second_stone_num'] = $attr[AttrIdEnum::SIDE_STONE1_NUM] ?? 0;
            $val['second_stone_num'] = empty($val['second_stone_num'])? 0: $val['second_stone_num'];//值为空默认0
            $val['second_stone_num_sum'] = $val['second_stone_num'] * $val['goods_num'];
            $val['second_stone_weight'] = $attr[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? 0;
            $val['second_stone_weight'] = empty($val['second_stone_weight'])? 0: $val['second_stone_weight'];//值为空默认0
            $val['second_stone_weight_sum'] = $val['second_stone_weight'] * $val['second_stone_num_sum'];
            $val['second_stone_price_sum'] = $val['second_stone_price1'] * $val['second_stone_num_sum'];

            //连石总重(g)
            $val['single_stone_weight_sum'] = $val['single_stone_weight'] * $val['goods_num'];

            //净重/单件(g) 总净重(g) ---金重
            $val['gold_weight'] = $val[AttrIdEnum::JINZHONG] ?? 0;
            $val['gold_weight_sum'] = $val['gold_weight'] * $val['goods_num'];

            //工厂总额
            $val['factory_cost_price_sum'] = $val['factory_cost_price'] * $val['goods_num'];
            //公司成本总额
            $val['company_unit_cost_sum'] = $val['company_unit_cost'] * $val['goods_num'];
        }
        return $list;
    }


}
