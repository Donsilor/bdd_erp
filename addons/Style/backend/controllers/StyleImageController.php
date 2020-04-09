<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\enums\FaceWorkEnum;
use addons\Style\common\enums\ImagePositionEnum;
use addons\Style\common\enums\ImageTypeEnum;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleImages;
use common\enums\ConfirmEnum;
use common\helpers\ResultHelper;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use yii\db\Exception;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleImageController extends BaseController
{
    use Curd;
    public $modelClass = StyleImages::class;
    public $noAuthOptional = ['get-position'];
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $style_id = Yii::$app->request->get('style_id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        $tab = Yii::$app->request->get('tab');
        $style = Style::find()->where(['id'=>$style_id])->one();
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['factory.factory_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
                'style' => ['style_sn'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',StyleImages::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',StyleImages::tableName().'.style_id',$style_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab'=>$tab,
            'style_id' => $style_id,
            'tabList'=>\Yii::$app->styleService->style->editTabList($style_id,$returnUrl),
            'style' => $style,
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
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if($model->is_default == ConfirmEnum::YES){
                    StyleImages::updateAll(['is_default'=>ConfirmEnum::NO],['style_id'=>$model->style_id]);
                    Style::updateAll(['style_image'=>$model->image],['id'=>$model->style_id]);
                }
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }

                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


    public function actionGetPosition(){
        $type = Yii::$app->request->post('type');
        $position = ImageTypeEnum::getPosition($type);
        return ResultHelper::json(200, 'ok',$position);

    }


}
