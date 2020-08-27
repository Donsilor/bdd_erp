<?php

namespace backend\modules\base\controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use backend\controllers\BaseController;
use common\enums\WorksTypeEnum;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\models\backend\Member;
use common\models\backend\MemberWorks;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;


/**
 *
 * 工作总结
 * Class OrderPayController
 * @package backend\modules\goods\controllers
 */
class MemberWorksController extends BaseController
{
    use Curd;

    /**
     * @var BankPay
     */
    public $modelClass = MemberWorks::class;
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
            'pageSize' => $this->getPageSize(2),
            'relations' => [
                'member' => ['username'],
                'department' => ['name']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['date']);
        $date = $searchModel->date;
        if (!empty($date)) {
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }else{
            $date = $searchModel->date = date('Y-m-01',time())."/".date('Y-m-d',time());
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            if (empty($date)) {
                return $this->message('导出必须选择日期，且选择时间必须是31日之内', $this->redirect(['index']), 'warning');
            }
            $day = (strtotime(explode('/', $date)[1])-strtotime(explode('/', $date)[0]))/3600/24;
            if($day > 31){
                return $this->message('选择时间必须是31日之内', $this->redirect(['index']), 'warning');
            }
            $queryIds = $dataProvider->query->select(MemberWorks::tableName().'.id');
            $this->actionExport($queryIds);
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        $this->layout = '@backend/views/layouts/iframe';
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new MemberWorks();

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->creator_id = Yii::$app->user->identity->getId();
                $model->dept_id = $model->member->dept_id;
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message('操作成功', $this->redirect($returnUrl), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }
        if($model->isNewRecord){
            $model->title = date('Y年m月d日').'工作日报';
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $creator_id = Yii::$app->request->get('creator_id');
        $model = Member::find()->where(['id'=>$creator_id])->one();
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'date'=>SORT_DESC,
                'id' => SORT_DESC
            ],
            'pageSize' => $this->getPageSize(2),
            'relations' => [
                'member' => ['username'],
                'department' => ['name']
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([MemberWorks::tableName().'.creator_id'=>$creator_id ,MemberWorks::tableName().'.type'=>WorksTypeEnum::DAY_SUMMARY]);
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider'=>$dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /***
     * 导出Excel
     */
    public function actionExport($ids=null){
        if(!is_object($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,$date_list) = $this->getData($ids);
        // [名称, 字段名, 类型, 类型规则]
        $header = [
            ['部门', 'dept', 'text'],
            ['岗位', 'post', 'text'],
            ['姓名', 'username', 'text'],
        ];
        foreach ($date_list as $date){
            $date_txt = date('m月d日',strtotime($date['date']));
            $header[] = [$date_txt, $date['date'],'text'];
        }


        // 初始化
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // 写入头部
        $hk = 1;
        foreach ($header as $k => $v) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($hk) . '1', $v[0]);
            $sheet->getStyle(Coordinate::stringFromColumnIndex($hk) . '1')->getFont()->setBold(true);
            $sheet->getDefaultColumnDimension()->setWidth(45); //设置默认列宽为12
            $sheet->getColumnDimension('A')->setWidth(15); //设置默认列宽为12
            $sheet->getColumnDimension('B')->setWidth(15); //设置默认列宽为12
            $sheet->getColumnDimension('C')->setWidth(15); //设置默认列宽为12
            $sheet->getDefaultRowDimension()->setRowHeight(-1); //设置行高自动
            $sheet->getStyle(Coordinate::stringFromColumnIndex($hk) . '1')->getAlignment()->setWrapText(true);
//            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($hk))->setAutoSize(true); //自动计算列宽
            $hk += 1;
        }
        return ExcelHelper::exportData($list, $header, '工作日报' . date('YmdHis',time()),'xlsx', [$spreadsheet,$sheet]);

    }

    /**
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getData($ids)
    {
        $where = ['id' => $ids, 'type'=> WorksTypeEnum::DAY_SUMMARY];
        $date_list = MemberWorks::find()->where($where)->groupBy('date')->select(['date'])->asArray()->all();
        $creator_id_list = MemberWorks::find()->where($where)->groupBy('creator_id')->select(['creator_id'])->asArray()->all();
        $lists = [];
        foreach ($creator_id_list as $creator_id){
            $list = [];
            $member = Member::find()->where(['id'=>$creator_id])->one();
            $list['username'] = $member->username;
            $list['dept'] = $member->department->name ?? '';
            $list['post'] = $member->authRole->title ?? '';
            $member_works_list = MemberWorks::find() ->where($where)->andWhere(['creator_id'=>$creator_id])->select(['date','content'])->asArray()->all();
            $member_works_list = array_column($member_works_list,'content','date');
            foreach ($date_list as $date){
                $list[$date['date']] = $member_works_list[$date['date']] ?? '';
            }
            $lists[] = $list;
        }

        return [$lists, $date_list];

    }



    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionWorks()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $creator_id = Yii::$app->user->identity->getId();
        $model = Member::find()->where(['id'=>$creator_id])->one();
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'date'=>SORT_DESC,
                'id' => SORT_DESC
            ],
            'pageSize' => $this->getPageSize(2),
            'relations' => [
                'member' => ['username'],
                'department' => ['name']
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([MemberWorks::tableName().'.creator_id'=>$creator_id ,MemberWorks::tableName().'.type'=>WorksTypeEnum::DAY_SUMMARY]);
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider'=>$dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionWorksView()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }





}
