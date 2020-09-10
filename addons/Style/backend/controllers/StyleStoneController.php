<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\models\Style;
use addons\Style\common\models\StyleStone;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleStoneController extends BaseController
{
    use Curd;
    public $modelClass = StyleStone::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $style_id = Yii::$app->request->get('style_id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        $style = Style::find()->where(['id'=>$style_id])->one();
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
                'style' => ['style_sn'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',StyleStone::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',StyleStone::tableName().'.style_id',$style_id]);

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
                $trans = Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $stone_types = $model->stone_type;
                    foreach ($stone_types as $stone_type){
                        $new_model = new StyleStone();
                        $new_model->attributes = $model->attributes;
                        $new_model['stone_type'] = $stone_type;
                        $count = StyleStone::find()->where(['style_id'=>$model->style_id,'position'=>$model->position,'stone_type'=>$stone_type])->count();
                        if($count){
                            return $this->message(\addons\Style\common\enums\StoneEnum::getValue($model->position,'getPositionMap').'和'.Yii::$app->attr->valueName($stone_type).'已经存在', $this->redirect(Yii::$app->request->referrer), 'error');
                        }
                        if(false === $new_model->save()){
                            throw new \Exception($this->getError($new_model));
                        }
                    }

                }else{
                    if(false === $model->save()){
                        throw new \Exception($this->getError($model));
                    }
                }
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


}
