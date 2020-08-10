<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseTempletBill;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
use addons\Warehouse\common\forms\WarehouseTempletBillLForm;
use addons\Warehouse\common\forms\WarehouseTempletBillLGoodsForm;
use addons\Warehouse\common\enums\TempletBillStatusEnum;
use addons\Warehouse\common\enums\TempletBillTypeEnum;
use common\enums\AuditStatusEnum;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\SnHelper;
use common\helpers\Url;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class TempletBillLController extends TempletBillController
{
    use Curd;
    public $modelClass = WarehouseTempletBillLForm::class;
    public $billType = TempletBillTypeEnum::TEMPLET_L;

    /**
     * Lists all StyleChannel models.
     * @return mixed
     * @throws
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
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', WarehouseTempletBill::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseTempletBill::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }

        $dataProvider->query->andWhere(['>', WarehouseTempletBill::tableName() . '.status', -1]);
        $dataProvider->query->andWhere(['=', WarehouseTempletBill::tableName() . '.bill_type', $this->billType]);

        //导出
        if (\Yii::$app->request->get('action') === 'export') {
            $queryIds = $dataProvider->query->select(WarehouseTempletBill::tableName() . '.id');
            $this->actionExport($queryIds);
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
        $model = $model ?? new WarehouseTempletBillLForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                if ($model->isNewRecord) {
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                    $model->bill_status = TempletBillStatusEnum::SAVE;
                }
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
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
        $bill_id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab', 1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['templet-bill-l/index', 'bill_id' => $bill_id]));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseTempletBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->templetBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehouseTempletBill();
        if ($model->bill_status != TempletBillStatusEnum::SAVE) {
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $goods = WarehouseTempletBillGoods::findOne(['bill_id' => $id]);
        if (!$goods) {
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = TempletBillStatusEnum::PENDING;
        $model->audit_status = AuditStatusEnum::PENDING;
        if (false === $model->save()) {
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 入库单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseTempletBillLForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try {
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->templetL->auditTempletL($model);

                $trans->commit();

                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * 取消
     * @param $id
     * @throws
     * @return mixed
     */
    public function actionCancel($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = TempletBillStatusEnum::CANCEL;
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            \Yii::$app->getSession()->setFlash('success', '取消成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $res = WarehouseTempletBillGoods::deleteAll(['bill_id' => $id]);
            if (false === $res) {
                throw new \Exception("删除明细失败");
            }
            if (false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            \Yii::$app->getSession()->setFlash('success', '删除成功');
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
     * @throws
     */
    public function actionExport($ids = null)
    {
        $name = '样板入库单明细';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['单据编号', 'bill_no', 'text'],
            ['金料类型', 'gold_type', 'text'],
            ['名称', 'gold_name', 'text'],
            ['款号', 'style_sn', 'text'],
            ['重量(g)', 'gold_weight', 'text'],
            ['价格	', 'gold_price', 'text'],
            ['备注', 'remark', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }

    private function getData($ids)
    {
        $select = ['wg.*', 'w.bill_no', 'w.to_warehouse_id', 'w.bill_status'];
        $query = WarehouseTempletBillLForm::find()->alias('w')
            ->leftJoin(WarehouseTempletBillLGoodsForm::tableName() . " wg", 'w.id=wg.bill_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list) {
            $list['gold_type'] = \Yii::$app->attr->valueName($list['gold_type']);
        }
        return [$lists, $total];
    }

    /**
     * 单据打印
     * @return string
     * @throws
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }


}
