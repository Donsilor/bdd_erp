<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use yii\base\Exception;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCGoodsForm;
use addons\Warehouse\common\forms\WarehouseBillThGoodsForm;
use addons\Warehouse\common\forms\WarehouseBillThForm;
use addons\Warehouse\common\models\WarehouseGoods;

/**
 * WarehouseBillBGoodsController implements the CRUD actions for WarehouseBillBGoodsController model.
 */
class BillThGoodsController extends BaseController
{
    use Curd;
    
    
    public $modelClass = WarehouseBillThGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_TH;

    /**
     * Lists all WarehouseBillBGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name', 'goods_remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);

        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => Yii::$app->request->get('tab',2),
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $this->returnUrl),
        ]);
    }
    /**
     * 扫码添加
     */
    public function actionAjaxScan()
    {
        $bill_id  = Yii::$app->request->post('bill_id');
        $goods_id = Yii::$app->request->post('goods_id');
        if(empty($goods_id)) {
            return $this->message('条码货号不能为空', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            \Yii::$app->warehouseService->billTh->scanAddGoods($bill_id,[$goods_id]);            
            $trans->commit();
            return $this->message("添加成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
    /**
     * ajax 更新商品明细
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }
        $data = Yii::$app->request->get();
        $model->attributes = ArrayHelper::filter($data, array_keys($data));
        try{
            $trans = Yii::$app->trans->beginTransaction();
            if (!$model->save()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            \Yii::$app->warehouseService->billTh->billSummary($model->bill_id);
            $trans->commit();
            return ResultHelper::json(200, '修改成功');
        }catch (\Exception $e) {
            $trans->rollback();
            return ResultHelper::json(404, '找不到数据');
        }
        
    }
    
    /**
     * 编辑/创建
     * @property WarehouseBillBForm $model
     * @return mixed
     */
    public function actionAdd()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $bill_id = Yii::$app->request->get('bill_id');
        $this->modelClass = WarehouseBillThForm::class;
        $form = $this->findModel($bill_id);
        $form = $form ?? new WarehouseBillThForm();
        if(\Yii::$app->request->post("search") == 1 && $form->load(\Yii::$app->request->post())){
            $form->validateGoodsList();//查询校验
            $searchModel = new SearchModel([
                'model' => WarehouseGoods::class,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [],
                'pageSize' => 100,
                'relations' => [
                    
                ]
            ]);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['goods_id'=>$form->getGoodsIds()]);
            return $this->render($this->action->id, [
                'model' => $form,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        } 
        
        if($form->load(\Yii::$app->request->post())){
            try {
                $trans = Yii::$app->db->beginTransaction();
                //批量添加商品
                \Yii::$app->warehouseService->billTh->batchAddGoods($form);
                $trans->commit();
                return $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(424, $e->getMessage());
            }
        }        
        
        return $this->render($this->action->id, [
            'model' => $form,
        ]);
    }

    /**
     * 其它出库单-批量编辑
     * @return mixed
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => Yii::$app->request->get('tab',2),
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $this->returnUrl),            
        ]);
    }
    
    /**
     *
     * 删除
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $trans = \Yii::$app->trans->beginTransaction();           
            \Yii::$app->warehouseService->billTh->deleteGoods($id);            
            $trans->commit();            
            return $this->message("删除成功", $this->redirect(\Yii::$app->request->referrer), 'success');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
    
    /**
     * 更改退货数量
     * @return array|mixed
     */
    public function actionAjaxReturnNum()
    {
        $id = Yii::$app->request->get("id");
        $goods_num = Yii::$app->request->get("goods_num");
        if($goods_num <= 0) {
            return ResultHelper::json(422, "退货数量必须大于0");
        }
        try{
            $trans = \Yii::$app->trans->beginTransaction();
            Yii::$app->warehouseService->billTh->updateReturnNum($id, $goods_num);
            $trans->commit();
            return $this->message("操作成功", $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return ResultHelper::json(422, $e->getMessage());
        }
    }


}
