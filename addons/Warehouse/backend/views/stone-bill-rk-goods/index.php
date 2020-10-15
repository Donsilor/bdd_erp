<?php

use addons\Warehouse\common\enums\BillStatusEnum;

use common\helpers\Html;
use \addons\Style\common\enums\AttrIdEnum;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t_goods', '其它入库单详情(石料)');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';
?>
<style>
    select.form-control {
        font-size: 12px;
    }
</style>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no ?>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php

        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['ajax-edit', 'bill_id' => $bill->id], '新增石料', [
                'class' => 'btn btn-primary btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModalLg',
            ]);
        }
//        echo Html::a('单据打印', ['bill-t/print', 'id' => $bill->id], ['target' => '_blank', 'class' => 'btn btn-info btn-xs',]);
        echo '&nbsp;';
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::edit(['ajax-upload', 'bill_id' => $bill->id], '批量导入', [
                'class' => 'btn btn-success btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
        }
//        echo Html::button('明细导出', ['class' => 'btn btn-inverse btn-xs', 'onclick' => 'batchExport()',]);
//        echo '&nbsp;';
//        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
//            echo Html::tag('span', '批量删除', ["class" => "btn btn-danger btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['batch-delete']),]);
//        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false) ?>
                    <span style="font-size:16px">
                        <!--<span style="font-weight:bold;">明细汇总：</span>-->
                        金料总重量(g)：<span style="color:green;"><?= $bill->total_weight ?></span>
                        金料总额：<span style="color:green;"><?= $bill->total_cost ?></span>
                    </span>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
                        'showFooter' => false,//显示footer行
                        'id' => 'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'headerOptions' => ['width'=>20],
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'id',
                                'headerOptions' => ['width'=>30],
                                'value' => function ($model, $key, $index) {
                                    return $model->id ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'stone_sn',
                                'headerOptions' => ['class' => 'col-md-2'],
                                'value' => function ($model, $key, $index) {
                                    return $model->stone_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'stone_sn', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute' => 'style_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2'],
                                'value' => function ($model, $key, $index) {
                                    return $model->style_sn;
                                },
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute' => 'stone_type',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60],
                                'value' => function ($model, $key, $index) {
                                    return Yii::$app->attr->valueName($model->stone_type)??"";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAT_STONE_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],

                            [
                                'attribute' => 'stone_name',
                                'headerOptions' => ['class' => 'col-md-2'],
                                'value' => function ($model, $key, $index) {
                                    return $model->stone_name ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'stone_name', [
                                    'class' => 'form-control',
                                ]),
                            ],

                            [
                                'attribute' => 'stone_num',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>60,'class'=>'batch_full', 'attr-name' => 'stone_num'],
                                'value' => function ($model, $key, $index){
                                    return Html::ajaxInput('stone_num', $model->stone_num ?? 0, ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'stone_weight',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>60, 'class'=>'batch_full', 'attr-name' => 'stone_weight'],
                                'value' => function ($model, $key, $index){
                                    return Html::ajaxInput('stone_weight', $model->stone_weight ?? 0, ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'carat',
                                'headerOptions' => ['width'=>60],
                                'value' => function ($model, $key, $index){
                                    return $model->carat;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'stone_price',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>60, 'class'=>'batch_full', 'attr-name' => 'stone_price'],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxInput('stone_price',$model->stone_price ?? 0, ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_price',
                                'headerOptions' => ['width'=>60],
                                'value' => function ($model, $key, $index){
                                    return $model->cost_price ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'incl_tax_price',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>60, 'class'=>'batch_full', 'attr-name' => 'incl_tax_price'],
                                'value' => function ($model, $key, $index){
                                    return Html::ajaxInput('incl_tax_price', $model->incl_tax_price ?? 0, ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cert_type',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'cert_type','attr-name' => 'color','attr-id' => AttrIdEnum::DIA_CERT_TYPE],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'cert_type', Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'cert_id',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>60, 'class'=>'batch_full', 'attr-name' => 'cert_id'],
                                'value' => function ($model, $key, $index){
                                    return Html::ajaxInput('cert_id', $model->cert_id ?? '', ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'shape',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60],
                                'value' => function ($model, $key, $index) {
                                    return Yii::$app->attr->valueName($model->shape)??"";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'color',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'batch_select_full','attr-name' => 'color','attr-id' => AttrIdEnum::DIA_COLOR],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'color', Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'clarity',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60,  'class'=>'batch_select_full','attr-name' => 'clarity','attr-id' => AttrIdEnum::DIA_CLARITY],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'clarity', Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'cut',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'batch_select_full','attr-name' => 'cut','attr-id' => AttrIdEnum::DIA_CUT],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'cut', Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'symmetry',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'batch_select_full','attr-name' => 'symmetry','attr-id' => AttrIdEnum::DIA_SYMMETRY],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'symmetry', Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'symmetry',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'polish',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'batch_select_full','attr-name' => 'polish','attr-id' => AttrIdEnum::DIA_POLISH],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'polish', Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'polish',Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'fluorescence',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'batch_select_full','attr-name' => 'fluorescence','attr-id' => AttrIdEnum::DIA_FLUORESCENCE],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'fluorescence', Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'fluorescence',Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'stone_colour',
                                'format' => 'raw',
                                'headerOptions' => ['width' => 60, 'class'=>'batch_select_full','attr-name' => 'stone_colour','attr-id' => AttrIdEnum::DIA_COLOUR],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxSelect($model, 'stone_colour', Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_colour',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute' => 'stone_norms',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'stone_norms'],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxInput('stone_norms', $model->stone_norms ?? "", ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'stone_size',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'stone_size'],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxInput('stone_size', $model->stone_size ?? "", ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2 batch_full', 'attr-name' => 'remark'],
                                'value' => function ($model, $key, $index) {
                                    return Html::ajaxInput('remark', $model->remark ?? "", ['data-id' => $model->id]);
                                },
                                'filter' => false,
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'headerOptions' => ['class' => 'col-md-1'],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'bill_id' => $bill->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModalLg',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
<script type="text/javascript">
    $(function () {
        //默认全选
        $("input[name='id[]']").trigger("click");
    });


    function batchExport() {
        window.location.href = "<?= \common\helpers\Url::buildUrl('../bill-stone-rk/export',[],['ids'])?>?ids=<?php echo $bill->id ?>";
    }


</script>

<script type="text/javascript">
    $(function () {
        <?php
        if($bill->bill_status == BillStatusEnum::SAVE){
        ?>
        //批量填充(文本)
        $(".batch_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit'], "批量填充"); ?>');
        //批量填充(下拉)
        $(".batch_select_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit', 'check' => 1], "批量填充", ['input_type' => 'select']); ?>');
        //默认全选
        $("input[name='id_all']").trigger("click").prop("checked",true);
        <?php } ?>
    });

    function rfClearVal(obj) {
        var val = $(obj).val();
        if (val <= 0) {
            $(obj).val("");
        }
    }

</script>