<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;

use addons\Style\backend\controllers\BaseController;
use addons\Style\common\models\Style;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\forms\StyleGoodsForm;
use common\helpers\Url;
use common\enums\AuditStatusEnum;
use addons\Style\common\forms\StyleAuditForm;
use common\enums\StatusEnum;

/**
* Style
*
* Class StyleController
* @package backend\modules\goods\controllers
*/
class StyleController extends BaseController
{
    use Curd;

    /**
    * @var Style
    */
    public $modelClass = Style::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $cate_id = Yii::$app->request->get('cate_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['style_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel, 
        ]);
    }
    
    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $model = $this->findModel($id);
        
        $dataProvider = null;      
        
        return $this->render($this->action->id, [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }
    
    /**
     * 编辑/创建  基础信息
     *
     * @return mixed
     */
    public function actionEditInfo()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);

        if ($model->load(Yii::$app->request->post())) {            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect([$this->action->id,'id'=>$id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }
    /**
     * 编辑-款式属性
     *
     * @return mixed
     */
   /*  public function actionEditAttr()
    {    
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $style = $this->findModel($id);
        
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
                Yii::$app->styleService->styleAttribute->createStyleAttribute($id, $attr_list);
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect([$this->action->id,'id'=>$id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'success');
        }
        $model->initAttrs();
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    } */
    /**
     * 编辑-款式商品
     *
     * @return mixed
     */
    public function actionEditGoods()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $style = $this->findModel($id);
        $model = new StyleGoodsForm();
        $model->style_id = $style->id;
        $model->style_cate_id = $style->style_cate_id;
        $model->style_sn = $style->style_sn;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $goods_list = $model->getPostGoods();
                $trans = Yii::$app->trans->beginTransaction();
                \Yii::$app->styleService->styleGoods->createStyleGoods($id, $goods_list);
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect([$this->action->id,'id'=>$id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'success');
        }
        $model->initGoods();
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }  
    
    /**
     * 审核-款号
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = StyleAuditForm::class;
        $model = $this->findModel($id);        
        // ajax 校验
        $this->activeFormValidate($model);        
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    $model->status = StatusEnum::ENABLED;
                }else{
                    $model->status = StatusEnum::DISABLED;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }

        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
}
