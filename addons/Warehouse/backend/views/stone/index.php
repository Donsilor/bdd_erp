<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use addons\Warehouse\common\enums\StoneStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone', '石料库存');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
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
                    'options' => ['style'=>'white-space:nowrap;' ],
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
                            'attribute'=>'stone_sn',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->stone_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'stone_sn', [
                                'class' => 'form-control',
                                'style' => 'width:100px',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'stone_name',
                            'filter' => Html::activeTextInput($searchModel, 'stone_name', [
                                'class' => 'form-control',
                                'style' => 'width:150px',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style' => 'width:100px',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'stone_type',
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->stone_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'stone_status',
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\StoneStatusEnum::getValue($model->stone_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_status',\addons\Warehouse\common\enums\StoneStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px;'

                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '100'],
                        ],
                        [
                            'attribute'=>'stock_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'stock_cnt', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'stock_weight',
                            'filter' => Html::activeTextInput($searchModel, 'stock_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        /*[
                            'attribute'=>'stone_price',
                            'filter' => Html::activeTextInput($searchModel, 'stone_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],*/
                        [
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'stone_shape',
                            'value' => function ($model) {
                                return Yii::$app->attr->valueName($model->stone_shape)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'stone_color',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_color)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'stone_clarity',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_clarity)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'stone_symmetry',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_symmetry)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_symmetry',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'stone_polish',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_polish)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_polish',Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'stone_fluorescence',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_fluorescence)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_fluorescence',Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'stone_colour',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_colour)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_colour',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'cert_id',
                            //'filter' => Html::activeTextInput($searchModel, 'stone_size', [
                            //    'class' => 'form-control',
                            //]),
                            'value' => function ($model) {
                                return $model->cert_id??"";
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'cert_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->cert_type)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'stone_norms',
                            //'filter' => Html::activeTextInput($searchModel, 'stone_size', [
                            //    'class' => 'form-control',
                            //]),
                            'value' => function ($model) {
                                return $model->stone_norms??"";
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'stone_size',
                            //'filter' => Html::activeTextInput($searchModel, 'stone_size', [
                            //    'class' => 'form-control',
                            //]),
                            'value' => function ($model) {
                                return $model->stone_size??"";
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'100'],
                        ],
                        /*[
                            'attribute' => 'remark',
                            //'filter' => Html::activeTextInput($searchModel, 'stone_size', [
                            //    'class' => 'form-control',
                            //]),
                            'value' => function ($model) {
                                return $model->remark??"";
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'100'],
                        ],*/
                        [
                            'attribute' => 'creator_id',
                            'value' => 'creator.username',
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '100'],
                        ],
                        [
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'width:100px;'],
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
                            }
                        ],
                        /*[
                            'label' => '买入',
                            'attribute'=>'ms_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ms_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '分包转入',
                            'attribute'=>'fenbaoru_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'fenbaoru_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '送出',
                            'attribute'=>'ss_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ss_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '分包转出',
                            'attribute'=>'fenbaochu_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'fenbaochu_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '还回',
                            'attribute'=>'hs_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'hs_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '退石',
                            'attribute'=>'ts_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ts_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '退货',
                            'attribute'=>'th_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'th_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '遗失',
                            'attribute'=>'ys_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ys_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '损坏',
                            'attribute'=>'sy_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'sy_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '其它入库',
                            'attribute'=>'rk_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'rk_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '其它出库',
                            'attribute'=>'ck_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ck_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '',
                            'buttons' => [
                                'delete' => function($url, $model, $key){
                                    return Html::delete(['delete', 'id' => $model->id]);
                                },
                            ],
                        ],*/
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function($url, $model, $key){
                                    return Html::a('查看', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                            ],
                        ],
                    ]
                ]); ?>
            </div>
        </div>
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
</script>