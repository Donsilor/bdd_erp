<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Warehouse\common\forms\WarehousePartsForm;
use addons\Warehouse\common\forms\WarehousePartsBillGoodsForm;
use addons\Warehouse\common\enums\PartsBillTypeEnum;
use addons\Supply\common\models\Supplier;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\Url;

/**
 * PartsController implements the CRUD actions for StyleChannel model.
 */
class PartsController extends BaseController
{
    use Curd;
    public $modelClass = WarehousePartsForm::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['parts_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseParts::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseParts::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',WarehouseParts::tableName().'.status',-1]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->actionExport($dataProvider);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);


    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['parts/index', 'id'=>$id]));
        $model = $this->findModel($id);
        $model = $model ?? new WarehousePartsForm();
        $bill = $model->getBillInfo();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->parts->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
            'bill'=>$bill,
        ]);
    }

    /**
     * 领件信息
     * @return mixed
     * @throws
     */
    public function actionLingjian()
    {
        $id = \Yii::$app->request->get('id');
        $this->modelClass = new WarehousePartsBillGoodsForm();
        $tab = \Yii::$app->request->get('tab',2);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['parts/index', 'id'=>$id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'bill' => ['audit_time'],
            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehousePartsBillGoodsForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehousePartsBillGoodsForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $parts = WarehouseParts::findOne(['id'=>$id]);
        $dataProvider->query->andWhere(['=', 'parts_sn', $parts->parts_sn]);
        $dataProvider->query->andWhere(['>',WarehousePartsBillGoodsForm::tableName().'.status',-1]);

        $dataProvider->query->andWhere(['=', 'bill.bill_type', PartsBillTypeEnum::PARTS_C]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'parts' => $parts,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->parts->menuTabList($id, $returnUrl),
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null){
        $name = '配件';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['批次号', 'parts_sn' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['配件类型', 'parts_type' , 'text'],
            ['配件名称', 'parts_name' , 'text'],
            ['配件款号', 'style_sn' , 'text'],
            ['配件材质', 'material_type' , 'text'],
            ['配件形状', 'shape' , 'text'],
            ['配件颜色', 'color' , 'text'],
            ['链类型', 'chain_type' , 'text'],
            ['扣环', 'cramp_ring' , 'text'],
            ['尺寸', 'size' , 'text'],
            ['配件数量', 'parts_num' , 'text'],
            ['库存重量(g)', 'parts_weight' , 'text'],
            ['配件单价', 'parts_price' , 'text'],
            ['备注', 'remark' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['g.*','sup.supplier_name'];
        $query = WarehouseParts::find()->alias('g')
            ->leftJoin(Supplier::tableName().' sup','sup.id=g.supplier_id')
            ->where(['g.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            $list['parts_type'] = \Yii::$app->attr->valueName($list['parts_type']);
            $list['material_type'] = \Yii::$app->attr->valueName($list['material_type']);
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['color'] = \Yii::$app->attr->valueName($list['color']);
            $list['chain_type'] = \Yii::$app->attr->valueName($list['chain_type']);
            $list['cramp_ring'] = \Yii::$app->attr->valueName($list['cramp_ring']);
        }
        return [$lists,$total];
    }

}
