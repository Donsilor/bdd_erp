<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\enums\FaceWorkEnum;
use addons\Style\common\enums\ImagePositionEnum;
use addons\Style\common\enums\ImageTypeEnum;
use addons\Style\common\enums\LogTypeEnum;
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
            'tabList'=>\Yii::$app->styleService->style->menuTabList($style_id,$returnUrl),
            'style' => $style,
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
        $model = $this->findModel($id);
        if ($this->findModel($id)->delete()) {

            $style = Style::find()->where(['id'=>$model->style_id])->one();
            Yii::$app->styleService->style->updateStyleImage($style);
            //记录日志
            $log = [
                'style_id' => $model->style_id,
                'style_sn' => $style->style_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_time' => time(),
                'log_module' => '图片信息',
                'log_msg' => "删除图片信息",
            ];
            \Yii::$app->styleService->styleLog->createStyleLog($log);
            return $this->message("删除成功", $this->redirect(['index','style_id'=>$model->style_id]));
        }

        return $this->message("删除失败", $this->redirect(['index','style_id'=>$model->style_id]), 'error');
    }

    public function actionGetPosition(){
        $type = Yii::$app->request->post('type');
        $position = ImageTypeEnum::getPosition($type);
        return ResultHelper::json(200, 'ok',$position);

    }


    public function actionAjaxEditMulte(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $images_list = $model->image;
                foreach ($images_list as $image){
                    $new_model = new StyleImages();
                    $new_model->attributes = $model->attributes;
                    $new_model->image = $image;
                    $new_model->type = ImageTypeEnum::ORIGINAL;
                    $new_model->position = ImagePositionEnum::POSITIVE;
                    if(false === $new_model->save()){
                        throw new \Exception($this->getError($new_model));
                    }
                }
                $style = Style::find()->select(['style_sn'])->where(['id' => $model->style_id])->one();
                //记录日志
                $log = [
                    'style_id' => $model->style_id,
                    'style_sn' => $style->style_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_time' => time(),
                    'log_module' => '图片信息',
                    'log_msg' => $model->isNewRecord ? "创建图片信息" : "编辑图片信息",
                ];
                \Yii::$app->styleService->styleLog->createStyleLog($log);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message("保存失败=>" . $e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


}
