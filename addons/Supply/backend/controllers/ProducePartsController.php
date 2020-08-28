<?php

namespace addons\Supply\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceParts;
use addons\Supply\common\enums\PeijianStatusEnum;
use addons\Supply\common\enums\BuChanEnum;

/**
 * 配件列表
 *
 * Class ProducePartsController
 * @package addons\Supply\backend\controllers
 */
class ProducePartsController extends BaseController
{
    use Curd;
    
    /**
     * @var ProduceParts
     */
    public $modelClass = ProduceParts::class;
    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $produce_id = Yii::$app->request->get('produce_id');
        
        $produce = Produce::find()->where(['id'=>$produce_id])->one();
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'relations' => [
                        
                ],
                'pageSize' => $this->getPageSize(),
                
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=','produce_id',$produce_id]);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'produce' => $produce,
                'tab'=> Yii::$app->request->get('tab'),
                'tabList'=>\Yii::$app->supplyService->produce->menuTabList($produce_id,$this->returnUrl),
        ]);
    }
    /**
     * 确认配件
     */
    public function actionAjaxConfirm()
    {
        $produce_id = Yii::$app->request->get('produce_id');
        $produce = Produce::find()->where(['id'=>$produce_id])->one();
        //单据校验
        if($produce->peijian_status == PeijianStatusEnum::HAS_LINGJIAN){
            return $this->message('布产单已经确认领件了！', $this->redirect(Yii::$app->request->referrer), 'error');
        } elseif($produce->peijian_status != PeijianStatusEnum::TO_LINGJIAN) {
            return $this->message('布产单不是待领件状态,不能操作！', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        foreach($produce->ProduceParts ?? [] as $produceParts) {
            if($produceParts->peijian_status < PeijianStatusEnum::TO_LINGJIAN){
                return $this->message("(ID={$produceParts->id})配件单不是待领件状态", $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }        
        try {
              $trans = \Yii::$app->trans->beginTransaction();  
              //1
              $res = ProduceParts::updateAll(['peijian_status'=>PeijianStatusEnum::HAS_LINGJIAN],['produce_id'=>$produce_id,'peijian_status'=>PeijianStatusEnum::TO_LINGJIAN]);
              if(!$res) {
                  throw new \Exception("确认失败！code=1");
              }
              //2
              $produce->peijian_status = PeijianStatusEnum::HAS_LINGJIAN;
              if($produce->peijian_status == PeijianStatusEnum::HAS_LINGJIAN) {
                  $produce->bc_status = BuChanEnum::TO_PRODUCTION;
              }
              if(false === $produce->save(true,['peijian_status','bc_status','updated_at'])){
                  throw new \Exception("确认失败！code=2");
              }
              $trans->commit();
              return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        
    }
    
    /**
     * 重置配件
     */
    public function actionAjaxReset()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        //单据校验
        if($model->peijian_status != PeiJianStatusEnum::TO_LINGJIAN) {
            return $this->message('不是待配件状态,不能操作！', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            $model->peijian_status = PeijianStatusEnum::IN_PEIJIAN;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            Yii::$app->supplyService->produce->autoPeijianStatus([$model->produce_sn]);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        
    }
    
    
    
}