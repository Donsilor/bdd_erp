<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;
use common\enums\AuditStatusEnum;
use kartik\daterange\DateRangePicker;
use common\enums\StatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '款式列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div>
                <?php
                $get = Yii::$app->request->get();
                if (isset($get['SearchModel'])) {
                    $url = \common\helpers\ArrayHelper::merge([0 => 'index'], ['SearchModel' => $get['SearchModel']]);
                } else {
                    $url = ['index'];
                }
                ?>
                <?= Html::beginForm(Url::to($url), 'get') ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">筛选查询<a
                                    class="btn-xs btn-default glyphicon glyphicon-chevron-down searchBox"
                                    style="float:right;" href="javascript:void(0);" role="button">展开</a></h4>
                    </div>
                    <div class="modal-body" id="search-content">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">款号：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= Html::textInput('style_sn', $search->style_sn, ['class' => 'form-control', 'placeholder' => '多个以空格/英文/逗号隔开/单个模糊搜索', 'title'=>'多个以空格/英文/逗号隔开/单个模糊搜索']) ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">款式名称：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= Html::textInput('style_name', $search->style_name, ['class' => 'form-control', 'placeholder' => '模糊搜索']) ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">款式性别：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'style_sex',
                                            'value' => $search->style_sex,
                                            'data' => \addons\Style\common\enums\StyleSexEnum::getMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">款式材质：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'style_material',
                                            'value' => $search->style_material,
                                            'data' => \addons\Style\common\enums\StyleMaterialEnum::getMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">是否镶嵌：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'is_inlay',
                                            'value' => $search->is_inlay,
                                            'data' => \addons\Style\common\enums\InlayEnum::getMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">是否定制：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'is_made',
                                            'value' => $search->is_made,
                                            'data' => \common\enums\ConfirmEnum::getMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">是否赠品：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'is_gift',
                                            'value' => $search->is_gift,
                                            'data' => \common\enums\ConfirmEnum::getMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">审核状态：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'audit_status',
                                            'value' => $search->audit_status,
                                            'data' => AuditStatusEnum::getMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">创建人：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'creator_id',
                                            'value' => $search->creator_id,
                                            'data' => \Yii::$app->services->backendMember->getDropDown(),
                                            'options' => ['placeholder' => "请选择"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">创建时间：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= DateRangePicker::widget([    // 日期组件
                                            'model' => $search,
                                            //'attribute' => 'created_at',
                                            'name' => 'created_at',
                                            'value' => $search->created_at,
                                            'options' => ['placeholder' => "请选择", 'readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:220px;'],
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
                                        ])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">状态：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'status',
                                            'value' => $search->status,
                                            'data' => StatusEnum::getDestroyMap(),
                                            'options' => ['placeholder' => "请选择", 'multiple' => false, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">备注：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= Html::textInput('remark', $search->remark, ['class' => 'form-control', 'placeholder' => '模糊搜索']) ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">款式分类：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'style_cate_id',
                                            'value' => $search->style_cate_id,
                                            'data' => \Yii::$app->styleService->styleCate::getDropDown(),
                                            'options' => ['placeholder' => "请选择（可多选）", 'multiple' => true, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">产品线：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'product_type_id',
                                            'value' => $search->product_type_id,
                                            'data' => \Yii::$app->styleService->productType::getDropDown(),
                                            'options' => ['placeholder' => "请选择（可多选）", 'multiple' => true, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],
                                        ])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">归属渠道：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'style_channel_id',
                                            'value' => $search->style_channel_id,
                                            'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                                            'options' => ['placeholder' => "请选择（可多选）", 'multiple' => true, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">款式来源：</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= \kartik\select2\Select2::widget([
                                            'name' => 'style_source_id',
                                            'value' => $search->style_source_id,
                                            'data' => \Yii::$app->styleService->styleSource->getDropDown(),
                                            'options' => ['placeholder' => "请选择（可多选）", 'multiple' => true, 'style' => "width:180px"],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer text-center">
                            <button type="reset" class="btn btn-white btn-sm" onclick="clearSearch1()">重置</button>
                            <button class="btn btn-primary btn-sm">确定</button>
                        </div>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
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
                    //'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style' => 'width:100%;white-space:nowrap;'],
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
                                return Html::a($model->style_sn, ['view', 'id' => $model->id], ['class' => 'openContab', 'style' => "text-decoration:underline;color:#3c8dbc", 'id' => $model->style_sn]) . ' <i class="fa fa-copy" onclick="copy(\'' . $model->style_sn . '\')"></i>';
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
                            'headerOptions' => ['width' => '80'],
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
                                return Yii::$app->formatter->asDate($model->created_at);
                            }

                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                $str = \common\enums\StatusEnum::getValue($model->status, 'getDestroyMap');
                                if ($model->status == StatusEnum::DELETE) {
                                    $str = "<font color='red'>" . $str . "</font>";
                                }
                                return $str;
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status', \common\enums\StatusEnum::getDestroyMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width' => '60'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{attribute} {image} {apply} {audit} {status} {delete} {destroy}',
                            'buttons' => [
                                'attribute' => function ($url, $model, $key) {
                                    if ($model->status != \common\enums\StatusEnum::DELETE) {
                                        return Html::edit(['style-attribute/edit', 'style_id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '属性', [
                                            'class' => 'btn btn-primary btn-sm openIframe',
                                            'data-width' => '90%',
                                            'data-height' => '90%',
                                            'data-offset' => '20px',
                                        ]);
                                    }

                                },
                                'image' => function ($url, $model, $key) {
                                    if ($model->status != \common\enums\StatusEnum::DELETE) {
                                        return Html::edit(['style-image/ajax-edit-multe', 'style_id' => $model->id, 'returnUrl' => Url::getReturnUrl(), 'returnUrl' => Url::getReturnUrl()], '传图', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                },
                                'apply' => function ($url, $model, $key) {
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
                                    if ($model->audit_status == AuditStatusEnum::SAVE && $model->status == StatusEnum::DISABLED) {
                                        return Html::delete(['delete', 'id' => $model->id]);
                                    }
                                },
                                'destroy' => function ($url, $model, $key) {
                                    if ($model->audit_status == AuditStatusEnum::PASS) {
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
<script type="text/javascript">
    $(function () {
        var search = sessionStorageGet('styleSearch');
        if (search === false || search == '0') {
            $("#search-content").attr("style", "display:none;");
        }
    });

    function clearSearch1() {
        $('#search-content select').prop('selectedIndex', 0);
    }

    $(document).delegate('.searchBox', 'click', function () {
        if ($("#search-content").css("display") == "block") {
            $(".searchBox").html("展开");
            $(".searchBox").removeClass("glyphicon glyphicon-chevron-down").addClass("glyphicon glyphicon-chevron-up");
            sessionStorageAdd('styleSearch', 0, true);
        } else {
            $(".searchBox").html("隐藏");
            $(".searchBox").removeClass("glyphicon glyphicon-chevron-up").addClass("glyphicon glyphicon-chevron-down");
            sessionStorageAdd('styleSearch', 1, true);
        }
        $("#search-content").slideToggle();
    });
</script>