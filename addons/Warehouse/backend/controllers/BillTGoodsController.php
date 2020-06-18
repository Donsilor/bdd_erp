<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\helpers\ResultHelper;
use yii\base\Exception;

/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class BillTGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillTGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_T;
    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-t-goods/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'productType' => ['name'],
                'styleCate' => ['name'],
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseBillGoodsL::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
            'tab' => $tab,
        ]);
    }

    /**
     * ajax添加商品
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillGoodsL();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                $model->bill_id = $bill_id;
                Yii::$app->warehouseService->billT->addBillTGoods($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['edit-all', 'bill_id' => $bill_id]);
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
     * ajax编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = \Yii::$app->request->get('id');
        //$bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillGoodsL();
        // ajax 校验
        //$this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                //$trans = \Yii::$app->db->beginTransaction();
                //Yii::$app->warehouseService->billT->addBillTGoods($model);
                //$trans->commit();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                //$trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax批量编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionBatchEdit()
    {
        $ids = Yii::$app->request->post('ids');
        $field = Yii::$app->request->post('field');
        $text = Yii::$app->request->post('text');
        $model = new WarehouseBillTGoodsForm();
        $model->ids = $ids;
        $id_arr = $model->getIds();
        if(!$id_arr){
            return ResultHelper::json(422, "ID不能为空");
        }
        if(!$field){
            return ResultHelper::json(422, "字段错误");
        }
        if(!$text){
            return ResultHelper::json(422, "输入值不能为空");
        }
        try{
            $trans = Yii::$app->trans->beginTransaction();
            foreach ($id_arr as $id) {
                $goods = WarehouseBillGoodsL::findOne(['id'=>$id]);
                $goods->$field = $text;
                if(false === $goods->validate()) {
                    throw new \Exception($this->getError($goods));
                }
                if(false === $goods->save()) {
                    throw new \Exception($this->getError($goods));
                }
            }
            $trans->commit();
            Yii::$app->getSession()->setFlash('success','保存成功');
            return ResultHelper::json(200, '保存成功');
        }catch (\Exception $e){
            $trans->rollBack();
            return ResultHelper::json(422, $e->getMessage());
        }
    }


    public function actionBatchSelect(){
        $this->layout = '@backend/views/layouts/iframe';
        $ids = Yii::$app->request->get('ids');
        $id_arr = explode(',',$ids);
        $name = Yii::$app->request->get('name');
        $attr_id = Yii::$app->request->get('attr_id',0);
        if($attr_id == 0){
            return ResultHelper::json(400, '参数错误',[]);
        }
        $style_arr = WarehouseBillTGoodsForm::find()->where(['id'=>$id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if(count($style_arr) != 1){
            return ResultHelper::json(400, '请选择一个款的商品进行操作',[]);
        }
        $style_sn = $style_arr[0]['style_sn'];
        $model = new WarehouseBillTGoodsForm();

        $attr_arr = Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn,$attr_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'ids' => $ids,
            'name'=> $name,
            'attr_arr' =>$attr_arr
        ]);
    }

    /**
     * 收货单-编辑
     * @return mixed
     */
    public function actionEditAll()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill-l-goods/index']));
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
        //$dataProvider->query->andWhere(['>',WarehouseBillGoodsT::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'tab' => $tab,
        ]);
    }

    /**
     * 删除/关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if(false === $model->delete()){
                throw new \Exception($this->getError($model));
            }
            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            if(false === $res){
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success','删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
}
