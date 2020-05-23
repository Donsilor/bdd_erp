<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\enums\StatusEnum;
use yii\base\Exception;


/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class WarehouseBillGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     */
    public function actionIndex()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill/index']));
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
        $model = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tabList'=>\Yii::$app->warehouseService->warehouseBill->menuTabList($bill_id,$returnUrl),
            'tab' => $tab,
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
        $goods_list = Yii::$app->request->post('bill_goods_list');
        $bill_info = Yii::$app->request->post('WarehouseBill');
        $model = new WarehouseBillGoods();
        if(!empty($goods_list)){
            try {
                $trans = Yii::$app->db->beginTransaction();
                $bill_id = $bill_info['id'];
                foreach ($goods_list as $key => $goods) {
                    $id = isset($goods['id']) ? $goods['id'] : '';
                    $model = $this->findModel($id);
                    // ajax 校验
                    $this->activeFormValidate($model);
                    if (false === $model::updateAll($goods, ['id' => $id])) {
                        throw new Exception($this->getError($model));
                    }
                }

                $old_list = $model::find()->where(['bill_id' => $bill_id])->asArray()->all();
                $old_ids = array_column($old_list, 'id');
                $new_ids = array_column($goods_list, 'id');
                $del_ids = array_diff($old_ids, $new_ids);
                if(!empty($del_ids)){
                    $res = $model::updateAll(['status' => StatusEnum::DELETE], ['id' => $del_ids]);
                    if(false === $res){
                        throw new Exception('删除明细商品失败');
                    }
                }

                //更新采购收货单汇总：总金额和总数量
                $res = Yii::$app->warehouseService->warehouseBill->WarehouseBillSummary($bill_id);
                if(false === $res){
                    throw new Exception('更新单据汇总失败');
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax('index', [
            'model' => $model
        ]);
    }
}
