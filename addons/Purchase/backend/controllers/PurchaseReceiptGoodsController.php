<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\forms\PurchaseReceiptForm;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseReceipt;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Purchase\common\enums\ReceiptGoodsAttrEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
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
        $dataProvider->query->andWhere(['>','status',-1]);
        $receipt = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tabList' => \Yii::$app->purchaseService->purchaseReceipt->menuTabList($receipt_id,$returnUrl),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
            'receipt' => $receipt,
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
        $receipt_goods_list = Yii::$app->request->post('receipt_goods_list');
        $model = new PurchaseReceiptForm();
        $model->produce_sns = $produce_sns;
        $receiptModel = new PurchaseReceipt();
        $receiptGoods = new PurchaseReceiptGoods();
        $this->modelClass = PurchaseReceiptGoodsForm::class;
        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        $receipt_goods = [];
        if($search == 1 && !empty($produce_sns)){
            $produce_arr = $model->getProduceSns($produce_sns);
            $receiptInfo = $receiptModel::find()->where(['id'=>$receipt_id])->asArray()->one();
            $supplier_id = $receiptInfo['supplier_id'];
            try {
                $trans = Yii::$app->db->beginTransaction();
                foreach ($produce_arr as $produce_sn) {
                    $produce_info = Produce::find()->where(['produce_sn' => $produce_sn])->one();
                    if(empty($produce_info)){
                        throw new Exception("布产单{$produce_sn}单号不对");
                    }
                    $produce_id = $produce_info['id'];
                    if($supplier_id != $produce_info['supplier_id']){
                        throw new Exception("布产单{$produce_sn}供应商不一致");
                    }
                    $shippent_num = ProduceShipment::find()->where(['produce_id' => $produce_id])->sum('shippent_num');
                    if(!$shippent_num){
                        throw new Exception("布产单{$produce_sn}未出货");
                    }
                    /*$purchase_receipt_info = PurchaseReceiptGoods::find()->joinWith(['receipt'])
                        ->select('supplier_id')
                        ->where(['produce_sn' => $produce_sn])
                        ->andWhere(['<=', 'audit_status', AuditStatusEnum::PASS])
                        ->andWhere([PurchaseReceiptGoods::tableName().'status'=>StatusEnum::ENABLED])
                        ->asArray()
                        ->all();*/
                    $receipt_num = PurchaseReceiptGoods::find()->where(['produce_sn' => $produce_sn])->count();
                    //$receipt_num = count($purchase_receipt_info);
                    $the_receipt_num = bcsub($shippent_num, $receipt_num);
                    $produce_attr = ProduceAttribute::find()->where(['produce_id'=> $produce_id])->asArray()->all();
                    $produce_attr_arr = [];
                    foreach ($produce_attr as $attr) {
                        $attr_name = Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id']);
                        $produce_attr_arr[$attr['attr_id']]['attr_name'] = $attr_name;
                        $produce_attr_arr[$attr['attr_id']]['attr_value'] = $attr['attr_value'];
                        $produce_attr_arr[$attr['attr_id']]['attr_value_id'] = $attr['attr_value_id'];
                    }
                    if ($the_receipt_num >= 1) {
                        $receipt_list = [];
                        foreach ($receiptGoods->attributeLabels() as $k => $item) {
                            $receipt_list[$k] = '';
                        }
                        for ($i = 1; $i <= $the_receipt_num; $i++) {
                            $receipt_list['id'] = null;
                            $receipt_list['receipt_id'] = $receipt_id;
                            $receipt_list['produce_sn'] = $produce_sn;
                            $receipt_list['purchase_sn'] = $produce_info['from_order_sn'];
                            $receipt_list['goods_name'] = $produce_info['goods_name'];
                            $receipt_list['goods_num'] = 1;
                            $receipt_list['style_sn'] = $produce_info['style_sn'] != "" ? $produce_info['style_sn'] : $produce_info['qiban_sn'];
                            $receipt_list['style_cate_id'] = $produce_info['style_cate_id'];
                            $receipt_list['product_type_id'] = $produce_info['product_type_id'];
                            $receipt_list['finger'] = isset($produce_attr_arr[ReceiptGoodsAttrEnum::FINGER])?$produce_attr_arr[ReceiptGoodsAttrEnum::FINGER]['attr_value']:'';
                            $receipt_list['xiangkou'] = isset($produce_attr_arr[ReceiptGoodsAttrEnum::XIANGKOU])?$produce_attr_arr[ReceiptGoodsAttrEnum::XIANGKOU]['attr_value']:'';
                            $receipt_list['material'] = isset($produce_attr_arr[ReceiptGoodsAttrEnum::MATERIAL])?$produce_attr_arr[ReceiptGoodsAttrEnum::MATERIAL]['attr_value_id']:'';
                            $receipt_list['jintuo_type'] = $produce_info['jintuo_type'];
                            $receipt_goods[] = $receipt_list;
                        }
                    } else {
                        throw new Exception("布产单{$produce_sn}没有可出货数量");
                    }
                    if(!empty($receipt_goods_list)){
                        $receipt_val = [];
                        $receipt_key = array_keys($receipt_goods_list[0]);
                        array_push($receipt_key, 'receipt_id');
                        foreach ($receipt_goods_list as $goods) {
                            array_push($goods, $receipt_id);
                            $receipt_val[] = array_values($goods);
                        }
                        $res= \Yii::$app->db->createCommand()->batchInsert(PurchaseReceiptGoods::tableName(), $receipt_key, $receipt_val)->execute();
                        if(false === $res){
                            throw new Exception("保存失败");
                        }
                        //更新采购收货单汇总：总金额和总数量
                        $res = Yii::$app->purchaseService->purchaseReceipt->purchaseReceiptSummary($receipt_id);
                        if(false === $res){
                            throw new Exception('更新收货单汇总失败');
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
            'receipt_goods' => $receipt_goods
        ]);
    }
}
