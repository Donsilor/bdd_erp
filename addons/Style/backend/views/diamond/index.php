<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;
use addons\Style\common\enums\AttrIdEnum;

$id = $searchModel->id;
$goods_name = $searchModel->goods_name;
$goods_sn = $searchModel->goods_sn;
$cert_id = $searchModel->cert_id;
$sale_price = $searchModel->sale_price;
$carat = $searchModel->carat;
$status = $searchModel->status;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods_diamond', '裸钻管理');
$this->params['breadcrumbs'][] = $this->title;
//$cert_type = \common\enums\DiamondEnum::getCertTypeList();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools"  style="right: 100px;">
                    <?= Html::create(['edit']) ?>
                </div>
                <div class="box-tools" >
                    <a href="<?= Url::to(['export?goods_name='.$goods_name.'&id='.$id.'&goods_sn='.$goods_sn.'&cert_id='.$cert_id.'&sale_price='.$sale_price.'&carat='.$carat.'&status='.$status])?>" class="blue">导出Excel</a>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>         
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'options' => ['style'=>' width:120%;white-space:nowrap;'],
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
                'value' => 'id',
                'filter' => Html::activeTextInput($searchModel, 'id', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'goods_image',
                'value' => function ($model) {
                    return ImageHelper::fancyBox($model->goods_image);
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'goods_name',
                'value' => function($model){
                    return Html::a($model->goods_name,['view','id' => $model->id,'returnUrl'=>Url::getReturnUrl()] ,['style'=>"text-decoration:underline;color:#3c8dbc"]);
                },
                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'goods_sn',
                'value' => 'goods_sn',
                'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'cert_type',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->cert_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'cert_id',
                'value' => 'cert_id',
                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                    'class' => 'form-control',
                    'style' =>'width:100px'
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],

            [
                'attribute' => 'sale_price',
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            //'cost_price',
            [
                'attribute' => 'carat',
                'filter' => true,
                'headerOptions' => ['class' => 'col-md-1'],
                'format' => 'raw',
            ],
            [
                'attribute' => 'shape',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->shape);
                },
                'filter' => Html::activeDropDownList($searchModel, 'shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'color',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->color);
                },
                'filter' => Html::activeDropDownList($searchModel, 'color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'clarity',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->clarity);
                },
                'filter' => Html::activeDropDownList($searchModel, 'clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'cut',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->cut);
                },
                'filter' => Html::activeDropDownList($searchModel, 'cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'polish',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->polish);
                },
                'filter' => Html::activeDropDownList($searchModel, 'polish',Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'fluorescence',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->fluorescence);
                },
                'filter' => Html::activeDropDownList($searchModel, 'fluorescence',Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'symmetry',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                'value' => function ($model){
                    return Yii::$app->attr->valueName($model->symmetry);
                },
                'filter' => Html::activeDropDownList($searchModel, 'symmetry',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'is_stock',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \addons\Sales\common\enums\IsStockEnum::getValue($model->is_stock);
                },
                'filter' => Html::activeDropDownList($searchModel, 'is_stock',\addons\Sales\common\enums\IsStockEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'audit_status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\FrameEnum::getValue($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\FrameEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            //'created_at',
            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {ajax-apply} {audit}  {view}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE || $model->audit_status == \common\enums\AuditStatusEnum::UNPASS) {
                        return Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                    }
                },
                'ajax-apply' => function($url, $model, $key){
                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                            'class'=>'btn btn-success btn-sm',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                        ]);
                    }
                },

                'audit' => function($url, $model, $key){
                    if($model->audit_status == \common\enums\AuditStatusEnum::PENDING){
                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-sm',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
                },
               'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                  },
                'delete' => function($url, $model, $key){
                        return Html::delete(['delete', 'id' => $model->id]);
                },
                'view'=> function($url, $model, $key){
                    return Html::a('详情',['view','id'=>$model->id] ,['class'=>'btn btn-info btn-sm']);
                },
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
