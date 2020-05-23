<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\SnHelper;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillWForm;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class WarehouseBillWController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBill::class;
    public $billType = BillTypeEnum::BILL_TYPE_W;
    /**
     * Lists all StyleChannel models.
     * @return mixed
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
                        
                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at','created_at']);
        
        $created_at = $searchModel->created_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type',$this->billType]);
        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
        
        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
        }
        
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
        
        
    }
    
    /**
     * ajax编辑/创建 盘点单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        
        if($model->isNewRecord){
            $model->bill_type = $this->billType; 
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {            
            if($model->isNewRecord){               
                $model->bill_no   = SnHelper::createBillSn($this->billType);
                $model->creator_id  = \Yii::$app->user->identity->id;
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();               

                Yii::$app->warehouseService->billW->createBill($model);
                
                $trans->commit();                
                return $this->message('保存成功',$this->redirect(Yii::$app->request->referrer),'success');
                
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
     * 盘点
     * @return mixed
     */
    public function actionPandian()
    {
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = WarehouseBillWForm::class;
        $model = $this->findModel($id);      
        
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            
            //$model->va
            
            return $this->redirect(['index']);
        }
        
        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * 导出列表
     * @param unknown $dataProvider
     * @return boolean
     */
    private function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
                ['ID', 'id'],
                ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '盘点数据导出_' . time());
        
    }
    
    
}
