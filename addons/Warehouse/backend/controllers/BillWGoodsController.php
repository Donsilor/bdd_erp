<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\models\WarehouseBillGoodsW;
use addons\Warehouse\common\forms\ImportBillWForm;
use yii\web\UploadedFile;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillWGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
    public $billType = BillTypeEnum::BILL_TYPE_W;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $bill = WarehouseBillWForm::find()->where(['id'=>$bill_id])->one();
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' =>  $this->getPageSize(15),
                'relations' => [
                     "goodsW"=> ['should_num','actual_num',"adjust_status"]
                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',WarehousebillGoods::tableName().'.bill_id',$bill_id]);
        //$dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',PandianStatusEnum::SAVE]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'bill'=>$bill,
                'tab' =>Yii::$app->request->get('tab',2),
                'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id,$this->billType,$this->returnUrl),
                'returnUrl'=>$this->returnUrl
        ]);        
        
    } 
    /**
     * 编辑盘点列表
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $bill = WarehouseBillWForm::find()->where(['id'=>$bill_id])->one();
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'updated_at' => SORT_DESC
                ],
                'pageSize' =>  $this->getPageSize(15),
                'relations' => [
                        "goodsW"=> ['should_num','actual_num',"adjust_status"],
                ]
        ]);
        
        $dataProvider = $searchModel
        ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',WarehousebillGoods::tableName().'.bill_id',$bill_id]);
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',PandianStatusEnum::SAVE]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'bill'=>$bill,
                'tab' =>Yii::$app->request->get('tab',2),
                'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id,$this->billType,$this->returnUrl),
                'returnUrl'=>$this->returnUrl
        ]);
        
    } 
    /**
     * 其它出库单批量导入
     */
    public function actionAjaxImport()
    {
        if (Yii::$app->request->get('download')) {
            $file = dirname(dirname(__FILE__)) . '/resources/excel/货品盘点单数据模板导入.xlsx';
            $content = file_get_contents($file);
            if (!empty($content)) {
                header("Content-type:application/vnd.ms-excel");
                header("Content-Disposition: attachment;filename=货品盘点单数据模板导入" . date("Ymd") . ".xlsx");
                header("Content-Transfer-Encoding: binary");
                exit($content);
            }
        }
        $bill_id = Yii::$app->request->get('bill_id');
        $model = new ImportBillWForm();
        $model->bill_id = $bill_id;
        
        // ajax 校验
        $this->activeFormValidate($model);
        
        if ($model->load(\Yii::$app->request->post())) {            
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                
                $model->file = UploadedFile::getInstance($model, 'file');
                
                \Yii::$app->warehouseService->billW->importGoods($model);
                $trans->commit();
                return $this->message('导入成功', $this->redirect(Yii::$app->request->referrer), 'success');
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
     * 盘点货品添加
     */
    public function actionAjaxPandian()
    {
        $bill_id  = Yii::$app->request->post('bill_id');
        $goods_id = Yii::$app->request->post('goods_id');
        if($goods_id == "") {
            \Yii::$app->getSession()->setFlash('error', '条码货号不能为空');
            return ResultHelper::json(422, "条码货号不能为空");
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            \Yii::$app->warehouseService->billW->pandianGoods($bill_id,[$goods_id=>1]);
            $trans->commit();            
            return $this->message("操作成功", $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            
            \Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return ResultHelper::json(422, $e->getMessage());
        }
    }
    /**
     * 更改实盘数量
     * @return array|mixed
     */
    public function actionAjaxPandianNum()
    {
        $id = Yii::$app->request->get("id");
        $actual_num = Yii::$app->request->get("actual_num");       
        try{
            $trans = \Yii::$app->trans->beginTransaction();
            Yii::$app->warehouseService->billW->pandianNum($id, $actual_num);
            $trans->commit();
            //\Yii::$app->getSession()->setFlash('success', '操作成功');
            //return ResultHelper::json(200, "操作成功");
            return $this->message("操作成功", $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();            
            return ResultHelper::json(422, $e->getMessage());
        }
    }
    
}
