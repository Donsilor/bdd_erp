<?php

namespace backend\modules\common\controllers;

use backend\controllers\BaseController;
use common\models\base\SearchModel;
use common\models\common\GoldPrice;
use Yii;
use common\traits\Curd;
use common\enums\OperateTypeEnum;
use common\models\forms\GoldPriceChangeForm;

/**
 * 商品分类
 *
 * Class ArticleCateController
 * @package addons\RfArticle\backend\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class GoldPriceController extends BaseController
{
    use Curd;

    /**
     * @var StyleCateController
     */
    public $modelClass = GoldPrice::class;

    /**
     * Lists all Tree models.
     * @return mixed
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
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return mixed|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);
        // ajax 验证
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->save();
            if($res){
                $this->redirect(['index']);
            }else{
                $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
    /**
     * 手动更新金价
     * @return mixed|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxPrice()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $this->modelClass = GoldPriceChangeForm::class;
        $model = $this->findModel($id);
        // ajax 验证
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->sync_type = OperateTypeEnum::USER;
            $model->sync_user = Yii::$app->user->identity->username;
            $model->sync_time = time();
            if(true === $model->save()){
                $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }else{
                $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $model->status = -1;
            $model->save();
            return $this->message("删除成功", $this->redirect(['index', 'id' => $model->id]));
        }
        
        return $this->message("删除失败", $this->redirect(['index', 'id' => $model->id]), 'error');
    }
}