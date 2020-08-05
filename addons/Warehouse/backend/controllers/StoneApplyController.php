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
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use common\helpers\SnHelper;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;



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
                if($model && $model->peishi_status >= PeishiStatusEnum::TO_LINGSHI) {
                     return ResultHelper::json(422,"(ID={$id})配石单状态不允许批量配石操作");
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
     * 创建领石单
     */
    public function actionLingshi()
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
                if(empty($model)) {
                    return ResultHelper::json(422,"(ID={$id})配石单不存在");
                }elseif($model->delivery_no != '') {
                    return ResultHelper::json(422,"(ID={$id})配石单已绑定领石单");
                }elseif($model->peishi_status != PeishiStatusEnum::HAS_PEISHI) {
                    return ResultHelper::json(422,"(ID={$id})配石单不允许创建领料单");
                }
                $order_sn_array[$model->from_order_sn] = $model->from_order_sn;
            }
            if(count($order_sn_array) > 1 ) {
                return ResultHelper::json(422,"只有相同订单下的配石单才可批量创建领石单");
            } 
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['lingshi','ids'=>implode(',',$ids)])]);
        }
        
        if ($post = Yii::$app->request->post('ProduceStone')) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                
                $bill = [
                        'bill_type' => StoneBillTypeEnum::STONE_SS,
                        'bill_status'=> GoldBillStatusEnum::SAVE,
                        'supplier_id' => $post['supplier_id'] ??'',
                        'remark' => $post['remark'] ??'',
                ];
                $details = [];
                foreach ($ids as $k=>$id) {
                    $model = ProduceStone::find()->where(['id'=>$id])->one();
                    if(empty($model)) {
                        throw new \Exception("(ID={$id})配石单不存在");
                    }elseif($model->peishi_status != PeishiStatusEnum::HAS_PEISHI) {
                        throw new \Exception("(ID={$id})配石单不是已配石状态");
                    }
                    $stoneGoodsModels = $model->stoneGoods ??[];
                    if(empty($stoneGoodsModels)) {
                        throw new \Exception("(ID={$model->id})配石数据异常");
                    }
                    foreach ($stoneGoodsModels as $stoneGoods) {
                        $details[] = [
                                'stone_name'=>$stoneGoods->stone->stone_name,
                                'stone_sn'=>$stoneGoods->stone->stone_sn,
                                'stone_type'=>$stoneGoods->stone->stone_type,
                                'style_sn'=>$stoneGoods->stone->style_sn,
                                'color'=>$stoneGoods->stone->stone_color,
                                'clarity'=>$stoneGoods->stone->stone_clarity,
                                'cut'=>$stoneGoods->stone->stone_cut,
                                'polish'=>$stoneGoods->stone->stone_polish,
                                'fluorescence'=>$stoneGoods->stone->stone_fluorescence,
                                'symmetry'=>$stoneGoods->stone->stone_symmetry,                            
                                'cost_price'=>$stoneGoods->stone->cost_price,
                                'stone_price'=>$stoneGoods->stone->stone_price,
                                'sale_price'=>$stoneGoods->stone->sale_price,
                                'source_detail_id' => $stoneGoods->id,
                                'stone_num'=>$stoneGoods->stone_num,
                                'stone_weight'=>$stoneGoods->stone_weight
                        ];
                    }
                }
                //创建单据
                $bill = Yii::$app->warehouseService->stoneSs->createBillSs($bill,$details);
               //绑定领石单
                ProduceStone::updateAll(['delivery_no'=>$bill->bill_no],['id'=>$ids]);
                
                $trans->commit();               
               
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200,"保存成功");
            } catch (\Exception $e){
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
                ],
                'relations' => [
                        
                ],
                'pageSize' => $this->getPageSize(100),
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([ProduceStone::tableName().'.id'=>$ids]);
        
        $model = $this->findModel($ids[0]);
        $model->remark = '';
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'model'=>$model,
        ]);
        
    }
    
}
