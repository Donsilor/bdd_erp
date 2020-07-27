<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\forms\WarehouseGoldBillCGoodsForm;
use common\helpers\Url;
use common\helpers\ExcelHelper;

/**
 * 领料单
 */
class GoldBillCGoodsController extends GoldBillGoodsController
{
    use Curd;
    public $modelClass = WarehouseGoldBillCGoodsForm::class;
    public $billType = GoldBillTypeEnum::GOLD_C;
    /**
     * 列表
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = \Yii::$app->request->get('bill_id');
        $tab = \Yii::$app->request->get('tab',2);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['gold-bill-c-goods/index', 'bill_id'=>$bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoldBillGoods::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoldBillGoods::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);

        $gold_sn = \Yii::$app->request->get('gold_sn', null);
        if($gold_sn){
            $dataProvider->query->andWhere(['=', 'gold_sn', $gold_sn]);
        }
        $dataProvider->query->andWhere(['>',WarehouseGoldBillGoods::tableName().'.status',-1]);
        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
        }
        $bill = WarehouseGoldBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->goldBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehouseGoldBillGoods();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message('保存成功',$this->redirect(Yii::$app->request->referrer),'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 编辑明细
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-bill-c-goods/index', 'bill_id'=>$bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseGoldBillGoods::tableName().'.status',-1]);

        $bill = WarehouseGoldBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList' => \Yii::$app->warehouseService->goldBill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }

    public function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '数据导出_' . time());

    }

}
