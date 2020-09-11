<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;
use common\enums\AuditStatusEnum;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '款式列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                    <?= Html::edit(['ajax-upload'], '批量导入', [
                        'class' => 'btn btn-primary btn-xs',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php //echo Html::batchButtons()?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    //'options' => ['style'=>'width:100%;white-space:nowrap;' ],
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
                            'headerOptions' => ['width' => '30'],
                        ],
                        [
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width' => '80'],
                        ],
                        [
                            'attribute' => 'style_image',
                            'value' => function ($model) {
                                return ImageHelper::fancyBox($model->style_image);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['style' => '110'],
                        ],
                        [
                            'attribute' => 'style_sn',
                            'value' => function ($model) {
                                return Html::a($model->style_sn, ['view', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['style' => "text-decoration:underline;color:#3c8dbc",'id'=>$model->style_sn]).' <i class="fa fa-copy" onclick="copy(\''. $model->style_sn .'\')"></i>';
                            },
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style' => 'width:110px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '110'],
                        ],
                        [
                            'attribute' => 'style_name',
                            'value' => 'style_name',
                            'filter' => Html::activeTextInput($searchModel, 'style_name', [
                                'class' => 'form-control',
                                'style' => 'width:200px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '200'],
                        ],
                        [
                            'attribute' => 'style_cate_id',
                            'value' => function ($model) {
                                return $model->cate->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', Yii::$app->styleService->styleCate->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '100'],
                        ],
                        [
                            'attribute' => 'product_type_id',
                            'value' => function ($model) {
                                return $model->type->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'product_type_id', Yii::$app->styleService->productType->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:120px;'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '120'],
                        ],
                        [
                            'attribute' => 'is_inlay',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_inlay', \addons\Style\common\enums\InlayEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px'
                            ]),
                            'headerOptions' => ['width' => '110'],
                        ],
                        [
                            'attribute' => 'style_channel_id',
                            'value' => function ($model) {
                                return $model->channel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_channel_id', Yii::$app->styleService->styleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '100'],
                        ],
                        [
                            'attribute' => 'is_autosn',
                            'value' => function ($model) {
                                return \common\enums\AutoSnEnum::getValue($model->is_autosn);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_autosn', \common\enums\AutoSnEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '100'],
                        ],
                        [
                            'attribute' => 'goods_num',
                            'value' => "goods_num",
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                                'style' => 'width:80px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '80'],
                        ],
                        [
                            'attribute' => 'audit_status',
                            'value' => function ($model) {
                                $audit_name = Yii::$app->services->flowType->getCurrentUsersName(\common\enums\TargetTypeEnum::STYLE_STYLE, $model->id);
                                $audit_name_str = $audit_name ? "({$audit_name})" : "";
                                return \common\enums\AuditStatusEnum::getValue($model->audit_status) . $audit_name_str;
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'audit_status', \common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '100'],
                        ],
//            [
//                'attribute' => 'creator_id',
//                'value' => 'creator.username',
//                'headerOptions' => ['class' => 'col-md-1'],
//                'filter' => Html::activeTextInput($searchModel, 'creator.username', [
//                    'class' => 'form-control',
//                    'style'=> 'width:100px;'
//                ]),
//            ],
                        [
                            'attribute' => 'creator_id',
                            'value' => "creator.username",
                            'filter' => \kartik\select2\Select2::widget([
                                'name' => 'SearchModel[creator_id]',
                                'value' => $searchModel->creator_id,
                                'data' => \Yii::$app->services->backendMember->getDropDown(),
                                'options' => ['placeholder' => "请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:100px;'],
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
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            }

                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return \common\enums\StatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status', \common\enums\StatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '80'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {ajax-apply} {audit} {status}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);

                                },
                                'ajax-apply' => function ($url, $model, $key) {
                                    if ($model->audit_status == AuditStatusEnum::SAVE) {
                                        return Html::edit(['ajax-apply', 'id' => $model->id], '提审', [
                                            'class' => 'btn btn-success btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },

                                'audit' => function ($url, $model, $key) {
                                    $isAudit = Yii::$app->services->flowType->isAudit(\common\enums\TargetTypeEnum::STYLE_STYLE, $model->id);
                                    if ($model->audit_status == AuditStatusEnum::PENDING && $isAudit) {
                                        return Html::edit(['ajax-audit', 'id' => $model->id], '审核', [
                                            'class' => 'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'status' => function ($url, $model, $key) {
                                    if ($model->audit_status == AuditStatusEnum::PASS) {
                                        return Html::status($model->status);
                                    }
                                },
                                'delete' => function ($url, $model, $key) {
                                    if ($model->audit_status == 0) {
                                        return Html::delete(['delete', 'id' => $model->id]);
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
<script type="text/javascript">
    /**
     * 一键粘贴
     * @param  {String} id [需要粘贴的内容]
     * @param  {String} attr [需要 copy 的属性，默认是 innerText，主要用途例如赋值 a 标签上的 href 链接]
     *
     * range + selection
     *
     * 1.创建一个 range
     * 2.把内容放入 range
     * 3.把 range 放入 selection
     *
     * 注意：参数 attr 不能是自定义属性
     * 注意：对于 user-select: none 的元素无效
     * 注意：当 id 为 false 且 attr 不会空，会直接复制 attr 的内容
     */
    function copy (id, attr = null) {
        let target = null;
        if (attr) {
            target = document.createElement('div');
            target.id = 'tempTarget';
            target.style.opacity = '0';
            if (id) {
                let curNode = document.querySelector('#' + id);
                target.innerText = curNode[attr];
            } else {
                target.innerText = attr;
            }
            document.body.appendChild(target);
        } else {
            target = document.querySelector('#' + id);
        }

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

        if (attr) {
            // remove temp target
            target.parentElement.removeChild(target);
        }
    }

    function cleardd() {
        $('#select select').prop('selectedIndex', 0);
    }
</script>