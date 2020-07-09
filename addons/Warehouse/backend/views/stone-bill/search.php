<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone_bill', '单据列表');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?php $form = ActiveForm::begin(['action' => ['search'], 'method'=>'get']); ?>
                    <div class="col-xs-3">
                        <?= $form->field($model, 'stone_sn')->textInput(["placeholder"=>"请输入石料编号"]) ?>
                    </div>
                    <div class="col-xs-3" style="padding-top: 26px;padding-left: 0px;">
                        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'width:120%;'],
                    'showFooter' => false,//显示footer行
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'class'=>'yii\grid\CheckboxColumn',
                            'name'=>'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width'=>'30'],
                        ],
                        /*[
                            'label' => '序号',
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'80'],
                        ],*/
                        [
                            'attribute' => 'bill_type',
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\StoneBillTypeEnum::getValue($model->bill_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'bill_type',\addons\Warehouse\common\enums\StoneBillTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px;'

                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'width:100px;'],
                        ],
                        [
                            'attribute'=>'bill_no',
                            'value'=>function($model) {
                                return Html::a($model->bill_no, ['view', 'id' => $model->bill->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'bill.bill_status',
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\BillStatusEnum::getValue($model->bill->bill_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'bill.bill_status',\addons\Warehouse\common\enums\BillStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px;'

                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute'=>'stone_sn',
                            'filter' =>false,
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'bill.supplier_id',
                            'value' =>"bill.supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[supplier_id]',
                                'value'=>$searchModel->supplier_id,
                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                'options' => ['placeholder' =>"请选择",'class' => 'col-md-4', 'style'=> 'width:120px;'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'width'=>'260px',
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'stone_num',
                            'filter' => Html::activeTextInput($searchModel, 'stone_num', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'120'],
                        ],
                        [
                            'attribute'=>'stone_weight',
                            'filter' => Html::activeTextInput($searchModel, 'stone_weight', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '制单人',
                            'attribute' => 'bill.creator_id',
                            'value' => 'bill.creator.username',
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute'=>'bill.created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'endDate' => date('Y-m-d',time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,
                                ],
                            ]),
                            'value'=>function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'label' => '审核人',
                            'attribute' => 'bill.auditor_id',
                            'value' =>"bill.auditor.username",
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'bill.audit_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'bill.audit_time',
                                'value' => '',
                                'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'endDate' => date('Y-m-d', time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,
                                ],
                            ]),
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->bill->audit_time);
                            },
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'attribute' => 'bill.audit_status',
                            'format' => 'raw',
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->bill->audit_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'bill.audit_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '80'],
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>