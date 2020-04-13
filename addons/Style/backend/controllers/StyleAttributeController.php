<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;

use addons\Style\backend\controllers\BaseController;
use addons\Style\common\models\Style;
use addons\Style\common\forms\StyleAttrForm;
use common\helpers\Url;

use addons\Style\common\models\StyleAttribute;
use common\enums\StatusEnum;

/**
 * Style
 *
 * Class StyleController
 * @package backend\modules\goods\controllers
 */
class StyleAttributeController extends BaseController
{
    use Curd;
    
    /**
     * @var Style
     */
    public $modelClass = StyleAttribute::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
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
                    //'id' => SORT_DESC
                ],
                'pageSize' => 100,
                'relations' => [
                     
                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',StyleAttribute::tableName().'.style_id',$style_id]);
        $dataProvider->query->andWhere(['=',StyleAttribute::tableName().'.status',StatusEnum::ENABLED]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->menuTabList($style_id,$returnUrl),
                'returnUrl'=>$returnUrl,
                'style' => $style,
        ]);
    }


    /**
     * 编辑-款式属性
     *
     * @return mixed
     */
    public function actionAjaxEdit()
    {
        
        $style_id = Yii::$app->request->get('style_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $this->modelClass = Style::class;
        $style = $this->findModel($style_id);
        
        $model = new StyleAttrForm();
        $model->style_id = $style->id;
        $model->style_cate_id = $style->style_cate_id;
        $model->style_sn = $style->style_sn;
        // ajax 校验
        $this->activeFormValidate($model);
        
        if ($model->load(Yii::$app->request->post())) {
            $attr_list = $model->getPostAttrs();
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->styleService->styleAttribute->createStyleAttribute($style_id, $attr_list);
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'style_id'=>$style_id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect(['index','style_id'=>$style_id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'success');
        }
        $model->initAttrs();
        return $this->renderAjax($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->menuTabList($style_id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }
    
}
