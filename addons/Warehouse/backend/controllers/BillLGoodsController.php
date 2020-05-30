<?php

namespace addons\Warehouse\backend\controllers;



use addons\Style\common\enums\LogTypeEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseGoods;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\enums\StatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use yii\base\Exception;


/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class BillLGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
    public $billType = BillTypeEnum::BILL_TYPE_L;
    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     */
    public function actionIndex()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
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
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);
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
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);
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
            //更新库存状态
            $billGoods = WarehouseBillGoods::find()->where(['id' => $id])->one();
            $goods = WarehouseGoods::find()->where(['goods_id' => $billGoods->goods_id])->one();
            if(!$goods){
                throw new \yii\db\Exception("商品{$goods->goods_id}不是收货中或者不存在，请查看原因");
            }

            //$goods->goods_status = GoodsStatusEnum::CANCEL;

            if(false === $goods->delete()){
                throw new \Exception($this->getError($goods));
            }

            if(false === $billGoods->delete()){
                throw new \Exception($this->getError($model));
            }

            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->bill->WarehouseBillSummary($billGoods->bill_id);
            if(false === $res){
                throw new \yii\db\Exception('更新单据汇总失败');
            }

            \Yii::$app->getSession()->setFlash('success','删除成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }
}
