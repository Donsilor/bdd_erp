<?php

namespace addons\Gdzb\backend\controllers;

use addons\Sales\common\enums\ChannelIdEnum;
use addons\Gdzb\common\forms\OrderForm;
use addons\Gdzb\common\models\Order;
use common\helpers\DateHelper;
use Yii;
use common\helpers\Url;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use addons\Gdzb\common\models\Customer;
use common\traits\Curd;
use yii\db\Exception;

/**
 * 客户管理
 *
 * Class CustomerController
 * @package addons\Sales\backend\controllers
 */
class CustomerController extends BaseController
{
    use Curd;

    /**
     * @var Customer
     */
    public $modelClass = Customer::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['realname'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Customer::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Customer::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',Customer::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 创建客户
     * @return array|mixed
     * @throws
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new Customer();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                if($model->channel_id == ChannelIdEnum::GP && !$model->email){
                    throw new \Exception("渠道为国际批发，客户邮箱为必填");
                }
                if($model->channel_id != ChannelIdEnum::GP && !$model->mobile){
                    throw new \Exception("非国际批发客户手机号必填");
                }
                if($model->birthday){
                    $model->age = DateHelper::getYearByDate($model->birthday);
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                \Yii::$app->salesService->customer->createCustomerNo($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
                return $isNewRecord
                    ? $this->message("保存成功", $this->redirect(['edit', 'id' => $model->id]), 'success')
                    : $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);

    }

    /**
     * 编辑客户
     *
     * @return mixed
     * @throws
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new Customer();

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                if(!$model->customer_no){
                    \Yii::$app->salesService->customer->createCustomerNo($model);
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:".$e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect(['view','id'=>$model->id]), 'success');
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['index']));
        $model = $this->findModel($id);
        $model = $model ?? new Customer();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->gdzbService->customer->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * 客户订单列表
     * @return string
     * @throws
     */
    public function actionOrder()
    {
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['index']));
        $customer_id = \Yii::$app->request->get('customer_id', null);
        $model = $this->findModel($customer_id);
        $searchModel = new SearchModel([
            'model' => Order::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['order_time']);
        $searchParams = \Yii::$app->request->queryParams['SearchModel'] ?? [];
        $dataProvider->query->andWhere(['=', Order::tableName().'.customer_id', $customer_id]);
        //创建时间过滤
        if (!empty($searchParams['order_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['order_time']);
            $dataProvider->query->andFilterWhere(['between', Order::tableName().'.order_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->salesService->customer->menuTabList($customer_id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}