<?php

namespace common\traits;

use Yii;
use yii\data\Pagination;
use yii\base\InvalidConfigException;
use common\helpers\ResultHelper;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\enums\AuditStatusEnum;

/**
 * Trait Curd
 * @property \yii\db\ActiveRecord|\yii\base\Model $modelClass
 * @package common\traits
 */
trait Curd
{
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->modelClass === null) {
            throw new InvalidConfigException('"modelClass" 属性必须设置.');
        }
    }

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = $this->modelClass::find()
            ->where(['>=', 'status', StatusEnum::DISABLED]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();

        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 伪删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDestroy($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
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
        if ($this->findModel($id)->delete()) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * ajax更新排序/状态
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }

        $keys_arr = array_keys(Yii::$app->request->get());  //$model->attributes();
        $model->attributes = ArrayHelper::filter(Yii::$app->request->get(), $keys_arr);

        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return ResultHelper::json(200, '修改成功');
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
            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = $this->modelClass::findOne($id)))) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }
        return $model;
    }
    
    /**
     * 编辑/创建 多语言
     *
     * @return mixed
     */
    public function actionEditLang()
    {
        $id = Yii::$app->request->get('id', null);
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        
        //$trans = Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->editLang($model,false);
            return $this->redirect($returnUrl);
        }
        
        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEditLang()
    {
        $id = Yii::$app->request->get('id');
        //$trans = Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                //多语言编辑
                $this->editLang($model,true);
                return $this->redirect(['index']);
            }else{
                return $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * ajax 批量删除(物理)
     * @param array $ids
     * @return string[]|array[]
     */
    public function actionAjaxBatchDelete ($ids = []){
        
        try{
            $ids = Yii::$app->request->post("ids",$ids);
            
            $trans = Yii::$app->db->beginTransaction();
            
            $model = $this->findModel(null);
            $count = $model->deleteAll(['in','id',$ids]);
            if(method_exists($model,'langModel')){
                $langModel = $model->langModel();
                $langModel->deleteAll(['in','master_id',$ids]);
            }
            $trans->commit();
            
            return ResultHelper::json(200, '删除成功');
        } catch (\Exception $e) {
            
            $trans->rollBack();
            return ResultHelper::json(422, '删除失败');
        }
    }
    
    /**
     * ajax批量更新排序/状态
     *
     * @param $id
     * @return array
     */
    public function actionAjaxBatchUpdate($ids = [])
    {
        $ids = Yii::$app->request->post('ids');
        if(!empty($ids) && is_array($ids)){
            foreach ($ids as $id){
                if (!($model = $this->modelClass::findOne($id))) {
                    return ResultHelper::json(404, '找不到数据');
                }
                
                $model->attributes = ArrayHelper::filter(Yii::$app->request->post(), ['sort', 'status']);
                if (!$model->save(false)) {
                    return ResultHelper::json(422, $this->getError($model));
                }
            }
        }
        return ResultHelper::json(200, '修改成功',[],true);
    }
    
    /**
     * ajax 批量审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        
        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->audit_time = time();
            $model->auditor_id = \Yii::$app->user->identity->id;
            if($model->audit_status == AuditStatusEnum::PASS){
                $model->status = StatusEnum::ENABLED;
            }else{
                $model->status = StatusEnum::DISABLED;
            }
            return $model->save()
            ? $this->redirect(Yii::$app->request->referrer)
            : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
}