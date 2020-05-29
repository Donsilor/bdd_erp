<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseDefective;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseDefectiveGoodsForm;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Purchase\common\enums\ReceiptGoodsAttrEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use yii\base\Exception;

/**
 * PurchaseDefectiveGoods
 *
 * Class PurchaseDefectiveGoodsController
 * @property PurchaseDefectiveGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseDefectiveGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseDefectiveGoodsForm
     */
    public $modelClass = PurchaseDefectiveGoodsForm::class;
    
    
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-defective-goods/index']));
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
        $dataProvider->query->andWhere(['>','status',-1]);

        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'defective' => $defective,
            'tabList' => \Yii::$app->purchaseService->purchaseDefective->menuTabList($defective_id,$returnUrl),
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

        $xuhaos = Yii::$app->request->get('xuhao');
        $model = new PurchaseDefectiveGoods();
        $model->xuhao = $xuhaos;

        $defective = PurchaseDefective::find()->where(['id' => $defective_id])->one();

        $defective_goods_list = Yii::$app->request->post('defective_goods_list');
        $receiptModel = new PurchaseDefective();
        $model = new PurchaseDefectiveGoods();

        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);

        $defectiveGoods = [];
        if(Yii::$app->request->get('search') == 1 && !empty($xuhaos)){
            $xuhao_arr = $model->getXuhaos($xuhaos);
            $receiptInfo = $receiptModel::find()->where(['id'=>$defective_id])->asArray()->one();
            $receipt_no = $receiptInfo['receipt_no'];
            try {
                $trans = Yii::$app->db->beginTransaction();
                foreach ($xuhao_arr as $receipt_goods_id) {
                    $receipt_info = PurchaseReceipt::find()->where(['receipt_no' => $receipt_no])->one();
                    if(empty($receipt_info)){
                        throw new Exception("采购收货单【{$receipt_no}】不存在");
                    }
                    $receipt_goods = PurchaseReceiptGoods::find()->where(['id' => $receipt_goods_id, 'receipt_id' => $receipt_info['id']])->one();

                    if(empty($receipt_goods)){
                        throw new Exception("采购收货单【{$receipt_no}】中序号【{$receipt_goods_id}】不存在");
                    }
                    $defective_list = [];
                    $defective_list['id'] = null;
                    $defective_list['defective_id'] = $defective_id;
                    $defective_list['receipt_goods_id'] = $receipt_goods_id;
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
                        foreach ($defective_goods_list as $goods) {
                            $defective_val[] = array_values($goods);
                        }
                        $res= \Yii::$app->db->createCommand()->batchInsert(PurchaseDefectiveGoods::tableName(), $defective_key, $defective_val)->execute();
                        if(false === $res){
                            throw new Exception("保存失败");
                        }
                        //更新不良返厂单汇总：总金额和总数量
                        $res = Yii::$app->purchaseService->purchaseDefective->purchaseDefectiveSummary($defective_id);
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-defective-goods/index']));
        $this->pageSize = 1000;
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
        $dataProvider->query->andWhere(['>','status',-1]);

        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'defective' => $defective,
            'tabList' => \Yii::$app->purchaseService->purchaseDefective->menuTabList($defective_id,$returnUrl,$tab),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }
}
