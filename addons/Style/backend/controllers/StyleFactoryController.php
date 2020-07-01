<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\models\Style;
use addons\Style\common\models\StyleFactory;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleFactoryController extends BaseController
{
    use Curd;
    public $modelClass = StyleFactory::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $style_id = Yii::$app->request->get('style_id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        $style = Style::find()->where(['id'=>$style_id])->one();

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['supplier.supplier_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
                'style' => ['style_sn'],
                'supplier' => ['supplier_name']
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['shipping_time']);

        $dataProvider->query->andWhere(['>',StyleFactory::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',StyleFactory::tableName().'.style_id',$style_id]);

        $shipping_time = $searchModel->shipping_time;
        if (!empty($shipping_time)) {
            $dataProvider->query->andFilterWhere(['>=',StyleFactory::tableName().'.shipping_time', strtotime(explode('/', $shipping_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',StyleFactory::tableName().'.shipping_time', (strtotime(explode('/', $shipping_time)[1]) + 86400)] );//结束时间
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab'=>$tab,
            'style_id' => $style_id,
            'tabList'=>\Yii::$app->styleService->style->menuTabList($style_id,$returnUrl),
            'style' => $style,
        ]);
    }


}
