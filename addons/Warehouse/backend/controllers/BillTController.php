<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillL;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillTForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\BillFixEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\enums\GoodsTypeEnum;
use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Sales\common\models\SaleChannel;
use addons\Supply\common\models\Supplier;
use common\enums\AuditStatusEnum;
use common\models\backend\Member;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\ResultHelper;
use common\helpers\Url;
use yii\web\UploadedFile;

/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillTController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillTForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_T;
    public $billFix = BillFixEnum::BILL_RK;

    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name', 'remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'billL' => ['goods_type'],
            ]
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, ['created_at', 'audit_time', 'goods_type']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', Warehousebill::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', Warehousebill::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }
        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=', Warehousebill::tableName() . '.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', Warehousebill::tableName() . '.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)]);//结束时间
        }
        $goods_type = $searchModel->goods_type;
        if (!empty($goods_type)) {
            $dataProvider->query->andWhere(['=', 'billL.goods_type', $goods_type]);
        }
        $dataProvider->query->andWhere(['>', Warehousebill::tableName() . '.status', -1]);
        $dataProvider->query->andWhere(['=', Warehousebill::tableName() . '.bill_type', $this->billType]);

        //导出
        if (\Yii::$app->request->get('action') === 'export') {
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list, 'id');
            $this->actionExport($ids);
        }

        return $this->render($this->action->id, [
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
        $model = $model ?? new WarehouseBillTForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                $goods_type = $model->goods_type;
                if ($isNewRecord) {
                    //$model->bill_no = SnHelper::createBillSn($this->billType);
                    if (!$model->bill_no) {
                        $model->bill_no = \Yii::$app->warehouseService->bill->createBillSn($this->billFix);
                    }
                    $model->bill_type = $this->billType;
                }
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $gModel = new WarehouseBillTGoodsForm();
                $gModel->file = UploadedFile::getInstance($model, 'file');
                if ($isNewRecord) {
                    $gModel->bill_id = $model->id;
                    $gModel->supplier_id = $model->supplier_id;
                    $gModel->put_in_type = $model->put_in_type;
                    //$gModel->goods_type = $goods_type;
                    if (!empty($gModel->file) && isset($gModel->file)) {
                        \Yii::$app->warehouseService->billT->uploadGoods($gModel);
                    }
                    $log_msg = "创建其它入库单{$model->bill_no}";
                } else {
                    $log_msg = "修改其它入库单{$model->bill_no}";
                }
                if (empty($gModel->file)) {
                    //创建收货单附属表
                    $billT = WarehouseBillL::findOne($model->id);
                    $billT = $billT ?? new WarehouseBillL();
                    $billT->id = $model->id;
                    $billT->goods_type = $goods_type ?? 0;
                    if (false === $billT->save()) {
                        throw new \Exception($this->getError($billT));
                    }
                }
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它入库单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                \Yii::$app->warehouseService->billT->warehouseBillTSummary($model->id);
                $trans->commit();
                if ($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['bill-t-goods/index', 'bill_id' => $model->id]), 'success');
                } else {
                    return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
                }
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        $model->put_in_type = PutInTypeEnum::PURCHASE;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
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
        $tab = Yii::$app->request->get('tab', 1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t/index', 'id' => $id]));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($id, $this->billType, $returnUrl),
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        if ($model->bill_status != BillStatusEnum::SAVE) {
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if ($model->goods_num <= 0) {
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->warehouseService->billT->syncUpdatePriceAll($model);
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其它入库单',
                'log_msg' => '单据提审'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

    }

    /**
     *
     * ajax收货单审核
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->billL->auditBillL($model);
                //日志
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它入库单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                //var_dump($e->getTraceAsString());die;
                return $this->message("审核失败:" . $e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * 同步更新价格
     * @param $id
     * @return mixed
     */
    /* public function actionSyncUpdatePrice($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();

            \Yii::$app->warehouseService->billT->syncUpdatePriceAll($id);

            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billT->warehouseBillTSummary($id);
            if (false === $res) {
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '更新成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    } */

    /**
     *
     * 取消单据
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionCancel($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据取消',
                'log_msg' => '取消其它收货单'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success', '操作成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 删除单据
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();

            if (false === WarehouseBillGoodsL::deleteAll(['bill_id' => $id])) {
                throw new \Exception("单据明细删除失败");
            }
            if (false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            $billL = WarehouseBillL::findOne($id);
            if ($billL) {
                $billL->delete();
            }
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据删除',
                'log_msg' => '删除其它入库单'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success', '操作成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 单据打印
     * @return string
     * @throws
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = Yii::$app->request->get('id');
        if (!$id) {
            exit("ID不能为空");
        }
        $model = $this->findModel($id);
        if (!$model) {
            exit("单据不存在");
        }
        $model = $model ?? new WarehouseBillTForm();
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    /**
     * 单据导出
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null)
    {
        $name = '其他入库单明细';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['wb.bill_no', 'creator.username as creator_name', 'wb.created_at', 'auditor.username as auditor_name', 'wb.bill_status', 'wb.supplier_id',
            'wg.*', 'type.name as product_type_name', 'cate.name as style_cate_name', 'supplier.supplier_name',
            'w.name as warehouse_name'];
        $query = WarehouseBill::find()->alias('wb')
            ->innerJoin(WarehouseBillGoodsL::tableName() . " wg", 'wb.id=wg.bill_id')
            ->leftJoin(Warehouse::tableName() . ' w', 'w.id=wg.to_warehouse_id')
            ->leftJoin(ProductType::tableName() . ' type', 'type.id=wg.product_type_id')
            ->leftJoin(Supplier::tableName() . ' supplier', 'supplier.id=wb.supplier_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=wg.style_cate_id')
            ->leftJoin(Member::tableName() . ' creator', 'creator.id=wb.creator_id')
            ->leftJoin(Member::tableName() . ' auditor', 'auditor.id=wb.auditor_id')
            ->where(['wb.id' => $ids])
            ->select($select);
        $list = PageHelper::findAll($query, 100);

        $header = [
            ['入库单号', 'bill_no', 'text'],
            ['供应商', 'supplier_name', 'text'],
            ['创建人', 'creator_name', 'text'],
            ['创建时间', 'created_at', 'date', 'Y-m-d'],
            ['审核人', 'auditor_name', 'text'],
            ['款式分类', 'style_cate_name', 'text'],
            ['产品分类', 'product_type_name', 'text'],
            ['货号', 'goods_id', 'text'],
            ['款号', 'goods_sn', 'text'],
            ['商品名称', 'goods_name', 'text'],
            ['起版号', 'qiban_sn', 'text'],
            ['入库仓库', 'warehouse_name', 'text'],
            ['材质', 'material_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['material_type']);
            }],
            ['材质颜色', 'material_color', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['material_color']);
            }],
            ['商品数量', 'goods_num', 'text'],
            ['手寸(港)', 'finger_hk', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['finger_hk']);
            }],
            ['手寸(美)', 'finger', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['finger']);
            }],
            ['尺寸(mm)', 'length', 'text'],
            ['成品尺寸(mm)', 'product_size', 'text'],
            ['戒托镶口(ct)', 'xiangkou', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['xiangkou']);
            }],
            ['刻字', 'kezi', 'text'],
            ['链类型', 'chain_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['chain_type']);
            }],
            ['扣环', 'cramp_ring', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['cramp_ring']);
            }],
            ['爪头形状', 'talon_head_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['talon_head_type']);
            }],
//            ['配料方式','peiliao_way', 'text', function ($model) {
//                return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getValue($model['peiliao_way']);
//            }],
            ['连石重[净重](g)', 'suttle_weight', 'text'],
            ['金重(g)', 'gold_weight', 'text'],
            ['损耗[金损](%)', 'gold_loss', 'text', function ($model) {
                $gold_loss = $model['gold_loss'];
                return $gold_loss * 100;
            }],
            ['含耗重(g)', 'lncl_loss_weight', 'text'],
            ['金价/g', 'gold_price', 'text'],
            ['金料额', 'gold_amount', 'text'],
            ['折足率(%)', 'pure_gold_rate', 'function', function ($model) {
                return $pure_gold_rate = $model['pure_gold_rate'];
            }],
            ['折足(g)', 'pure_gold', 'text'],
//            ['主石配石方式','main_pei_type', 'function', function ($model) {
//                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model['main_pei_type']);
//            }],
            ['主石编号', 'main_stone_sn', 'text'],
            ['主石类型', 'main_stone_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_type']);
            }],
            ['主石粒数', 'main_stone_num', 'text'],
            ['主石重(ct)', 'main_stone_weight', 'text'],
            ['主石单价/ct', 'main_stone_price', 'text'],
            ['主石成本价', 'main_stone_amount', 'text'],
            ['主石形状', 'main_stone_shape', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_shape']);
            }],
            ['主石颜色', 'main_stone_color', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_color']);
            }],
            ['主石净度', 'main_stone_clarity', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_clarity']);
            }],
            ['主石切工', 'main_stone_cut', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_cut']);
            }],
            ['主石色彩', 'main_stone_colour', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_colour']);
            }],
            ['主石证书号', 'main_cert_id', 'text'],
            ['主石证书类型', 'main_cert_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_cert_type']);
            }],
//            ['副石1配石方式','second_pei_type', 'function', function ($model) {
//                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model['second_pei_type']);
//            }],
            ['副石1类型', 'second_stone_type1', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['second_stone_type1']);
            }],
            ['副石1编号', 'second_stone_sn1', 'text'],
            ['副石1粒数', 'second_stone_num1', 'text'],
            ['副石1重(ct)', 'second_stone_weight1', 'text'],
            ['副石1单价/ct	', 'second_stone_price1', 'text'],
            ['副石1成本价	', 'second_stone_amount1', 'text'],
            ['副石1形状', 'second_stone_shape1', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['second_stone_shape1']);
            }],
            ['副石1颜色', 'second_stone_color1', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['second_stone_color1']);
            }],
            ['副石1净度', 'second_stone_clarity1', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['second_stone_clarity1']);
            }],
            ['副石1切工', 'second_stone_cut1', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['second_stone_cut1']);
            }],
            ['副石1色彩', 'second_stone_colour1', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['second_stone_colour1']);
            }],

//            ['副石2配石方式','second_pei_type2', 'function', function ($model) {
//                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model['second_pei_type2']);
//            }],
//            ['副石2类型','second_stone_type2', 'function', function ($model) {
//                return \Yii::$app->attr->valueName($model['second_stone_type2']);
//            }],
            ['副石2编号', 'second_stone_sn2', 'text'],
            ['副石2粒数', 'second_stone_num2', 'text'],
            ['副石2重(ct)', 'second_stone_weight2', 'text'],
            ['副石2单价/ct	', 'second_stone_price2', 'text'],
            ['副石2成本价	', 'second_stone_amount2', 'text'],

            ['副石3配石方式', 'second_pei_type3', 'function', function ($model) {
                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model['second_pei_type3']);
            }],
//            ['副石3类型','second_stone_type3', 'function', function ($model) {
//                return \Yii::$app->attr->valueName($model['second_stone_type3']);
//            }],
            ['副石3编号', 'second_stone_sn3', 'text'],
            ['副石3粒数', 'second_stone_num3', 'text'],
            ['副石3重(ct)', 'second_stone_weight3', 'text'],
            ['副石3单价/ct	', 'second_stone_price3', 'text'],
            ['副石3成本价	', 'second_stone_amount3', 'text'],
            ['石料备注', 'stone_remark', 'text'],
//            ['配件方式','parts_way', 'function', function ($model) {
//                return \addons\Warehouse\common\enums\PeiJianWayEnum::getValue($model['parts_way']);
//            }],
//            ['配件类型','parts_type', 'function', function ($model) {
//                return \Yii::$app->attr->valueName($model['parts_type']);
//            }],
            ['配件材质', 'parts_material', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['parts_material']);
            }],
            ['配件数量	', 'parts_num', 'text'],
            ['配件金重(g)', 'parts_gold_weight', 'text'],
            ['配件金价/g', 'parts_price', 'text'],
            ['配件总额', 'parts_amount', 'text'],
            ['克工费/g', 'gong_fee', 'text'],
            ['件/工费', 'piece_fee', 'text'],
            ['基本工费', 'basic_gong_fee', 'text'],
            ['配石重量(ct)', 'peishi_weight', 'text'],
            ['配石工费/ct', 'peishi_gong_fee', 'text'],
            ['配石费', 'peishi_fee', 'text'],
            ['配件工费', 'parts_fee', 'text'],
            ['镶嵌工艺', 'xiangqian_craft', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['xiangqian_craft']);
            }],
            ['镶石1工费', 'second_stone_fee1', 'text'],
            ['镶石2工费', 'second_stone_fee2', 'text'],
            ['镶石3工费	', 'second_stone_fee3', 'text'],
            ['镶石费', 'xianqian_fee', 'text'],
            ['表面工艺', 'biaomiangongyi', 'function', function ($model) {
                if (!empty($model['biaomiangongyi'])) {
                    $biaomiangongyi = explode(',', $model['biaomiangongyi']);
                    $biaomiangongyi = array_filter($biaomiangongyi);
                    $arr = [];
                    foreach ($biaomiangongyi as $item) {
                        $arr[] = \Yii::$app->attr->valueName($item);
                    }
                    return implode(",", $arr) ?? "";
                }
                return "";
            }],
            ['表面工艺费', 'biaomiangongyi_fee', 'text'],
            ['分色/分件费', 'fense_fee', 'text'],
            ['喷沙费', 'penlasha_fee', 'text'],
            ['喷沙费', 'penlasha_fee', 'text'],
            ['拉沙费', 'lasha_fee', 'text'],
            ['补口费', 'bukou_fee', 'text'],
            ['版费', 'templet_fee', 'text'],
            ['税费/克', 'tax_fee', 'text'],
            ['税额', 'tax_amount', 'text'],
            ['证书费', 'cert_fee', 'text'],
            ['其它工费', 'other_fee', 'text'],
            ['工厂总成本', 'factory_cost', 'text'],
            ['总成本/件', 'cost_price', 'function', function ($model) {
                $cost_price = bcsub($model['cost_price'], $model['templet_fee'], 3);
                return bcdiv($cost_price, $model['goods_num'], 3) ?? "0.00";
            }],
            ['公司总成本(成本价)', 'cost_price', 'text'],
            ['倍率[加价率]', 'markup_rate', 'text'],
            ['标签价(市场价)', 'market_price', 'text'],
            ['款式性别', 'style_sex', 'function', function ($model) {
                return \addons\Style\common\enums\StyleSexEnum::getValue($model['style_sex']);
            }],
//            ['金托类型','jintuo_type', 'function', function ($model) {
//                return \addons\Style\common\enums\JintuoTypeEnum::getValue($model['jintuo_type']);
//            }],
//            ['起版类型','qiban_type', 'function', function ($model) {
//                return \addons\Style\common\enums\QibanTypeEnum::getValue($model['qiban_type']);
//            }],

        ];
        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }

    private function getData($ids)
    {
        $select = [
            'w.bill_no', 'w.bill_type', 'w.bill_status', 'wg.goods_id', 'wg.style_sn', 'wg.goods_num', 'wg.goods_name', 'sc.name as channel_name', 'sc.code as channel_code',//基本
            'wg.material_type', 'wg.finger', 'wg.finger_hk',//属性
            'wg.suttle_weight', 'wg.gold_weight', 'wg.gold_loss', 'wg.lncl_loss_weight', 'wg.gold_price', 'wg.gold_amount', 'factory_gold_weight',//金料
            'wg.main_stone_sn', 'wg.main_stone_num', 'wg.main_stone_weight', 'wg.main_stone_price', 'wg.main_stone_amount',//主石
            'wg.second_stone_sn1', 'wg.second_stone_num1', 'wg.second_stone_weight1', 'wg.second_stone_price1', 'wg.second_stone_amount1',//副石1
            'parts_gold_weight', 'parts_amount', 'parts_fee',//配件
            'basic_gong_fee', 'xianqian_fee', 'biaomiangongyi_fee', 'fense_fee', 'bukou_fee', 'templet_fee', 'tax_amount',//工费
            'wg.cert_id', 'wg.pure_gold', 'wg.factory_cost', 'wg.cost_price',//成本
        ];
        $query = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoodsL::tableName() . " wg", 'w.id=wg.bill_id')
            ->leftJoin(ProductType::tableName() . ' type', 'type.id=wg.product_type_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=wg.style_cate_id')
            ->leftJoin(Warehouse::tableName() . ' wh', 'wh.id=wg.to_warehouse_id')
            ->leftJoin(SaleChannel::tableName() . ' sc', 'sc.id=wh.channel_id')
            ->where(['w.id' => $ids])
            ->select($select)->orderBy(['wg.id' => SORT_DESC]);
        $lists = PageHelper::findAll($query, 100);
//        echo '<pre>';
//        print_r($lists);die;
        $total = [
            'goods_num' => 0,
            'suttle_weight' => 0,
            'gold_weight' => 0,
            'lncl_loss_weight' => 0,
            'gold_amount' => 0,
            'factory_gold_weight' => 0,

            'main_stone_num' => 0,
            'main_stone_weight' => 0,
            'main_stone_amount' => 0,
            'second_stone_num1' => 0,
            'second_stone_weight1' => 0,
            'second_stone_amount1' => 0,

            'parts_gold_weight' => 0,
            'parts_amount' => 0,
            'parts_fee' => 0,
            'basic_gong_fee' => 0,
            'xianqian_fee' => 0,
            'biaomiangongyi_fee' => 0,
            'fense_fee' => 0,
            'bukou_fee' => 0,
            'templet_fee' => 0,

            'tax_amount' => 0,
            'pure_gold' => 0,
            'factory_cost' => 0,
            'one_cost_price' => 0,
            'cost_price' => 0,

            'gold_price' => 0,
            'channel' => "",
        ];
        $channel = [];
        foreach ($lists as &$list) {
            if (empty($list['goods_id'])) {
                exit("货号不能为空");
            }
            if ($list['channel_name']) {
                $channel[] = $list['channel_name'] ?? '';
            }
            //金价
            if ($total['gold_price'] == 0 && $list['gold_price']) {
                $total['gold_price'] = $list['gold_price'];
            }
            //商品名称
            if ($list['goods_name']) {
                $list['goods_name'] = mb_substr($list['goods_name'], 0, 6, 'utf-8') . "...";
            }
            //主石编号
            if ($list['main_stone_sn']) {
                $list['main_stone_sn'] = substr($list['main_stone_sn'], 0, 3) . "...";
            }
            //副石1编号
            if ($list['second_stone_sn1']) {
                $list['second_stone_sn1'] = substr($list['second_stone_sn1'], 0, 3) . "...";
            }
            //材质
            $material_type = empty($list['material_type']) ? 0 : $list['material_type'];
            $list['material_type'] = Yii::$app->attr->valueName($material_type);
            //手寸
            $finger = empty($list['finger']) ? $list['finger_hk'] : $list['finger'];
            $finger = $finger ?? 0;
            $list['finger'] = Yii::$app->attr->valueName($finger);
            //汇总
            $total['goods_num'] = bcadd($total['goods_num'], $list['goods_num']);//数量
            $total['suttle_weight'] = bcadd($total['suttle_weight'], $list['suttle_weight'], 3);//连石重
            $total['gold_weight'] = bcadd($total['gold_weight'], $list['gold_weight'], 3);//金重
            $total['lncl_loss_weight'] = bcadd($total['lncl_loss_weight'], $list['lncl_loss_weight'], 3);//含耗重
            $total['gold_amount'] = bcadd($total['gold_amount'], $list['gold_amount'], 3);//金料额
            $total['factory_gold_weight'] = bcadd($total['factory_gold_weight'], $list['factory_gold_weight'], 3);//工厂总金重

            $total['main_stone_num'] = bcadd($total['main_stone_num'], $list['main_stone_num']);//主石粒数
            $total['main_stone_weight'] = bcadd($total['main_stone_weight'], $list['main_stone_weight'], 3);//主石重
            $total['main_stone_amount'] = bcadd($total['main_stone_amount'], $list['main_stone_amount'], 3);//主石成本价

            $total['second_stone_num1'] = bcadd($total['second_stone_num1'], $list['second_stone_num1']);//副石1粒数
            $total['second_stone_weight1'] = bcadd($total['second_stone_weight1'], $list['second_stone_weight1'], 3);//副石1重
            $total['second_stone_amount1'] = bcadd($total['second_stone_amount1'], $list['second_stone_amount1'], 3);//副石1成本价

            $total['parts_gold_weight'] = bcadd($total['parts_gold_weight'], $list['parts_gold_weight'], 3);//配件金重
            $total['parts_amount'] = bcadd($total['parts_amount'], $list['parts_amount'], 3);//配件额
            $total['parts_fee'] = bcadd($total['parts_fee'], $list['parts_fee'], 3);//配件工费
            $total['basic_gong_fee'] = bcadd($total['basic_gong_fee'], $list['basic_gong_fee'], 3);//基本工费
            $total['xianqian_fee'] = bcadd($total['xianqian_fee'], $list['xianqian_fee'], 3);//镶石费
            $total['biaomiangongyi_fee'] = bcadd($total['biaomiangongyi_fee'], $list['biaomiangongyi_fee'], 3);//表面工艺费
            $total['fense_fee'] = bcadd($total['fense_fee'], $list['fense_fee'], 3);//分件分色费
            $total['bukou_fee'] = bcadd($total['bukou_fee'], $list['bukou_fee'], 3);//补口费
            $total['templet_fee'] = bcadd($total['templet_fee'], $list['templet_fee'], 3);//版费

            $total['tax_amount'] = bcadd($total['tax_amount'], $list['tax_amount'], 3);//税额
            $total['pure_gold'] = bcadd($total['pure_gold'], $list['pure_gold'], 3);//折足
            $total['factory_cost'] = bcadd($total['factory_cost'], bcdiv($list['factory_cost'], $list['goods_num'], 3), 3);//单件工厂工费
            $total['one_cost_price'] = bcadd($total['one_cost_price'], bcdiv($list['cost_price'], $list['goods_num'], 3), 3);//成本价/件
            $total['cost_price'] = bcadd($total['cost_price'], $list['cost_price'], 3);//总成本价
        }
        if ($channel) {
            $total['channel'] = implode(",", array_unique($channel));//渠道
        }
        return [$lists, $total];
    }

}
