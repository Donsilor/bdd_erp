<?php

namespace addons\Finance\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Finance\common\forms\OrderPayForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\enums\PayStatusEnum;

/**
 *
 * 财务订单点款
 * Class OrderPayController
 * @package backend\modules\goods\controllers
 */
class OrderPayController extends BaseController
{
    use Curd;
    
    /**
     * @var BankPay
     */
    public $modelClass = OrderPayForm::class;
    /**
     * @var int
     */
    
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $this->modelClass = Order::class;
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                    'account'=>["order_amount","pay_amount","paid_amount","currency"] ,
                    'payLogs'=>["pay_sn"]
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=',Order::tableName().".order_status",OrderStatusEnum::CONFORMED]);
        //$dataProvider->query->andWhere(['=',Order::tableName().".pay_status",PayStatusEnum::NO_PAY]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderPayForm();
        
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                
                
                $trans->commit();
                return $this->message('操作成功', $this->redirect(['index']), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            
        }
        
        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    
    
    
}
