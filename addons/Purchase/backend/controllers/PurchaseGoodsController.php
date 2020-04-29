<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseGoods;
use common\helpers\ResultHelper;
use addons\Style\common\models\Style;
use addons\Purchase\common\forms\PurchaseGoodsForm;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use common\enums\StatusEnum;
/**
 * Attribute
 *
 * Class AttributeController
 * @property PurchaseGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseGoodsForm
     */
    public $modelClass = PurchaseGoodsForm::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $purchase_id = Yii::$app->request->get('purchase_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase/index']));
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['goods_name'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                     
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','purchase_id',$purchase_id]);
        $dataProvider->query->andWhere(['>','status',-1]);
        
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase'=> $purchase,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->purchaseService->purchase->menuTabList($purchase_id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }
    /**
     * 编辑/创建
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);        
        $model = $model ?? new PurchaseGoodsForm();

        if($model->isNewRecord && ($return = $this->checkGoods($model)) !== true) {   
            return $return;
        } 

        if ($model->load(Yii::$app->request->post())) {  
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{                
                $trans = Yii::$app->trans->beginTransaction();  
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }     
                //创建属性关系表数据
                $model->createAttrs();
                //更新采购汇总：总金额和总数量
                Yii::$app->purchaseService->purchase->purchaseSummary($model->purchase_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        
        $model->initAttrs();
        return $this->render($this->action->id, [
                'model' => $model,
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
        $style_sn = Yii::$app->request->get('style_sn');
        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        
        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }
        if($model->isNewRecord) {
            $model->purchase_id = $purchase_id;
        }
        if($model->isNewRecord && $search && $style_sn) {
            
            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$style_sn])->one();
            if(!$style) {
                $qiban = Qiban::find()->where(['qiban_sn'=>$style_sn])->one();
                if(!$qiban) {
                    return $this->message("[款号/起版号]不存在", $this->redirect($skiUrl), 'error');
                }elseif($qiban->status != StatusEnum::ENABLED) {
                    return $this->message("起版号不可用", $this->redirect($skiUrl), 'error');
                }else{
                    $exist = PurchaseGoods::find()->where(['purchase_id'=>$model->purchase_id,'style_sn'=>$style_sn,'status'=>StatusEnum::ENABLED])->count();
                    if($exist) {
                        return $this->message("起版号已添加过", $this->redirect($skiUrl), 'error');
                    }                    
                    $model->style_id = $qiban->id;
                    $model->style_sn = $style_sn;
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
                $model->style_sn = $style_sn;
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
