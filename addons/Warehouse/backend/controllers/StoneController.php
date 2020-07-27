<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseStoneBillGoodsForm;
use addons\Warehouse\common\forms\WarehouseStoneBillMsForm;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\helpers\PageHelper;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\forms\WarehouseStoneForm;
use addons\Warehouse\common\models\WarehouseStone;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StoneController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseStoneForm::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['stone_name'], // 模糊查询
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseStone::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseStone::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',WarehouseStone::tableName().'.status',-1]);

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
     * 领石信息
     * @return mixed
     */
    public function actionLingshi()
    {
        $this->modelClass = new WarehouseStoneBillGoodsForm();
        $tab = \Yii::$app->request->get('tab',2);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['stone/index']));
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseStoneBillGoodsForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseStoneBillGoodsForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $id = \Yii::$app->request->get('id');
        $stone = WarehouseStone::findOne(['id'=>$id]);
        $dataProvider->query->andWhere(['=', 'stone_sn', $stone->stone_sn]);
        $dataProvider->query->andWhere(['>',WarehouseStoneBillGoodsForm::tableName().'.status',-1]);

        $dataProvider->query->andWhere(['=', 'bill.bill_type', StoneBillTypeEnum::STONE_SS]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'stone' => $stone,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->stone->menuTabList($id, $returnUrl),
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = \Yii::$app->request->get('id');
        $tab = \Yii::$app->request->get('tab',1);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['stone/index']));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneForm();
        $bill = $model->getBillInfo();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->stone->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
            'bill'=>$bill,
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '石料';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('ID不能为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['石料编号', 'stone_sn' , 'text'],
            ['名称', 'stone_name' , 'text'],
            ['石类', 'stone_type' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['石头颜色', 'stone_color' , 'text'],
            ['石头形状', 'stone_shape' , 'text'],
            ['库存数量', 'stock_cnt' , 'text'],
            ['库存重量', 'stock_weight' , 'text'],
            ['尺寸', 'stone_size' , 'text'],
            ['规格(颜色/净度/切工)', 'spec' , 'text'],
            ['单价', 'stone_price' , 'text'],
            //['总价格', 'stone_sum_price' , 'text'],
            ['备注', 'remark' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['s.*'];
        $query = WarehouseStone::find()->alias('s')
            ->where(['s.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            //'stone_num_count' => 0,
            //'stone_sum_price_count' => 0,

        ];
        foreach ($lists as &$list){
            $list['stone_type'] = \Yii::$app->attr->valueName($list['stone_type']);
            $clarity = \Yii::$app->attr->valueName($list['stone_clarity']);
            $cut = $list['stone_cut'];
            $color = \Yii::$app->attr->valueName($list['stone_color']);
            $list['stone_color'] = $color;
            $list['stone_shape'] = \Yii::$app->attr->valueName($list['stone_shape']);
            $list['spec'] = $color.'/'.$clarity.'/'
                .$cut;
            //$list['stone_sum_price'] = $list['stone_price'] * $list['stone_weight'];
            //$total['stone_num_count'] += $list['stone_num'];
            //$total['stone_sum_price_count'] += $list['stone_sum_price'];
        }
        return [$lists,$total];
    }

    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {


        $this->layout = '@backend/views/layouts/print';
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists,$total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    /**
     * 导出
     * @return string
     * @throws
     */
    public function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '数据导出_' . time());

    }

}
