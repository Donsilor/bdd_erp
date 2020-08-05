<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseDefective;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseDefectiveGoodsForm;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use yii\base\Exception;

/**
 * PurchaseDefectiveGoods
 *
 * Class PurchaseDefectiveGoodsController
 * @property PurchaseDefectiveGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class DefectiveGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseDefectiveGoodsForm
     */
    public $modelClass = PurchaseDefectiveGoodsForm::class;
    public $purchaseType = PurchaseTypeEnum::GOODS;
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $defective_id = Yii::$app->request->get('defective_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['defective-goods/index']));
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                     'recGoods' => ['style_sn']
                ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=','defective_id',$defective_id]);
        $dataProvider->query->andWhere(['>',PurchaseDefectiveGoods::tableName().'.status',-1]);

        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'defective' => $defective,
            'tabList' => \Yii::$app->purchaseService->defective->menuTabList($defective_id, $this->purchaseType, $returnUrl),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }

    /**
     * 编辑/创建
     * @property PurchaseReceiptGoodsForm $model
     * @return mixed
     */
    public function actionAdd()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $defective_id = Yii::$app->request->get('defective_id');

        $xuhaos = Yii::$app->request->get('xuhaos');
        $model = new PurchaseDefectiveGoods();
        $form = new PurchaseDefectiveGoodsForm();
        $model->xuhao = $xuhaos;

        $defective = PurchaseDefective::find()->where(['id' => $defective_id])->one();

        $defective_goods_list = Yii::$app->request->post('defective_goods_list');

        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        $defectiveGoods = [];
        if(Yii::$app->request->get('search') == 1 && !empty($xuhaos)){
            $form->xuhaos = $xuhaos;
            $xuhao_arr = $form->getXuhaos();
            $receipt_no = $defective->receipt_no;
            try {
                $trans = Yii::$app->db->beginTransaction();
                foreach ($xuhao_arr as $xuhao) {

                    $check = PurchaseDefectiveGoods::find()->where(['defective_id' => $defective_id, 'xuhao' => $xuhao])->one();
                    if($check){
                        throw new Exception("序号【{$xuhao}】已存在，不能重复添加");
                    }

                    $receipt_info = PurchaseReceipt::find()->where(['receipt_no' => $receipt_no])->one();
                    if(empty($receipt_info)){
                        throw new Exception("采购收货单【{$receipt_no}】不存在");
                    }

                    $receipt_goods = PurchaseReceiptGoods::find()->where(['receipt_id' => $receipt_info['id'], 'xuhao' => $xuhao])->one();
                    if(empty($receipt_goods)){
                        throw new Exception("序号【{$xuhao}】不在采购收货单【{$receipt_no}】中");
                    }

                    $defective_list = [];
                    $defective_list['id'] = null;
                    $defective_list['defective_id'] = $defective_id;
                    $defective_list['xuhao'] = $xuhao;
                    $defective_list['produce_sn'] = $receipt_goods['produce_sn'];
                    $defective_list['style_sn'] = $receipt_goods['style_sn'];
                    $defective_list['factory_mo'] = $receipt_goods['factory_mo'];
                    $defective_list['style_cate_id'] = $receipt_goods['style_cate_id'];
                    $defective_list['product_type_id'] = $receipt_goods['product_type_id'];
                    $defective_list['cost_price'] = $receipt_goods['cost_price'];
                    $defective_list['oqc_reason'] = '';
                    $defective_list['goods_remark'] = '';
                    $defectiveGoods[] = $defective_list;
                    if(!empty($defective_goods_list)){
                        $defective_val = [];
                        $defective_key = array_keys($defective_goods_list[0]);
                        array_push($defective_key, 'id', 'defective_id');
                        foreach ($defective_goods_list as $goods) {
                            array_push($goods, null, $defective_id);
                            $defective_val[] = array_values($goods);
                        }
                        $res= \Yii::$app->db->createCommand()->batchInsert(PurchaseDefectiveGoods::tableName(), $defective_key, $defective_val)->execute();
                        if(false === $res){
                            throw new Exception("保存失败");
                        }
                        //更新不良返厂单汇总：总金额和总数量
                        $res = Yii::$app->purchaseService->defective->purchaseDefectiveSummary($defective_id);
                        if(false === $res){
                            throw new Exception('更新不良返厂单汇总失败');
                        }
                        $trans->commit();
                        Yii::$app->getSession()->setFlash('success', '保存成功');
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect($skiUrl), 'error');
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'defectiveGoods' => $defectiveGoods
        ]);
    }

    /**
     * 编辑明细
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEditAll()
    {
        $defective_id = Yii::$app->request->get('defective_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['defective-goods/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=','defective_id',$defective_id]);
        $dataProvider->query->andWhere(['>',PurchaseDefectiveGoods::tableName().'.status',-1]);

        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'defective' => $defective,
            'tabList' => \Yii::$app->purchaseService->defective->menuTabList($defective_id, $this->purchaseType, $returnUrl, $tab),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }

    /**
     * ajax 不良返厂单-删除明细
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new PurchaseDefectiveGoods();
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();
            if(false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("操作成功", $this->redirect(Yii::$app->request->referrer), 'success');
    }
}
