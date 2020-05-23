<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillMForm;
use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\SnHelper;
use common\models\base\SearchModel;
use common\traits\Curd;
use yii\db\Exception;


/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class WarehouseBillMController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBill::class;

    /**
     * 调拨单
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['updated_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type','M']);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $this->getError($dataProvider);
        }
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
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $this->modelClass = WarehouseBillMForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $bill_type = BillTypeEnum::BILL_TYPE_L;
                    $model->bill_no = SnHelper::createBillSn($bill_type);
                    $model->bill_type = $bill_type;
                    $log_msg = '';
                }else{

                }
                $model->save();

                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调拨单',
                    'log_msg' => '创建调拨单'
                ];
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


}
