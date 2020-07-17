<?php

namespace addons\Sales\backend\controllers;

use addons\Sales\common\models\Order;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\forms\DistributionForm;
use addons\Sales\common\enums\DistributeStatusEnum;
use common\helpers\ResultHelper;


/**
 * 待配货订单
 *
 * Class DistributionController
 * @package addons\Order\backend\controllers
 */
class DistributionController extends BaseController
{
    use Curd;
    /**
     * @var DistributionForm
     */
    public $modelClass = DistributionForm::class;
    
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['log_msg'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                    'account' => ['order_amount'],
                    'address' => [],
                ]
                
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        //$dataProvider->query->andWhere(['=',DistributionForm::tableName().'.order_id',$order_id]);
        $dataProvider->query->andWhere(['=',Order::tableName().'.distribute_status', DistributeStatusEnum::ALLOWED]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                //'order' => $order,
                'tab'=>Yii::$app->request->get('tab',2),
                //'tabList'=>\Yii::$app->salesService->order->menuTabList($order_id,$this->returnUrl),
        ]);
    }

    /**
     * 销账
     * @return string
     * @throws
     */
    public function actionAccountSales()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $goods_ids = Yii::$app->request->post('goods_ids');
        $model = $this->findModel($id);
        $model = $model ?? new DistributionForm();
        $model->goods_ids = $goods_ids;
        //$this->activeFormValidate($model);
        if (\Yii::$app->request->isPost) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();

                \Yii::$app->salesService->distribution->AccountSales($model);

                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                //return ResultHelper::json(422, "保存失败:".$e->getMessage());
                //$error = $e->getMessage();\Yii::error($error);
                return $this->message("保存失败:".$e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($this->returnUrl), 'success');
        }

        $dataProvider = null;
        if (!is_null($id)) {
            $searchModel = new SearchModel([
                'model' => OrderGoods::class,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'pageSize' => 1000,
            ]);

            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            $dataProvider->query->andWhere(['=', 'order_id', $id]);

            $dataProvider->setSort(false);
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'returnUrl'=>$this->returnUrl,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->salesService->order->menuTabList($id,$this->returnUrl),
        ]);
    }
}
