<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;



/**
 *
 * 配料列表
 */
class GoldApplyController extends BaseController
{
    use Curd;
    public $modelClass = ProduceGold::class;
    /**
     * 
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
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
        
    }
    
    /**
     * 批量配料
     */
    public function actionPeiliao()
    {
        $ids = Yii::$app->request->get('ids');
        if(empty($ids)) {
            return ResultHelper::json(422,'ids参数不能为空');
        }
        $ids = StringHelper::explodeIds($ids);
        if (Yii::$app->request->get('check')) {
            //数据校验
            foreach ($ids as $id) {
                $model = ProduceGold::find()->where(['id'=>$id])->one();
                if($model && $model->peiliao_status >= PeiliaoStatusEnum::TO_LINGLIAO) {
                    return ResultHelper::json(422,"(ID={$id})配料单不允许批量配料");
                }
            }
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['peiliao','ids'=>implode(',',$ids)])]);
        }
        
        if ($post = Yii::$app->request->post('ProduceGold')) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->supplyService->produceGold->batchPeiliao($post);
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
                ],
                'relations' => [
                        
                ],
                'pageSize' => $this->getPageSize(100),
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([ProduceGold::tableName().'.id'=>$ids]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    
    /**
     * 创建领料单
     */
    public function actionLingliao()
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
                $model = ProduceGold::find()->where(['id'=>$id])->one();
                if(empty($model)) {
                    return ResultHelper::json(422,"(ID={$id})配料单不存在");
                }elseif($model->delivery_no != '') {
                    return ResultHelper::json(422,"(ID={$id})配料单已绑定领料单");
                }elseif($model->peiliao_status != PeiliaoStatusEnum::HAS_PEILIAO) {
                    return ResultHelper::json(422,"(ID={$id})配料单不允许创建领料单");
                }
                $order_sn_array[$model->from_order_sn] = $model->from_order_sn;
            }
            if(count($order_sn_array) > 1 ) {
                return ResultHelper::json(422,"只有相同订单下的配料单才可批量创建领料单");
            }
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['lingliao','ids'=>implode(',',$ids)])]);
        }
        
        if ($post = Yii::$app->request->post('ProduceGold')) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                
                $bill = [
                        'bill_type' => GoldBillTypeEnum::GOLD_C,
                        'bill_status'=>GoldBillStatusEnum::SAVE,
                        'supplier_id' => $post['supplier_id'] ??'',
                        'remark' => $post['remark'] ??'',
                ];
                $details = [];
                foreach ($ids as $k=>$id) {
                    $model = ProduceGold::find()->where(['id'=>$id])->one();
                    if(empty($model)) {
                        throw new \Exception("(ID={$id})配料单不存在");
                    }elseif($model->peiliao_status != PeiliaoStatusEnum::HAS_PEILIAO) {
                        throw new \Exception("(ID={$id})配料单不是已配料状态");
                    }
                    $goldGoodsModels = $model->goldGoods ??[];
                    if(empty($goldGoodsModels)) {
                        throw new \Exception("(ID={$model->id})配料数据异常");
                    }
                    foreach ($goldGoodsModels as $goldGoods) {
                        $details[] = [
                                'gold_name'=>$goldGoods->gold->gold_name,
                                'gold_sn'=>$goldGoods->gold->gold_sn,
                                'gold_type'=>$goldGoods->gold->gold_type,
                                'style_sn'=>$goldGoods->gold->style_sn,
                                'gold_weight'=>$goldGoods->gold_weight,
                                'cost_price'=>$goldGoods->gold->cost_price,
                                'gold_price'=>$goldGoods->gold->gold_price,
                                'sale_price'=>$goldGoods->gold->sale_price,
                                'source_detail_id' => $goldGoods->id,                                
                        ];
                    }
                }
                //创建单据
                $bill = Yii::$app->warehouseService->goldC->createGoldC($bill,$details);
                //绑定领料单
                ProduceGold::updateAll(['delivery_no'=>$bill->bill_no],['id'=>$ids]);
                
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
        $dataProvider->query->andWhere([ProduceGold::tableName().'.id'=>$ids]);
        
        $model = $this->findModel($ids[0]);
        $model->remark = '';
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'model'=>$model,
        ]);
        
    }
    
}
