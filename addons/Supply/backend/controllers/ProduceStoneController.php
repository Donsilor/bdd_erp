<?php

namespace addons\Supply\backend\controllers;

use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;

use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\models\Produce;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;

/**
 * 配石列表
 *
 * Class ProduceStoneController
 * @package addons\Supply\backend\controllers
 */
class ProduceStoneController extends BaseController
{
    use Curd;
    
    /**
     * @var ProduceStone
     */
    public $modelClass = ProduceStone::class;
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
                        'id' => SORT_ASC
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
     * 确认配石
     */
    public function actionAjaxConfirm()
    {
        $produce_id = Yii::$app->request->get('produce_id');
        $produce = Produce::find()->where(['id'=>$produce_id])->one();
        //单据校验
        if($produce->peishi_status == PeishiStatusEnum::HAS_LINGSHI){
            return $this->message('布产单已经确认领石了！', $this->redirect(Yii::$app->request->referrer), 'error');
        }elseif($produce->peishi_status != PeishiStatusEnum::TO_LINGSHI) {
            return $this->message('布产单不是待领石状态,不能操作！', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        foreach($produce->produceStones ?? [] as $produceStone) {
            if($produceStone->peishi_status < PeishiStatusEnum::TO_LINGSHI){
                return $this->message("(ID={$produceStone->id})配料单不是待领石状态", $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            //1
            $res = ProduceStone::updateAll(['peishi_status'=>PeishiStatusEnum::HAS_LINGSHI],['produce_id'=>$produce_id,'peishi_status'=>PeishiStatusEnum::TO_LINGSHI]);
            if(!$res) {
                throw new \Exception("确认失败！code=1");
            }
            //2
            $produce->peishi_status = PeishiStatusEnum::HAS_LINGSHI;
            //布产单状态未生产之前，确认配石 需要变动布产状态
            if($produce->bc_status < BuChanEnum::TO_PRODUCTION) {
                if($produce->peiliao_status == PeiliaoStatusEnum::HAS_LINGLIAO) {
                    $produce->bc_status = BuChanEnum::TO_PRODUCTION;
                }
            }
            if(false === $produce->save(true,['peishi_status','bc_status','updated_at'])){
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
     * 重置配石
     */
    public function actionAjaxReset()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        //单据校验
        if($model->peishi_status != PeishiStatusEnum::TO_LINGSHI) {
            return $this->message('不是待领石状态,不能操作！', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            $model->peishi_status = PeishiStatusEnum::IN_PEISHI;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            Yii::$app->supplyService->produce->autoPeishiStatus([$model->produce_sn]);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        
    }
        
    
}