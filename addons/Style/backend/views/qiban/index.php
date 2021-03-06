<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;
use kartik\daterange\DateRangePicker;
use addons\Style\common\enums\QibanTypeEnum;
use common\enums\StatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '起版列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit','1'=>1], '有款起版', [
                        'class' => 'btn btn-primary btn-xs openIframe',
                        'data-width'=>'90%',
                        'data-height'=>'90%',
                        'data-offset'=>'20px',
                    ]); ?>
                    <?= Html::create(['edit-no-style','1'=>1], '无款起版', [
                        'class' => 'btn btn-primary btn-xs openIframe',
                        'data-width'=>'90%',
                        'data-height'=>'90%',
                        'data-offset'=>'20px',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">  
    <?php //echo Html::batchButtons()?>                  
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
                    'class'=>'yii\grid\CheckboxColumn',
                    'name'=>'id',  //设置每行数据的复选框属性
                    'headerOptions' => ['width'=>'30'],
            ],
            [
                    'attribute' => 'id',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'style_image',
                'value' => function ($model) {
                    return \common\helpers\ImageHelper::fancyBox($model->style_image);
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'90'],
            ],
            [
                'attribute' => 'style_sn',
                'value' => "style_sn",
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'format_sn',
                'value'=>function($model) {
                    return $model->format_sn;
                },
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'150'],
            ],
            [
                'attribute' => 'qiban_sn',
                'value'=>function($model) {
                    return Html::a($model->qiban_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnTab()], ['class'=>'openContab','style'=>"text-decoration:underline;color:#3c8dbc"]);
                },
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'150'],
            ],
            [
                'label' => '归属渠道',
                'attribute' => 'channel.name',
                'value' => "channel.name",
                'filter' => Html::activeDropDownList($searchModel, 'style_channel_id',Yii::$app->styleService->styleChannel->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'headerOptions' => ['width'=>'300'],
                'attribute' => 'qiban_name',
                'value' => 'qiban_name',
                'filter' => Html::activeTextInput($searchModel, 'qiban_name', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
            ],
            [
                'attribute' => 'qiban_type',
                'value' => function($model){
                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'label' => '款式分类',
                'attribute' => 'cate.name',
                'value' => "cate.name",
                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'label' => '产品线',
                'attribute' => 'type.name',
                'value' => "type.name",
                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'label' => '成本价',
                'attribute' => 'cost_price',
                'value' => function ($model){
                    return $model->cost_price;
                },
                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'audit_status',
                'value' => function ($model){
                    $audit_name = Yii::$app->services->flowType->getCurrentUsersName(\common\enums\TargetTypeEnum::STYLE_QIBAN,$model->id);
                    $audit_name_str = $audit_name ? "({$audit_name})" : "";
                    return \common\enums\AuditStatusEnum::getValue($model->audit_status).$audit_name_str;
                },
                'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],

            [
                'attribute' => 'status',
                'value' => function ($model){
                    $str = \common\enums\StatusEnum::getValue($model->status,'getDestroyMap');
                    if($model->status === \common\enums\StatusEnum::DELETE) {
                        return "<font color='red'>".$str.'</font>';
                    }else{
                        return $str;
                    }                    
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getDestroyMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'creator_id',
                'value' => 'creator.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' =>false,

            ],
            [
                'attribute'=>'created_at',
                'value'=>function($model){
                    return Yii::$app->formatter->asDate($model->created_at);
                },
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'value' => $searchModel->created_at,
                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:100px;'],
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

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {format-edit} {apply} {audit} {delete} {destroy} {status}',
                'buttons' => [
                    'view'=> function($url, $model, $key){
                        return Html::edit(['view','id' => $model->id,'search'=>1,'returnUrl' => Url::getReturnUrl()],'详情',[
                                'class' => 'btn btn-info btn-sm',
                        ]);
                    },
                    'edit' => function($url, $model, $key){
                      //作废后不能编辑
                      if($model->status != \common\enums\StatusEnum::DELETE) {
                            if($model->qiban_type == QibanTypeEnum::HAVE_STYLE){
                                return Html::edit(['edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'class' => 'btn btn-primary btn-sm openIframe',
                                    'data-width'=>'90%',
                                    'data-height'=>'90%',
                                    'data-offset'=>'20px',
                                ]);
                            }else{
                                return Html::edit(['edit-no-style','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'class' => 'btn btn-primary btn-sm openIframe',
                                    'data-width'=>'90%',
                                    'data-height'=>'90%',
                                    'data-offset'=>'20px',
                                ]);
                            }
                       }

                    },
                    'format-edit' => function($url, $model, $key){
                        if($model->status != \common\enums\StatusEnum::DELETE) {
                            return Html::edit(['format-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'版式编辑',[
                                'class' => 'btn btn-primary btn-sm openIframe',
                                'data-width'=>'90%',
                                'data-height'=>'90%',
                                'data-offset'=>'20px',
                            ]);
                        }
                    },
                    'apply' => function($url, $model, $key){
                        if($model->audit_status == AuditStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'audit' => function($url, $model, $key){
                        if($model->audit_status == AuditStatusEnum::PENDING && Yii::$app->services->flowType->isAudit(\common\enums\TargetTypeEnum::STYLE_QIBAN,$model->id)){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                             ]); 
                        }
                    },

                    'status' => function($url, $model, $key){
                        if($model->audit_status == AuditStatusEnum::PASS){
                            return Html::lockStatus($model->status);
                        }                        
                    },
                    'delete' => function ($url, $model, $key) {
                        //未审核 + 保存 才可以删除
                        if($model->status == StatusEnum::DISABLED) {
                            return Html::delete(['delete', 'id' => $model->id]);
                        }
                    },
                    'destroy' => function ($url, $model, $key) {
                        if($model->audit_status == AuditStatusEnum::PASS) {
                            return Html::destory(['destroy', 'id' => $model->id]);
                        }
                    },
                    
                ]
            ]
        ]
      ]);
    ?>
            </div>
        </div>
    </div>
</div>
