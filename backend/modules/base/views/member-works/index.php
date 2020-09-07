<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('member-works', '工作总结');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-body table-responsive">
                <div id="text">
                    <h4>今日未提交日志 <?= $noWorksCount?> 人：</h4>
                    <div>
                        <?php
                        $str = '';
                        foreach ($noWorksMembers as $deptName => $noWorksMember){
                            $str .= ' 【'. $deptName .'-'. count($noWorksMember) . ': ';
                            foreach ($noWorksMember as $member){
                                $str .= $member['username']." ; ";
                            }
                            $str .= '】 ';
                        }
                        echo $str;
                        ?>
                    </div>
                </div>
                <div class="text-right"><button onclick="copy('text')">复制</button></div>

            </div>
        </div>
        <!-- box end -->
    </div>


    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit','returnUrl' => Url::getReturnUrl()], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-xs',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>' width:100%;white-space:nowrap;' ],
                    'showFooter' => false,//显示footer行
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'attribute'=>'date',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'date',
                                'value' => $searchModel->date,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'startDate' => date('Y-m-01', strtotime(date("Y-m-d"))),
                                    'endDate' => date('Y-m-d',time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,


                                ],

                            ]),
                            'value'=>function($model){
                                return $model->date;
                            }

                        ],
                        [
                            'attribute' => 'type',
                            'filter' => false,
                            'value'=>function($model){
                                return \common\enums\WorksTypeEnum::getValue($model->type);
                            },
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'creator_id',
                            'value'=>function($model) {
                                $username = $model->member->username ?? '';
                                if($username != ''){
                                    return Html::a($model->member->username, ['view', 'creator_id' => $model->creator_id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                }else{
                                    return '';
                                }
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                            'format' => 'raw',
                            'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                'class' => 'form-control',
                            ]),

                        ],
                        [
                            'attribute' => 'dept_id',
                            'value'=>function($model) {
                                return $model->department->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'dept_id',Yii::$app->services->department->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'format' => 'raw',
                            //'headerOptions' => ['width'=>'150'],
                        ],
                        [
                            'label' => '岗位',
                            'value'=>function($model) {
                                return $model->member->assignment->role->title ?? '';
                            },
                            'filter' => false,
                            //'headerOptions' => ['width'=>'150'],
                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-2'],
                            'filter' =>false,
                        ],
                        [
                            'attribute' => 'content',
                            'format' => 'raw',
                            'contentOptions' => ['style'=>'max-width:500px;max-height:80px;white-space:pre-wrap;'],
                            'headerOptions' => ['class' => 'col-md-5'],
                            'filter' =>false,
                        ],



                        [
                            'attribute' => 'created_at',
                            'value' => function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'headerOptions' => ['class' => 'col-md-5'],
                            'filter' =>false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {info} ',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },

                                'delete' => function($url, $model, $key){
                                    return Html::delete(['delete', 'id' => $model->id]);
                                },
                            ],

                        ]
                    ]
                ]); ?>
            </div>
            </div>
        <!-- box end -->
        </div>
    </div>
<script>
    function batchExport() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if(ids.length == 0){
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }
    function copy (id) {
        let target = null;
        target = document.querySelector('#' + id);
        try {
            let range = document.createRange();
            range.selectNode(target);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            rfMsg('复制成功');
            console.log('复制成功')
        } catch (e) {
            console.log('复制失败')
        }

    }

</script>