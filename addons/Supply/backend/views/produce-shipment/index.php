<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('style_channel', '出厂信息');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">布产详情 - <?php echo $produce->produce_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-16" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" >
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],


                            [
                                'attribute' => 'status',
                                'value' => function($model){
                                    return \addons\Supply\common\enums\QcTypeEnum::getValue($model->status);
                                },
                                'filter' =>Html::activeDropDownList($searchModel, 'status',\addons\Supply\common\enums\QcTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),

                            ],
                            [
                                'label' => '总数量',
                                'value' => 'produce.goods_num'

                            ],

                            [
                                'attribute'=>'shippent_num',
                                'filter' => false,

                            ],
                            [
                                'attribute'=>'nopass_num',
                                'filter' => false,

                            ],
                            [
                                'attribute'=>'nopass_reason',
                                'value' => function($model){
                                    return \addons\Supply\common\enums\NopassReasonEnum::getValue($model->nopass_reason);
                                },
                                'filter' =>Html::activeDropDownList($searchModel, 'nopass_reason',\addons\Supply\common\enums\NopassReasonEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),


                            ],

                            [
                                'attribute'=>'remark',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'created_at',
                                'filter' => false,
                                'value' => function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }

                            ],

                            [
                                'label' => '操作人',
                                'attribute' => 'creator',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'creator', [
                                    'class' => 'form-control',
                                ]),

                            ],


                        ]
                    ]); ?>
                </div>
            </div>
            <!-- box end -->
        </div>
    </div>
</div>