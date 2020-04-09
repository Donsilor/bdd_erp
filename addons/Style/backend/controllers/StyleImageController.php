<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\enums\FaceWorkEnum;
use addons\Style\common\enums\ImagePositionEnum;
use addons\Style\common\enums\ImageTypeEnum;
use addons\Style\common\models\StyleImages;
use common\helpers\ResultHelper;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleImageController extends BaseController
{
    use Curd;
    public $modelClass = StyleImages::class;
    public $noAuthOptional = ['get-position'];
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $style_id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl');
        $tab = Yii::$app->request->get('tab');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['factory.factory_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
                'style' => ['style_sn'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',StyleImages::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',StyleImages::tableName().'.style_id',$style_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab'=>$tab,
            'style_id' => $style_id,
            'tabList'=>\Yii::$app->styleService->style->editTabList($style_id,$returnUrl),
        ]);
    }




    public function actionGetPosition(){
        $type = Yii::$app->request->post('type');
        $position = ImageTypeEnum::getPosition($type);
        return ResultHelper::json(200, 'ok',$position);

    }


}
