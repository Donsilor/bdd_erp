<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\models\GoldStyle;
use addons\Style\common\models\StoneStyle;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseStoneBillRkGoodsForm;
use addons\Warehouse\common\forms\WarehouseStoneImportRkForm;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseStoneBill;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use yii\web\UploadedFile;

/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class StoneBillRkGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseStoneBillRkGoodsForm::class;
    public $billType = StoneBillTypeEnum::STONE_RK;

    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t-goods/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['gold_name', 'remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_ASC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>', WarehouseStoneBillRkGoodsForm::tableName() . '.status', -1]);
        $bill = WarehouseStoneBill::find()->where(['id' => $bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList' => \Yii::$app->warehouseService->stoneBill->menuTabList($bill_id, $this->billType, $returnUrl),
            'tab' => $tab,
        ]);
    }

    /**
     *
     * ajax添加商品
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $bill = WarehouseStoneBill::find()->where(['id'=>$bill_id])->one();
        $model = $model ?? new WarehouseStoneBillRkGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->bill_id = $bill_id;
                    $model->bill_no = $bill->bill_no;
                    $model->bill_type = $this->billType;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                \Yii::$app->warehouseService->stoneBill->stoneBillSummary($bill_id);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['index', 'bill_id' => $bill_id]);
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * ajax查看图片
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxImage()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneBillRkGoodsForm();
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * ajax批量
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxUpload()
    {
        $id = \Yii::$app->request->get('id');
        $bill_id = \Yii::$app->request->get('bill_id');
        $bill = WarehouseStoneBill::findOne($bill_id);
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneBillRkGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if (Yii::$app->request->isPost) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $gModel = new WarehouseStoneImportRkForm();
                $gModel->bill_id = $bill->id;
                $gModel->bill_no = $bill->bill_no;
                $gModel->bill_type = $bill->bill_type;
                $gModel->file = UploadedFile::getInstance($model, 'file');
                if (!empty($gModel->file) && isset($gModel->file)) {
                    \Yii::$app->warehouseService->stoneRk->importStoneRk($gModel);
                }
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['index', 'bill_id' => $bill_id]);
            } catch (\Exception $e) {
                $trans->rollBack();
                //var_dump($e->getTraceAsString());die;
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'bill' => $bill,
        ]);
    }




    /**
     *
     * ajax批量填充
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionBatchEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->post('ids');
        $ids = $ids ?? Yii::$app->request->get('ids');
        $model = new WarehouseStoneBillRkGoodsForm();
        $model->ids = $ids;
        $id_arr = $model->getIds();
        if (!$id_arr) {
            return ResultHelper::json(422, "ID不能为空");
        }
        $name = Yii::$app->request->post('name');
        $name = $name ?? Yii::$app->request->get('name');
        if (!$name) {
            return ResultHelper::json(422, "字段错误");
        }
        if (Yii::$app->request->isPost) {
            $value = Yii::$app->request->post('value');
            if (!$value) {
                return ResultHelper::json(422, "输入值不能为空");
            }
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $id_arr = array_unique($id_arr);
                foreach ($id_arr as $id) {
                    $goods = WarehouseStoneBillRkGoodsForm::findOne(['id' => $id]);
                    $goods->$name = $value;
                    if (false === $goods->validate()) {
                        throw new \Exception($this->getError($goods));
                    }

                    if (false === $goods->save()) {
                        throw new \Exception($this->getError($goods));
                    }
                }
                \Yii::$app->warehouseService->stoneBill->stoneBillSummary($model->bill_id);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');//['url'=>Url::to(['edit-all', 'bill_id' => $model->bill_id])."#suttle_weight"]
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $attr_id = Yii::$app->request->get('attr_id', 0);
        if (!$attr_id) {
            return ResultHelper::json(422, '参数错误');
        }
        $style_arr = $model::find()->where(['id' => $id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if (count($style_arr) != 1) {
            return ResultHelper::json(422, '请选择同款的商品进行操作');
        }
        $check = Yii::$app->request->get('check', null);
        if ($check) {
            return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'ids' => $ids, 'name' => $name, 'attr_id' => $attr_id])]);
        }
        $style_sn = $style_arr[0]['style_sn'] ?? "";
        $attr_arr = $model->getAttrValueListByStyle($style_sn, $attr_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'ids' => $ids,
            'name' => $name,
            'attr_arr' => $attr_arr
        ]);

    }

    /**
     *
     * 收货单-批量编辑
     * @return mixed
     * @throws
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab', 3);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t-goods/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name', 'stone_remark', 'biaomiangongyi', 'remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        //$dataProvider->query->andWhere(['>',WarehouseBillGoodsT::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id' => $bill_id])->one();
        if ($bill->bill_status != BillStatusEnum::SAVE) {
            exit("单据不是保存状态");
        }
        $model = new WarehouseStoneBillRkGoodsForm();
        $total = $model->goodsSummary($bill_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'total' => $total,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'tab' => $tab,
        ]);
    }

    /**
     *
     * 删除
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            if (false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            if (false === $res) {
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 批量删除
     * @return mixed
     */
    public function actionBatchDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return $this->message("ID不能为空", $this->redirect(['index']), 'error');
        }
        foreach ($ids as $id) {
            if (!($model = $this->modelClass::findOne($id))) {
                return $this->message("找不到数据", $this->redirect(['index']), 'error');
            }
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            WarehouseStoneBillRkGoodsForm::deleteAll(['id' => $ids]);
            \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }



    /**
     * 查询石料款号信息
     * @return array
     */
    public function actionAjaxGetStone()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $style_sn = \Yii::$app->request->get('style_sn');
        $model = StoneStyle::find()->select(['stone_type','stone_name','cert_type','stone_shape'])->where(['style_sn'=>$style_sn])->one();
        $data = [
            'stone_type' => $model->stone_type??"",
            'stone_name' => $model->stone_name??"",
            'cert_type' => $model->cert_type??"",
            'stone_shape' => $model->stone_shape??"",
        ];
        return ResultHelper::json(200,'查询成功', $data);
    }


    public function actionGetGoodsSn(){
        $stone_type = Yii::$app->request->post('stone_type');
        $model = Yii::$app->styleService->stone::getDropDown($stone_type);
        return ResultHelper::json(200, 'ok',$model);
    }

}
