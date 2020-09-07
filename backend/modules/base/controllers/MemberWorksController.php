<?php

namespace backend\modules\base\controllers;

use common\enums\StatusEnum;
use backend\controllers\BaseController;
use common\enums\WorksTypeEnum;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\models\backend\Member;
use common\models\backend\MemberWorks;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;


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
            'partialMatchAttributes' => ['member.username'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'member' => ['username'],
                'department' => ['name']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['date']);
        $date = $searchModel->date;
        if (!empty($date)) {
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<=',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }else{
            $date = $searchModel->date = date('Y-m-01',time())."/".date('Y-m-d',time());
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<=',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
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
        list($noWorksCount,$noWorksMembers) = $this->getNoWorksMember();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'noWorksMembers'=>$noWorksMembers,
            'noWorksCount'=>$noWorksCount,

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
                if($model->isNewRecord) {
                    $model->creator_id = Yii::$app->user->identity->getId();
                    $model->dept_id = $model->member->dept_id;
                }
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
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'member' => ['username'],
                'department' => ['name']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['date']);
        $date = $searchModel->date;
        if (!empty($date)) {
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<=',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }else{
            $date = $searchModel->date = date('Y-m-01',time())."/".date('Y-m-d',time());
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<=',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }
        $dataProvider->query->andWhere([MemberWorks::tableName().'.creator_id'=>$creator_id]);
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
        list($lists,$date_list) = $this->getData($ids);
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

        return self::exportData($lists, $header, '工作日报' . date('YmdHis',time()),'xlsx');

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
        $date_list = MemberWorks::find()->where($where)->groupBy('date')->select(['date'])->orderBy('date desc')->asArray()->all();
        $creator_id_list = MemberWorks::find()->where($where)->groupBy('creator_id')->select(['creator_id'])->asArray()->all();
        $lists = [
        ];
        foreach ($creator_id_list as $creator_id){
            $list = [];
            $member = Member::find()->where(['id'=>$creator_id])->one();
            if(!$member) continue;
            $list['username'] = $member->username;
            $list['dept'] = $member->department->name ?? '';
            $list['post'] = $member->assignment->role->title ?? '';
            $member_works_list = MemberWorks::find() ->where($where)->andWhere(['creator_id'=>$creator_id])->select(['date','content'])->asArray()->all();
            $member_works_list = array_column($member_works_list,'content','date');
            foreach ($date_list as $date){
                $list[$date['date']] = $member_works_list[$date['date']] ?? '';
            }
            if($member->dept_id == 34){
                $lists['平洲直播'][] = $list;
            }elseif ($member->dept_id == 35){
                $lists['平洲微信'][] = $list;
            }elseif ($member->dept_id == 36){
                $lists['四会直播1组'][] = $list;
            }elseif ($member->dept_id == 37){
                $lists['四会直播2组'][] = $list;
            }else{
                $lists['深圳办公室+香港办公室'][] = $list;
            }

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
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'member' => ['username'],
                'department' => ['name']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['date']);
        $date = $searchModel->date;
        if (!empty($date)) {
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<=',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }else{
            $date = $searchModel->date = date('Y-m-01',time())."/".date('Y-m-d',time());
            $dataProvider->query->andFilterWhere(['>=',MemberWorks::tableName().'.date', explode('/', $date)[0]]);//起始时间
            $dataProvider->query->andFilterWhere(['<=',MemberWorks::tableName().'.date', explode('/', $date)[1]]);//结束时间
        }
        $dataProvider->query->andWhere([MemberWorks::tableName().'.creator_id'=>$creator_id ,MemberWorks::tableName().'.type'=>WorksTypeEnum::DAY_SUMMARY]);

        if(Yii::$app->mobileDetect->isMobile()){
            $render = $this->action->id;
        }else{
            $render = 'view.php';

        }
        return $this->render($render, [
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




    /**
     * 导出Excel
     *
     * @param array $list
     * @param array $header
     * @param string $filename
     * @param string $title
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function exportData($lists = [], $header = [], $filename = '', $suffix = 'xlsx')
    {
        if (!is_array($lists) || !is_array($header)) {
            return false;
        }

        !$filename && $filename = time();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheetIndex = 1;
        foreach($lists as $title => $list){
            $sheet = $spreadsheet->createSheet($sheetIndex);
            $sheet->setTitle($title);
            $sheetIndex += 1;
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
                $hk += 1;
            }

            // 开始写入内容
            $column = 2;
            $size = ceil(count($list) / 500);
            for ($i = 0; $i < $size; $i++) {
                $buffer = array_slice($list, $i * 500, 500);

                foreach ($buffer as $k => $row) {
                    $span = 1;

                    foreach ($header as $key => $value) {
                        // 解析字段
                        $realData = ExcelHelper::formatting($header[$key], trim(ExcelHelper::formattingField($row, $value[1])), $row);
                        // 写入excel
                        $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($span) . $column, $realData, DataType::TYPE_STRING);
                        // $sheet->setCellValue(Coordinate::stringFromColumnIndex($span) . $column, $realData);
                        $span++;
                    }

                    $column++;
                    unset($buffer[$k]);
                }
            }
        }
        // 清除之前的错误输出
        ob_end_clean();
        ob_start();

        // 直接输出下载
        switch ($suffix) {
            case 'xlsx' :
                $writer = new Xlsx($spreadsheet);
                header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8;");
                header("Content-Disposition: inline;filename=\"{$filename}.xlsx\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                break;
            case 'xls' :
                $writer = new Xls($spreadsheet);
                header("Content-Type:application/vnd.ms-excel;charset=utf-8;");
                header("Content-Disposition:inline;filename=\"{$filename}.xls\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                break;
            case 'csv' :
                $writer = new Csv($spreadsheet);
                header("Content-type:text/csv;charset=utf-8;");
                header("Content-Disposition:attachment; filename={$filename}.csv");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                break;
            case 'html' :
                $writer = new Html($spreadsheet);
                header("Content-Type:text/html;charset=utf-8;");
                header("Content-Disposition:attachment;filename=\"{$filename}.{$suffix}\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                break;
        }

        /* 释放内存 */
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        ob_end_flush();

        exit();
    }

    public function getNoWorksMember(){

        //查询当天未提交日志人姓名
        $workMember = MemberWorks::find()->where(['date'=>date('Y-m-d'),'type'=>WorksTypeEnum::DAY_SUMMARY])->select(['creator_id'])->asArray()->all();
        $workMember = array_column($workMember,'creator_id');
        $workMember = array_merge($workMember,[1,23,25]);  //过滤 admin 曲洪良、张鹏飞
        $dept_ids = Member::find()->where(['not in','id', $workMember])->andWhere(['status'=>StatusEnum::ENABLED])->distinct('dept_id')->select(['dept_id',])->all();
        $noWorksMember = [];
        $noWorksCount = 0;
        foreach ($dept_ids as $dept_model){
            $dept_name = $dept_model->department->name;
            $no_works_member = Member::find()->where(['not in','id', $workMember])->andWhere(['dept_id'=>$dept_model->dept_id,'status'=>StatusEnum::ENABLED])->select(['username'])->all();
            $noWorksMember[$dept_name] = $no_works_member;
            $noWorksCount += count($no_works_member);
        }
        return [$noWorksCount,$noWorksMember];
    }








}
