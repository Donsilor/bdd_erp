<?php

namespace addons\Supply\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use addons\Supply\common\models\Supplier;
use yii\base\Exception;
use common\traits\Curd;
/**
* Supplier
*
* Class SupplierController
* @package backend\modules\goods\controllers
*/
class SupplierController extends BaseController
{
    use Curd;

    /**
    * @var Supplier
    */
    public $modelClass = Supplier::class;


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
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        //$dataProvider->query->andWhere(['>','status',-1]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEditLang()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }

            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
