<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use common\enums\WhetherEnum;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t_goods', '其它入库单详情');
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
            echo Html::create(['ajax-edit', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
        }
//        echo Html::a('单据打印', ['bill-t/print', 'id' => $bill->id], ['target' => '_blank', 'class' => 'btn btn-info btn-xs',]);
//        echo '&nbsp;';
//        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
//            echo Html::edit(['ajax-upload', 'bill_id' => $bill->id], '批量导入', [
//                'class' => 'btn btn-success btn-xs',
//                'data-toggle' => 'modal',
//                'data-target' => '#ajaxModal',
//            ]);
//            echo '&nbsp;';
//        }
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
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'id',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index) {
                                    return $model->id ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'gold_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index) {
                                    return $model->gold_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'style_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index) {
                                    return $model->style_sn;
                                },
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'gold_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index) {
                                    return Yii::$app->attr->valueName($model->gold_type)??"";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'gold_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAT_GOLD_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                            ],

                            [
                                'attribute' => 'gold_name',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index) {
                                    return $model->gold_name ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_name', [
                                    'class' => 'form-control',
                                    'style' => 'width:90px;'
                                ]),
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'value' => function ($model, $key, $index){
                                    return $model->gold_weight ?? 0;
                                },
                                'filter' => false,
                            ],

                            [
                                'attribute' => 'gold_price',
                                'value' => function ($model, $key, $index) {
                                    return $model->gold_price ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_price',
                                'value' => function ($model, $key, $index){
                                    return $model->cost_price ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'incl_tax_price',
                                'value' => function ($model, $key, $index){
                                    return $model->incl_tax_price ?? 0;
                                },
                                'filter' => false,
                            ],

                            [
                                'attribute' => 'remark',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index) {
                                    return $model->remark ?? "";
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
                                            return Html::edit(['edit', 'id' => $model->id, 'bill_id' => $bill->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
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
        window.location.href = "<?= \common\helpers\Url::buildUrl('../bill-t/export',[],['ids'])?>?ids=<?php echo $bill->id ?>";
    }


</script>