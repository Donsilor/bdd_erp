<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillMForm;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\helpers\ExcelHelper;
use common\helpers\Html;
use common\helpers\SnHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use yii\db\Exception;


/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class WarehouseBillMController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillMForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_M;

    /**
     * 调拨单
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
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['updated_at']);

        $dataProvider->key = 'id';

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type','M']);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            print_r($list);exit;
            $this->actionExport($ids);
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        $to_warehouse_id = $model->to_warehouse_id;

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                    $log_msg = "创建调拨单{$model->bill_no}，入库仓库为{$model->toWarehouse->name}";
                }else{
                    $log_msg = "修改调拨单{$model->bill_no}，入库仓库为{$model->toWarehouse->name}";
                }

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                if(!($model->isNewRecord) && $model->to_warehouse_id != $to_warehouse_id){
                    //编辑单据明细所有入库仓库
                    WarehouseBillGoods::updateAll(['to_warehouse_id' => $model->to_warehouse_id],['bill_id' => $model->id]);
                }

                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调拨单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->bill->createWarehouseBillLog($log);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = \Yii::$app->request->get('id');
        $tab = \Yii::$app->request->get('tab',1);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['warehouse-bill-m/index']));
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id,$this->billType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = BillStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }


    /**
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->bill_status = BillStatusEnum::CONFIRM; //单据状态改成审核
                    //更新库存状态和仓库
                    $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
                    foreach ($billGoods as $goods){
                        $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK, 'warehouse_id' => $model->to_warehouse_id],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_TRANSFER]);
                        if(!$res){
                            throw new Exception("商品{$goods->goods_id}不是调拨中或者不存在，请查看原因");
                        }
                    }
                }else{
                    $model->bill_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                //日志
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调拨单',
                    'log_msg' => '单据审核：'.AuditStatusEnum::getValue($model->audit_status)
                ];
                \Yii::$app->warehouseService->bill->createWarehouseBillLog($log);
                \Yii::$app->getSession()->setFlash('success','保存成功');
                $trans->commit();
                return $this->redirect(\Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 删除/关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        try{
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            //更新库存状态
            $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_TRANSFER]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是调拨中或者不存在，请查看原因");
                }
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '调拨单',
                'log_msg' => '单据取消'
            ];
            \Yii::$app->warehouseService->bill->createWarehouseBillLog($log);
            \Yii::$app->getSession()->setFlash('success','删除成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }


        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }


    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $ids = Yii::$app->request->get('ids');
        $id_arr = explode(',', $ids);
        $id = $id_arr[0];//暂时打印一个
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-receipt/index']));
        $model = $this->findModel($id);
        $goodsModel = new PurchaseReceiptGoods();
        $goodsList = $goodsModel::find()->where(['receipt_id' => $id])->asArray()->all();
        foreach ($goodsList as &$item) {
            $item['stone_zhong'] = $item['main_stone_weight']+$item['second_stone_weight1']+$item['second_stone_weight2']+$item['second_stone_weight3'];
            $item['han_tax_price'] = $item['cost_price'] + $item['tax_fee'];
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'goodsList' => $goodsList,
            'tab'=>$tab,
            'returnUrl'=>$returnUrl,
        ]);
    }


    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '调拨单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['w.bill_no','w.bill_type','w.bill_status','g.goods_id','wg.style_sn','wg.goods_name','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','type.name as product_type_name','cate.name as style_cate_name'];

        $list = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select)->asArray()->all();
        $header = [
            ['单据编号', 'bill_no' , 'text'],
            ['单据类型', 'bill_type' , 'selectd', BillTypeEnum::getMap()],
            ['单据状态', 'bill_status' , 'selectd',BillStatusEnum::getMap()],
            ['货号', 'goods_id' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['款式分类', 'style_cate_name' , 'text'],
            ['入库方式', 'put_in_type' , 'selectd',PutInTypeEnum::getMap()],
            ['主成色', 'material' , function($model){
                return \Yii::$app->attr->valueName($model['material']);
            }],
            ['金重', 'gold_weight' , 'text'],
            ['金损', 'gold_loss' , 'text'],
            ['钻石大小', 'diamond_carat' , 'text'],
            ['钻石颜色', 'diamond_color' , function($model){
                return \Yii::$app->attr->valueName($model['diamond_color']);
            }],
            ['钻石净度', 'diamond_clarity' ,function($model){
                return \Yii::$app->attr->valueName($model['diamond_clarity']);
            }],
            ['证书号', 'diamond_cert_id' , 'text'],
            ['成本价', 'cost_price' , 'text']

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }








}
