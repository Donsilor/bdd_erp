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
     * 批量配石
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
                if($model && $model->peiliao_status != PeiliaoStatusEnum::PENDING) {
                    return ResultHelper::json(422,"(ID={$id})配料单不是待配料状态");
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
    
}
