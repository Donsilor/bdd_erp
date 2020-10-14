<?php

namespace addons\Sales\backend\controllers;


use Yii;
use common\models\base\SearchModel;
use addons\Sales\common\models\Customer;
use common\traits\Curd;
use addons\Sales\common\models\CustomerAddress;

/**
 * 客户收货地址管理
 *
 * Class CustomerAddressController
 * @package addons\Sales\backend\controllers
 */
class CustomerAddressController extends BaseController
{
    use Curd;

    /**
     * @var Customer
     */
    public $modelClass = CustomerAddress::class;  
    public $noAuthOptional = ['index'];
    /**
     * 客户收货地址列表
     * @return string
     * @throws
     */
    public function actionIndex()
    {  
        $customer_id = \Yii::$app->request->get('customer_id');      
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC,
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                        
                ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', CustomerAddress::tableName().'.customer_id', $customer_id]);
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'tab'=>Yii::$app->request->get('tab',2),
                'tabList'=>\Yii::$app->salesService->customer->menuTabList($customer_id, $this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }    
    /**
     * 编辑客户地址
     *
     * @return mixed
     * @throws
     */
    public function actionAjaxEdit()
    {        
        $id = Yii::$app->request->get('id');
        $customer_id = Yii::$app->request->get('customer_id');
        
        $this->modelClass = CustomerAddress::class;
        $model = $this->findModel($id);        
        if($model->isNewRecord) { 
            if($customer_id == '') {
                return $this->message("customer_id不能为空", $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            $model->customer_id = $customer_id;
        }
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:".$e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }    
}