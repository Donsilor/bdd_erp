<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\Warehouse\common\forms\MoissaniteForm;
use addons\Style\common\models\StoneStyle;
use addons\Style\common\enums\AttrIdEnum;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use common\helpers\ResultHelper;

/**
 * 莫桑石列表
 *
 * Class MoissaniteController
 * @package addons\Warehouse\backend\controllers
 */
class MoissaniteController extends BaseController
{
    use Curd;

    /**
     * @var MoissaniteForm
     */
    public $modelClass = MoissaniteForm::class;
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
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',MoissaniteForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',MoissaniteForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',MoissaniteForm::tableName().'.status',-1]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(MoissaniteForm::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Ajax 编辑/创建
     * @throws
     * @return mixed
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new MoissaniteForm();
        if($model->isNewRecord){
            $model->type = AttrIdEnum::STONE_TYPE_MO;
            $model->creator_id = \Yii::$app->user->identity->getId();
        }
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $stone_type = \Yii::$app->attr->valueName($model->type)??"";
                $stone_shape = \Yii::$app->attr->valueName($model->shape)??"";
                $model->name = $stone_type.$stone_shape.$model->size;
                $model->est_cost = bcmul($model->real_carat, $model->karat_price, 2);
                $model->nominal_price = bcadd($model->est_cost, bcmul($model->est_cost, 0.05, 2), 2);
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
            }catch (\Exception $e){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 查询款号信息
     * @return array
     */
    public function actionAjaxGetStyle()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $style_sn = \Yii::$app->request->get('style_sn');
        $model = StoneStyle::find()->select(['stone_shape'])->where(['style_sn'=>$style_sn])->asArray()->one();
        return ResultHelper::json(200,'查询成功', $model);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '莫桑石信息';
        list($list,) = $this->getData($ids);
        $header = [
            ['名称', 'name' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['类型', 'type' , 'text'],
            ['形状', 'shape' , 'text'],
            ['尺寸(mm)', 'size' , 'text'],
            ['尺寸参考石重(ct)', 'ref_carat' , 'text'],
            ['实际石重(ct)', 'real_carat' , 'text', ],
            ['克拉数量', 'karat_num' , 'text'],
            ['克拉成本', 'karat_price' , 'text'],
            ['预估成本/ct', 'est_cost' , 'text'],
            ['颜色范围(D-Z)', 'color_scope' , 'text'],
            ['净度范围(FL-SI2)', 'clarity_scope' , 'text'],
            ['备注', 'remark' , 'text'],
        ];
        return ExcelHelper::exportData($list, $header, $name.'导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $lists = MoissaniteForm::find()->asArray()->all();
        foreach ($lists as &$list){
            if($list['type']){
                $list['type'] = \Yii::$app->attr->valueName($list['type'])??"";
            }
            if($list['shape']){
                $list['shape'] = \Yii::$app->attr->valueName($list['shape'])??"";
            }
        }
        return [$lists,[]];
    }
}