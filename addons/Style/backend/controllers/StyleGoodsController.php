<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Style\common\models\StyleGoods;
use addons\Style\common\forms\StyleGoodsForm;
use addons\Style\common\enums\AttrTypeEnum;
use common\helpers\Url;
use addons\Style\common\models\Style;
/**
* Goods
*
* Class GoodsController
* @package backend\modules\goods\controllers
*/
class StyleGoodsController extends BaseController
{
    use Curd;

    /**
    * @var StyleGoods
    */
    public $modelClass = StyleGoods::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name','style.style_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 'style'=>['style_name','style_image','style_cate_id','product_type_id']
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>',StyleGoods::tableName().'.status',-1]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    /**
     * 编辑-款式商品
     *
     * @return mixed
     */
    public function actionEditAll()
    {
        
        $style_id = Yii::$app->request->get('style_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $this->modelClass = Style::class;
        $style = $this->findModel($style_id);
        
        $model = new StyleGoodsForm();
        $model->style_id = $style->id;
        $model->style_cate_id = $style->style_cate_id;
        $model->style_sn = $style->style_sn;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $goods_list = $model->getPostGoods();
                $attr_list  = $model->getPostAttrs();
                //更新商品属性
                \Yii::$app->styleService->styleAttribute->createStyleAttribute($style_id, $attr_list,AttrTypeEnum::TYPE_SALE);
                //更新款式商品
                \Yii::$app->styleService->styleGoods->createStyleGoods($style_id, $goods_list);
                
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'style_id'=>$style_id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect([$this->action->id,'style_id'=>$style_id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'success');
        }
        $model->initGoods();
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($style_id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }  
}
