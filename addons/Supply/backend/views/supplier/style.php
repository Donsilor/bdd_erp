<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;
use common\enums\AuditStatusEnum;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '供应商详情';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title ?> - <?= $model->supplier_name?> - <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="col-xs-12" style="padding: 0px;">
                    <div class="box" style="margin-bottom: 0px;">
                        <div class="box-body table-responsive">
                        <?php //echo Html::batchButtons()?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'tableOptions' => ['class' => 'table table-hover'],
                            'options' => ['style'=>'width:100%;white-space:nowrap;' ],
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
                                    'attribute' => 'style_id',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '80'],
                                ],
                                [
                                    'attribute' => 'style.style_image',
                                    'value' => function ($model) {
                                        return ImageHelper::fancyBox($model->style->style_image ?? '');
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style' => '110'],
                                ],
                                [
                                    'attribute' => 'style.style_sn',
                                    'value' => function ($model) {
                                        return Html::a($model->style->style_sn, ['../style/style/view', 'id' => $model->id], ['class'=>'openContab','style' => "text-decoration:underline;color:#3c8dbc",'id'=>$model->style->style_sn]).' <i class="fa fa-copy" onclick="copy(\''. $model->style->style_sn .'\')"></i>';
                                    },
                                    'filter' => Html::activeTextInput($searchModel, 'style.style_sn', [
                                        'class' => 'form-control',
                                        'style' => 'width:110px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '110'],
                                ],
                                [
                                    'attribute' => 'style.style_name',
                                    'value' => 'style.style_name',
                                    'filter' => Html::activeTextInput($searchModel, 'style.style_name', [
                                        'class' => 'form-control',
                                        'style' => 'width:200px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '200'],
                                ],
                                [
                                    'attribute' => 'style.style_cate_id',
                                    'value' => function ($model) {
                                        return $model->style->cate->name ?? '';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style.style_cate_id', Yii::$app->styleService->styleCate->getDropDown(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:100px',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '100'],
                                ],
                                [
                                    'attribute' => 'style.product_type_id',
                                    'value' => function ($model) {
                                        return $model->style->type->name ?? '';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style.product_type_id', Yii::$app->styleService->productType->getDropDown(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:120px;'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '120'],
                                ],
                                [
                                    'attribute' => 'style.is_inlay',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return \addons\Style\common\enums\InlayEnum::getValue($model->style->is_inlay);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style.is_inlay', \addons\Style\common\enums\InlayEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:100px'
                                    ]),
                                    'headerOptions' => ['width' => '110'],
                                ],
                                [
                                    'attribute' => 'style.style_channel_id',
                                    'value' => function ($model) {
                                        return $model->style->channel->name ?? '';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style.style_channel_id', Yii::$app->styleService->styleChannel->getDropDown(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '100'],
                                ],

                                [
                                    'attribute' => 'style.audit_status',
                                    'value' => function ($model) {
                                        $audit_name = Yii::$app->services->flowType->getCurrentUsersName(\common\enums\TargetTypeEnum::STYLE_STYLE, $model->style_id);
                                        $audit_name_str = $audit_name ? "({$audit_name})" : "";
                                        return \common\enums\AuditStatusEnum::getValue($model->style->audit_status) . $audit_name_str;
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style.audit_status', \common\enums\AuditStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '100'],
                                ],
                                [
                                    'attribute' => 'style.creator_id',
                                    'value' => "style.creator.username",
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '80'],
                                ],
                                [
                                    'attribute' => 'style.created_at',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return Yii::$app->formatter->asDate($model->style->created_at);
                                    }

                                ],
                                [
                                    'attribute' => 'style.status',
                                    'value' => function ($model) {
                                        return \common\enums\StatusEnum::getValue($model->style->status);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style.status', \common\enums\StatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:80px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '60'],
                                ],

                            ]
                        ]);
                        ?>
                        </div>
                    </div>
                </div>
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