<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillTForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\enums\AuditStatusEnum;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\web\UploadedFile;

/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillTController extends BaseController
{

    use Curd;
    public $modelClass = WarehouseBillTForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_T;


    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, ['created_at', 'audit_time']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', Warehousebill::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', Warehousebill::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }
        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=', Warehousebill::tableName() . '.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', Warehousebill::tableName() . '.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)]);//结束时间
        }
        $dataProvider->query->andWhere(['>', Warehousebill::tableName() . '.status', -1]);
        $dataProvider->query->andWhere(['=', Warehousebill::tableName() . '.bill_type', $this->billType]);

        //导出
        if (\Yii::$app->request->get('action') === 'export') {
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list, 'id');
            $this->actionExport($ids);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                if ($isNewRecord) {
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                }
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                if ($isNewRecord) {
                    $gModel = new WarehouseBillTGoodsForm();
                    $gModel->bill_id = $model->id;
                    $gModel->file = UploadedFile::getInstance($model, 'file');
                    if (!empty($gModel->file) && isset($gModel->file)) {
                        \Yii::$app->warehouseService->billT->uploadGoods($gModel);
                    }
                    $log_msg = "创建其它入库单{$model->bill_no}";
                } else {
                    $log_msg = "修改其它入库单{$model->bill_no}";
                }
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它入库单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                \Yii::$app->warehouseService->billT->warehouseBillTSummary($model->id);
                $trans->commit();

                if ($isNewRecord) {
                    \Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['bill-t-goods/index', 'bill_id' => $model->id]);
                    //return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                } else {
                    \Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(\Yii::$app->request->referrer);
                }
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab', 1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t/index', 'id' => $id]));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($id, $this->billType, $returnUrl),
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        if ($model->bill_status != BillStatusEnum::SAVE) {
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if ($model->goods_num <= 0) {
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->warehouseService->billT->syncUpdatePriceAll($model);
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其它入库单',
                'log_msg' => '单据提审'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

    }

    /**
     *
     * ajax收货单审核
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->billL->auditBillL($model);
                //日志
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它入库单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message("审核失败:" . $e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * 同步更新价格
     * @param $id
     * @return mixed
     */
    /* public function actionSyncUpdatePrice($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();

            \Yii::$app->warehouseService->billT->syncUpdatePriceAll($id);

            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billT->warehouseBillTSummary($id);
            if (false === $res) {
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '更新成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    } */

    /**
     *
     * 取消单据
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionCancel($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据取消',
                'log_msg' => '取消其它收货单'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success', '操作成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 删除单据
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            
            if(false === WarehouseBillGoodsL::deleteAll(['bill_id' => $id])){
                throw new \Exception("单据明细删除失败");
            }            
            if (false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据删除',
                'log_msg' => '删除其它入库单'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success', '操作成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function actionExport($ids = null)
    {
        $name = '入库单明细';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['w.bill_no', 'w.bill_type', 'w.bill_status', 'g.goods_id', 'g.finger', 'g.gross_weight', 'g.main_stone_type', 'g.diamond_carat', 'g.main_stone_num',
            'g.second_stone_type1', 'g.second_stone_num1', 'g.second_stone_weight1', 'g.second_stone_price1', 'wg.warehouse_id', 'wg.style_sn', 'wg.goods_name', 'wg.goods_num', 'wg.put_in_type'
            , 'wg.material', 'wg.gold_weight', 'wg.gold_loss', 'wg.diamond_carat', 'wg.diamond_color', 'wg.diamond_clarity',
            'wg.cost_price', 'wg.diamond_cert_id', 'wg.goods_remark', 'type.name as product_type_name', 'cate.name as style_cate_name'];
        $list = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName() . " wg", 'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName() . ' g', 'g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName() . ' type', 'type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select)->asArray()->all();
        $header = [
            ['款号', 'style_sn', 'text'],
            ['仓库', 'warehouse_id', 'selectd', \Yii::$app->warehouseService->warehouse::getDropDownForAll()],
            ['商品类型', 'style_cate_name', 'selectd', BillStatusEnum::getMap()],
            ['产品分类', 'product_type_name', 'text'],
            ['材质', 'material', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['material']);
            }],
            ['手寸', 'finger', 'text'],
//            ['尺寸（规格）', 'finger' , 'text'],
            ['件数', 'goods_num', 'text'],
//            ['货重', 'gross_weight' , 'text'],
            ['金重', 'gold_weight', 'text'],
            ['损耗', 'gold_loss', 'text'],
            ['含耗重', 'gross_weight', 'text'],
//            ['金价', '' , 'text'],
//            ['金料额', '' , 'text'],
            ['石号', 'main_stone_type', 'function', function ($model) {
                return Yii::$app->attr->valueName($model->main_stone_type ?? '');
            }],
            ['粒数', 'main_stone_num', 'text'],
            ['主石重', 'diamond_carat', 'text'],
//            ['主石单价	', '' , 'text'],
            ['副石号', 'second_stone_type1', 'function', function ($model) {
                return Yii::$app->attr->valueName($model->second_stone_type1 ?? '');
            }],
            ['副石粒数', 'second_stone_num1', 'text'],
            ['副石重量', 'second_stone_weight1', 'text'],
            ['副石单价', 'second_stone_price1', 'text'],
//            ['加工费', 'second_stone_price1' , 'text'],
//            ['起版费', 'second_stone_price1' , 'text'],
//            ['镶工费', 'second_stone_price1' , 'text'],
//            ['喷拉砂', 'second_stone_price1' , 'text'],
//            ['分色分件', 'second_stone_price1' , 'text'],
//            ['总金额', 'second_stone_price1' , 'text'],
            ['备注', 'goods_remark', 'text'],

        ];
        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }

}
