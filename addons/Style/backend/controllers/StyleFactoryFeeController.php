<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\helpers\Url;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleFactoryFee;
use addons\Style\common\enums\LogTypeEnum;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleFactoryFeeController extends BaseController
{
    use Curd;
    public $modelClass = StyleFactoryFee::class;

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
                'supplier' => ['supplier_name'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',StyleFactoryFee::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',StyleFactoryFee::tableName().'.style_id',$style_id]);

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
     *
     * ajax编辑/创建
     * @return mixed|string|
     * @throws
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $style = Style::find()->select(['style_sn'])->where(['id' => $model->style_id])->one();
                //记录日志
                $log = [
                    'style_id' => $model->style_id,
                    'style_sn' => $style->style_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_time' => time(),
                    'log_module' => '工费信息',
                    'log_msg' => $model->isNewRecord ? "创建工费信息" : "编辑工费信息",
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
