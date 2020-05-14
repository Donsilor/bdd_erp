<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Purchase\common\forms\PurchaseGoodsForm;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\Style;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseReceipt;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
use yii\base\Exception;

/**
 * PurchaseReceiptGoods
 *
 * Class PurchaseReceiptGoodsController
 * @property PurchaseReceiptGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseReceiptGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseReceiptGoodsForm
     */
    public $modelClass = PurchaseReceiptGoodsForm::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $receipt_id = Yii::$app->request->get('receipt_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-receipt/index']));
        
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
        $dataProvider->query->andWhere(['=','receipt_id',$receipt_id]);
        //$dataProvider->query->andWhere(['>','status',-1]);
        $receipt_goods = $dataProvider->getModels();
        $receiptInfo = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'receiptInfo' => $receiptInfo,
                'receiptGoods' => $receipt_goods,
                'tabList' => \Yii::$app->purchaseService->purchaseReceipt->menuTabList($receipt_id,$returnUrl),
                'returnUrl' => $returnUrl,
                'tab'=>$tab,
        ]);
    }

    /**
     * 编辑/创建
     * @property PurchaseReceiptGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $receipt_id = Yii::$app->request->get('receipt_id');
        $produce_sns = Yii::$app->request->get('produce_sns');
        $search = Yii::$app->request->get('search');

        $model = new PurchaseReceiptGoods();
        $this->modelClass = PurchaseReceiptGoodsForm::class;
        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        if($search == 1 && !empty($produce_sns)){
            $produce_sns = str_replace(' ',',',$produce_sns);
            $produce_sns = str_replace('，',',',$produce_sns);
            $produce_sns = str_replace(array("\r\n", "\r", "\n"),',',$produce_sns);
            $produce_arr = explode(",", $produce_sns);
            try {
                $trans = Yii::$app->db->beginTransaction();
                foreach ($produce_arr as $produce_sn) {
                    $produce_info = Produce::find()->where(['produce_sn' => $produce_sn])->one();
                    $produce_id = $produce_info['id'];
                    $shippent_num = ProduceShipment::find()->where(['produce_id' => $produce_id])->sum('shippent_num');
                    $purchase_receipt_info = PurchaseReceiptGoods::find()->joinWith(['receipt'])
                        ->select('supplier_id')
                        ->where(['produce_sn' => $produce_sn])
                        ->andWhere(['<=', 'audit_status', AuditStatusEnum::PASS])
                        ->asArray()
                        ->all();
                    $receipt_num = count($purchase_receipt_info);
                    $the_receipt_num = $shippent_num - $receipt_num;

                    $produce_attr = ProduceAttribute::find()->where(['produce_id'=> $produce_id])->asArray()->all();
                    foreach ($produce_attr as $attr) {
                        $produce_attr_arr = Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id']);
                    }
                    if ($the_receipt_num) {
                        for ($i = 0; $i <= $the_receipt_num; $i++) {

                        }
                    } else {
                        throw new Exception("布产单{$produce_sn}没有可出货数量");
                    }
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect($skiUrl), 'error');
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
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
        $receipt_goods_list = Yii::$app->request->post('receipt_goods_list');
        $rurchase_receipt_info = Yii::$app->request->post('PurchaseReceipt');
        $model = new PurchaseReceiptGoods();
        if(!empty($receipt_goods_list)){
            try {
                $trans = Yii::$app->db->beginTransaction();
                $receipt_id = $rurchase_receipt_info['id'];
                foreach ($receipt_goods_list as $key => $goods) {
                    $id = isset($goods['id']) ? $goods['id'] : '';
                    $model = $this->findModel($id);
                    // ajax 校验
                    $this->activeFormValidate($model);
                    //if(!empty($id)){
                        if (false === $model::updateAll($goods, ['id' => $id])) {
                            throw new Exception($this->getError($model));
                        }
                    //}else{
                    //    if(false === $model->save()){
                    //        throw new Exception($this->getError($model));
                    //    }
                    //}
                }
                //更新采购收货单汇总：总金额和总数量
                //$res = Yii::$app->purchaseService->purchaseReceipt->purchaseReceiptSummary($receipt_id);
                //if(false === $res){
                //    throw new Exception('更新收货单汇总失败！');
                //}
                $trans->commit();
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax('index', [
            'model' => $model
        ]);
    }

    /**
     * 查询商品
     * @param unknown $model
     * @param unknown $style_sn
     * @return mixed|string
     */
    private function checkGoods(& $model)
    {

        $purchase_id = Yii::$app->request->get('purchase_id');
        $goods_sn = Yii::$app->request->get('goods_sn');
        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');

        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }
        if($model->isNewRecord) {
            $model->purchase_id = $purchase_id;
        }
        if($model->isNewRecord && $search && $goods_sn) {

            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$goods_sn])->one();
            if(!$style) {
                $qiban = Qiban::find()->where(['qiban_sn'=>$goods_sn])->one();
                if(!$qiban) {
                    return $this->message("[款号/起版号]不存在", $this->redirect($skiUrl), 'error');
                }elseif($qiban->status != StatusEnum::ENABLED) {
                    return $this->message("起版号不可用", $this->redirect($skiUrl), 'error');
                }else{
                    $exist = PurchaseGoods::find()->where(['purchase_id'=>$model->purchase_id,'qiban_sn'=>$goods_sn,'status'=>StatusEnum::ENABLED])->count();
                    if($exist) {
                        return $this->message("起版号已添加过", $this->redirect($skiUrl), 'error');
                    }
                    $model->style_id = $qiban->id;
                    $model->goods_sn = $goods_sn;
                    $model->qiban_sn = $goods_sn;
                    $model->qiban_type = $qiban->qiban_type;
                    $model->style_sn = $qiban->style_sn;
                    $model->style_cate_id = $qiban->style_cate_id;
                    $model->product_type_id = $qiban->product_type_id;
                    $model->goods_type = PurchaseGoodsTypeEnum::QIBAN;
                    $model->style_sex = $qiban->style_sex;
                    $model->goods_name = $qiban->qiban_name;
                    $model->cost_price  = $qiban->cost_price;
                    $model->jintuo_type = $qiban->jintuo_type;
                    $qibanForm = new QibanAttrForm();
                    $qibanForm->id = $qiban->id;
                    $qibanForm->initAttrs();

                    $model->attr_custom = $qibanForm->attr_custom;
                    $model->attr_require = $qibanForm->attr_require;
                }
            }elseif($style->status != StatusEnum::ENABLED) {
                return $this->message("款号不可用", $this->redirect($skiUrl), 'error');
            }else{
                $model->style_id = $style->id;
                $model->goods_sn = $goods_sn;
                $model->style_sn = $goods_sn;
                $model->style_cate_id = $style->style_cate_id;
                $model->product_type_id = $style->product_type_id;
                $model->goods_type = PurchaseGoodsTypeEnum::STYLE;
                $model->style_sex = $style->style_sex;
                $model->goods_name = $style->style_name;
                $model->cost_price = $style->cost_price;
            }
        }

        return true;
    }

}
