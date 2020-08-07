<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\models\WarehouseGiftBill;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Supply\common\models\Supplier;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\Url;

/**
 * 赠品库存
 */
class GiftBillController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGiftBill::class;

    /**
     * 列表
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab',2);
        $id = Yii::$app->request->get('id');
        $gift = WarehouseGift::find()->where(['id'=>$id])->one();
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gift/index', 'id'=>$id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['gift_name'], // 模糊查询
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
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGift::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGift::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->actionExport($dataProvider);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gift' => $gift,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->gift->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }


    /**
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null){
        $name = '赠品出入库';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['批次号', 'gift_sn' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['配件类型', 'gift_type' , 'text'],
            ['配件名称', 'gift_name' , 'text'],
            ['配件款号', 'style_sn' , 'text'],
            ['配件材质', 'material_type' , 'text'],
            ['配件形状', 'shape' , 'text'],
            ['配件颜色', 'color' , 'text'],
            ['链类型', 'chain_type' , 'text'],
            ['扣环', 'cramp_ring' , 'text'],
            ['尺寸', 'size' , 'text'],
            ['配件数量', 'gift_num' , 'text'],
            ['库存重量(g)', 'gift_weight' , 'text'],
            ['配件单价', 'gift_price' , 'text'],
            ['备注', 'remark' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['g.*','sup.supplier_name'];
        $query = WarehouseGift::find()->alias('g')
            ->leftJoin(Supplier::tableName().' sup','sup.id=g.supplier_id')
            ->where(['g.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            $list['gift_type'] = \Yii::$app->attr->valueName($list['gift_type']);
            $list['material_type'] = \Yii::$app->attr->valueName($list['material_type']);
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['color'] = \Yii::$app->attr->valueName($list['color']);
            $list['chain_type'] = \Yii::$app->attr->valueName($list['chain_type']);
            $list['cramp_ring'] = \Yii::$app->attr->valueName($list['cramp_ring']);
        }
        return [$lists,$total];
    }

}
