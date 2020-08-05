<?php

namespace addons\Supply\backend\controllers;

use addons\Supply\common\models\SupplierFollower;
use common\helpers\ExcelHelper;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class FollowerController extends BaseController
{
    use Curd;
    public $modelClass = SupplierFollower::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['supplier.supplier_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'supplier' => ['supplier_name'],
                'member' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at']);

        $updated_at = $searchModel->updated_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',SupplierFollower::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',SupplierFollower::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',SupplierFollower::tableName().'.status',-1]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
        }


        $supplier_id = Yii::$app->request->get('supplier_id');
        if($supplier_id){
            $tab = Yii::$app->request->get('tab');
            $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['follower/index']));
            $supplier = \addons\Supply\common\models\Supplier::find()->where(['id'=>$supplier_id])->one();
            $dataProvider->query->andWhere(['=',SupplierFollower::tableName().'.supplier_id',$supplier_id]);

            return $this->render('follower', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->supplyService->supplier->menuTabList($supplier_id,$returnUrl),
                'supplier' => $supplier
            ]);
        }else{
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        }


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
            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
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
