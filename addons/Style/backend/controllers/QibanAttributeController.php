<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\QibanAttribute;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;

use addons\Style\common\models\Style;
use common\helpers\Url;

use common\enums\StatusEnum;

/**
 * Style
 *
 * Class StyleController
 * @package backend\modules\goods\controllers
 */
class QibanAttributeController extends BaseController
{
    use Curd;
    
    /**
     * @var Style
     */
    public $modelClass = QibanAttribute::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $qiban_id = Yii::$app->request->get('qiban_id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['qiban/index']));
        
        $qiban = Qiban::find()->where(['id'=>$qiban_id])->one();
        
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
        
        $dataProvider->query->andWhere(['=',QibanAttribute::tableName().'.qiban_id',$qiban_id]);
        $dataProvider->query->andWhere(['=',QibanAttribute::tableName().'.status',StatusEnum::ENABLED]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'returnUrl'=>$returnUrl,
                'qiban' => $qiban,
        ]);
    }


    /**
     * 编辑-款式属性
     *
     * @return mixed
     */
    public function actionAjaxEdit()
    {
        
        $qiban_id = Yii::$app->request->get('qiban_id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['qiban/index']));
        
        $this->modelClass = Qiban::class;
        $qiban = $this->findModel($qiban_id);
        
        $model = new QibanAttrForm();
        $model->qiban_id = $qiban->id;
        $model->style_cate_id = $qiban->style_cate_id;
        $model->qiban_sn = $qiban->qiban_sn;
        // $model->is_combine = $qiban->type->is_combine;
        // ajax 校验
        $this->activeFormValidate($model);
        
        if ($model->load(Yii::$app->request->post())) {
            $attr_list = $model->getPostAttrs();
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->styleService->qibanAttribute->createQibanAttribute($qiban_id, $attr_list);
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
//                echo $e->getMessage();exit;
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'qiban_id'=>$qiban_id,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect(['index','qiban_id'=>$qiban_id,'returnUrl'=>$returnUrl]), 'success');
        }
        $model->initAttrs();
        return $this->renderAjax($this->action->id, [
                'model' => $model,
                'returnUrl'=>$returnUrl,
        ]);
    }
    
}
