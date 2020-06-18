<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Supply\common\models\ProduceStone;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use addons\Supply\common\enums\PeishiStatusEnum;
use common\helpers\Url;
use addons\Supply\common\models\ProduceStoneGoods;



/**
 * 
 * 配石列表
 */
class StoneApplyController extends BaseController
{
    use Curd;
    public $modelClass = ProduceStone::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
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
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,                
        ]);
        
    }
    /**
     * 批量配石
     */
    public function actionPeishi()
    {   
        $ids = Yii::$app->request->get('ids');
        if(empty($ids)) {
            return ResultHelper::json(422,'ids参数不能为空');
        }
        $ids = StringHelper::explodeIds($ids);           
        if (Yii::$app->request->get('check')) {            
            //数据校验
            foreach ($ids as $id) {
                $model = ProduceStone::find()->where(['id'=>$id])->one();
                if($model && $model->peishi_status != PeishiStatusEnum::PENDING) {
                     return ResultHelper::json(422,"(ID={$id})配石单不是待配石状态");
                }
            }
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['peishi','ids'=>implode(',',$ids)])]);
        }
        
        if ($post = Yii::$app->request->post('ProduceStone')) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->supplyService->produceStone->batchPeishi($post);                
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200,"保存成功");
            }catch (\Exception $e) {
                $trans->rollback();
                return ResultHelper::json(422,$e->getMessage());
            }
        }
        
        
        $this->layout = '@backend/views/layouts/iframe';        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC,
                        'stone_position' => SORT_DESC,
                ],
                'relations' => [
                        
                ],
                'pageSize' => $this->getPageSize(100),                
        ]);        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);  
        $dataProvider->query->andWhere([ProduceStone::tableName().'.id'=>$ids]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 创建送石单(领石单)
     */
    public function actionSongshi()
    {
        $ids = Yii::$app->request->get('ids');
        if(empty($ids)) {
            return ResultHelper::json(422,'ids参数不能为空');
        }
        $ids = StringHelper::explodeIds($ids);
        if (Yii::$app->request->get('check')) {
            //数据校验
            $order_sn_array = [];
            foreach ($ids as $id) {
                $model = ProduceStone::find()->where(['id'=>$id])->one();
                if($model && $model->peishi_status != PeishiStatusEnum::FINISH) {
                    return ResultHelper::json(422,"(ID={$id})配石单不是已配石状态");
                }
                $order_sn_array[$model->from_order_sn] = $model->from_order_sn;
            }
             if(count($order_sn_array) > 1 ) {
                return ResultHelper::json(422,"只有相同订单下的配石单才可批量创建领石单");
             } 
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['peishi','ids'=>implode(',',$ids)])]);
        }
        
    }
    
}
