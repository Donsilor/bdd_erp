<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Supply\common\models\ProduceParts;
use addons\Supply\common\enums\PeijianStatusEnum;
use addons\Warehouse\common\enums\PartsBillTypeEnum;
use addons\Warehouse\common\enums\PartsBillStatusEnum;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\helpers\Url;

/**
 *
 * 配件列表
 */
class PartsApplyController extends BaseController
{
    use Curd;
    public $modelClass = ProduceParts::class;
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
     * 批量配件
     */
    public function actionPeijian()
    {
        $ids = Yii::$app->request->get('ids');
        if(empty($ids)) {
            return ResultHelper::json(422,'ids参数不能为空');
        }
        $ids = StringHelper::explodeIds($ids);
        if (Yii::$app->request->get('check')) {
            //数据校验
            foreach ($ids as $id) {
                $model = ProduceParts::find()->where(['id'=>$id])->one();
                if($model && $model->peijian_status >= PeijianStatusEnum::TO_LINGJIAN) {
                    return ResultHelper::json(422,"(ID={$id})配件单不允许批量配件");
                }
            }
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['peijian','ids'=>implode(',',$ids)])]);
        }
        
        if ($post = Yii::$app->request->post('ProduceParts')) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->supplyService->produceParts->batchPeijian($post);
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
        $dataProvider->query->andWhere([ProduceParts::tableName().'.id'=>$ids]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    
    /**
     * 创建领件单
     */
    public function actionLingjian()
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
                $model = ProduceParts::find()->where(['id'=>$id])->one();
                if(empty($model)) {
                    return ResultHelper::json(422,"(ID={$id})配件单不存在");
                }elseif($model->delivery_no != '') {
                    return ResultHelper::json(422,"(ID={$id})配件单已绑定领件单");
                }elseif($model->peijian_status != PeijianStatusEnum::HAS_PEIJIAN) {
                    return ResultHelper::json(422,"(ID={$id})配件单不允许创建领件单");
                }
                $order_sn_array[$model->from_order_sn] = $model->from_order_sn;
            }
            if(count($order_sn_array) > 1 ) {
                return ResultHelper::json(422,"只有相同订单下的配件单才可批量创建领件单");
            }
            return ResultHelper::json(200,'初始化成功',['url'=>Url::to(['lingjian','ids'=>implode(',',$ids)])]);
        }
        
        if ($post = Yii::$app->request->post('ProduceParts')) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                
                $bill = [
                        'bill_type' => PartsBillTypeEnum::PARTS_C,
                        'bill_status'=>PartsBillStatusEnum::SAVE,
                        'supplier_id' => $post['supplier_id'] ??'',
                        'remark' => $post['remark'] ??'',
                ];
                $details = [];
                foreach ($ids as $k=>$id) {
                    $model = ProduceParts::find()->where(['id'=>$id])->one();
                    if(empty($model)) {
                        throw new \Exception("(ID={$id})配件单不存在");
                    }elseif($model->peijian_status != PeijianStatusEnum::HAS_PEIJIAN) {
                        throw new \Exception("(ID={$id})配件单不是已配件状态");
                    }
                    $partsGoodsModels = $model->partsGoods ??[];
                    if(empty($partsGoodsModels)) {
                        throw new \Exception("(ID={$model->id})配件数据异常");
                    }
                    foreach ($partsGoodsModels as $partsGoods) {
                        $details[] = [
                                'parts_name'=>$partsGoods->parts->parts_name,
                                'parts_sn'=>$partsGoods->parts->parts_sn,
                                'parts_type'=>$partsGoods->parts->parts_type,
                                'style_sn'=>$partsGoods->parts->style_sn,
                                'parts_weight'=>$partsGoods->parts_weight,
                                'cost_price'=>$partsGoods->parts->cost_price,
                                'parts_price'=>$partsGoods->parts->parts_price,
                                'sale_price'=>$partsGoods->parts->sale_price,
                                'source_detail_id' => $partsGoods->id,                                
                        ];
                    }
                }
                //创建单据
                $bill = Yii::$app->warehouseService->partsC->createPartsC($bill,$details);
                //绑定领件单
                ProduceParts::updateAll(['delivery_no'=>$bill->bill_no],['id'=>$ids]);
                
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
        $dataProvider->query->andWhere([ProduceParts::tableName().'.id'=>$ids]);
        
        $model = $this->findModel($ids[0]);
        $model->remark = '';
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'model'=>$model,
        ]);
        
    }
    
}
