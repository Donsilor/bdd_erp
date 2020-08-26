<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\helpers\Url;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillPay;
use addons\Warehouse\common\enums\PayMethodEnum;
use addons\Warehouse\common\enums\PayTaxEnum;
use yii\db\Exception;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class BillPayController extends BaseController
{
    use Curd;

    /**
     * @var WarehouseBillPay
     */
    public $modelClass = WarehouseBillPay::class;

    /**
     * 首页
     *
     * @return string
     * @throws
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-pay/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['log_msg'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'supplier' => ['supplier_name']
            ]

        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);

        $bill = WarehouseBill::find()->where(['id' => $bill_id])->one();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id, $bill->bill_type, $returnUrl),
            'bill' => $bill,
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
        $bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id) ?? new WarehouseBillPay();
        $bill = WarehouseBill::find()->where(['id' => $bill_id])->one();
        $model->supplier_id = $bill->supplier_id;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            $model->bill_id = $bill_id;

            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model->pay_method = PayMethodEnum::TALLY;
        $model->pay_tax = PayTaxEnum::NO_TAX;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'bill' => $bill,
        ]);
    }

    /**
     * 删除/关闭/取消
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        $model = WarehouseBillPay::find()->where(['id' => $id])->one();
        if (false === $model->delete()) {
            return $this->message("删除失败", $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        \Yii::$app->getSession()->setFlash('success', '删除成功');
        return $this->redirect(\Yii::$app->request->referrer);
    }
}