<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\forms\DistributionForm;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use common\helpers\ResultHelper;
use common\helpers\PageHelper;

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
        $dataProvider->query->andWhere(['>=', Order::tableName() . '.distribute_status', DistributeStatusEnum::ALLOWED]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            //'order' => $order,
            'tab' => Yii::$app->request->get('tab', 2),
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
        $tab = Yii::$app->request->get('tab', 1);
        $goods_ids = Yii::$app->request->post('goods_ids');
        $model = $this->findModel($id);
        $model = $model ?? new DistributionForm();
        $model->goods_ids = $goods_ids;
        //$this->activeFormValidate($model);
        if (\Yii::$app->request->isPost) {
            if (!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try {
                $trans = Yii::$app->db->beginTransaction();

                \Yii::$app->salesService->distribution->AccountSales($model);

                $trans->commit();
            } catch (\Exception $e) {
                $trans->rollBack();
                //return ResultHelper::json(422, "保存失败:".$e->getMessage());
                //$error = $e->getMessage();\Yii::error($error);
                return $this->message("保存失败:" . $e->getMessage(), $this->redirect([$this->action->id, 'id' => $model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect([$this->action->id, 'id' => $model->id]), 'success');
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
            'returnUrl' => $this->returnUrl,
            'tab' => $tab,
            'tabList' => \Yii::$app->salesService->distribution->menuTabList($id, $this->returnUrl),
        ]);
    }

    /**
     * 单据打印
     * @return string
     * @throws
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new Order();
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    private function getData($ids)
    {
        $select = ['o.*', 'og.*', 'g.*', 'type.name as product_type_name', 'cate.name as style_cate_name'];
        $query = Order::find()->alias('o')
            ->leftJoin(OrderGoods::tableName() . ' og', 'og.order_id=o.id')
            ->leftJoin(WarehouseGoods::tableName() . ' g', 'g.goods_id=og.goods_id')
            ->leftJoin(ProductType::tableName() . ' type', 'type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=g.style_cate_id')
            ->where(['o.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'cost_price_count' => 0,
        ];
        foreach ($lists as &$list) {
            $list['material'] = \Yii::$app->attr->valueName($list['material']);
            $list['main_stone_type'] = \Yii::$app->attr->valueName($list['main_stone_type']);

            $total['cost_price_count'] += $list['cost_price'];

        }
        return [$lists, $total];
    }
}
