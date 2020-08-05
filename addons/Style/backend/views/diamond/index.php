<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;

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
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'goods_image',
                'value' => function ($model) {
                    return ImageHelper::fancyBox($model->goods_image);
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'goods_name',
                'value' => 'goods_name',
                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'300'],
            ],
            [
                'attribute' => 'goods_sn',
                'value' => 'goods_sn',
                'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'cert_id',
                'value' => 'cert_id',
                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                    'class' => 'form-control',
                    'style' =>'width:100px'
                ]),
                'format' => 'raw',
            ],

            [
                'attribute' => 'sale_price',
                'filter' => true,
                'format' => 'raw',
            ],
            //'cost_price',
            [
                'attribute' => 'carat',
                'filter' => true,
                'format' => 'raw',
            ],

            //'clarity',
            //'cut',
            //'color',
            //'shape',
            //'depth_lv',
            //'table_lv',
            //'symmetry',
            //'polish',
            //'fluorescence',
            //'source_id',
            //'source_discount',
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
                    return \common\enums\StatusEnum::getValue($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
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
