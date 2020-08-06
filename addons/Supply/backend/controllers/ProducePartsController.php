<?php

namespace addons\Supply\backend\controllers;

use addons\Supply\common\enums\PeijianStatusEnum;
use addons\Supply\common\models\ProduceParts;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\models\Produce;
use addons\Supply\common\enums\PeiliaoStatusEnum;
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
        foreach($produce->produceGolds ?? [] as $produceGold) {
            if($produceGold->peiliao_status != PeijianStatusEnum::TO_LINGJIAN){
                return $this->message("(ID={$produceGold->id})配件单不是待领件状态", $this->redirect(Yii::$app->request->referrer), 'error');
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
     * 重置配料
     */
    public function actionAjaxReset()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        //单据校验
        if($model->peiliao_status != PeiliaoStatusEnum::TO_LINGSHI) {
            return $this->message('不是待领石状态,不能操作！', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            $model->peiliao_status = PeiliaoStatusEnum::IN_PEISHI;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            Yii::$app->supplyService->produce->autoPeiliaoStatus([$model->produce_sn]);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        
    }
    
    
    
}