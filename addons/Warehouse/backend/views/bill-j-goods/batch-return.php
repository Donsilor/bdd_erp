<?php

use common\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

?>
<div class="row">
    <?php $form = ActiveForm::begin([]); ?>
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'restore_time')->widget(DatePicker::class, [
                    'language' => 'zh-CN',
                    'options' => [
                        'value' => date('Y-m-d', time()),
                    ],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,//今日高亮
                        'autoclose' => true,//选择后自动关闭
                        'todayBtn' => true,//今日按钮显示
                    ]
                ]); ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'qc_status')->radioList(\addons\Warehouse\common\enums\QcStatusEnum::getReturnMap()); ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'qc_remark')->textArea(); ?>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover'],
            'options' => ['style' => 'width:100%;white-space:nowrap;'],
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
                    'attribute' => 'goods_id',
                    'filter' => true,
                ],
                [
                    'attribute' => 'style_sn',
                    'filter' => true,
                ],
                [
                    'attribute' => 'goods_name',
                    'filter' => true,
                ],
                [
                    'label' => '未还数量',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
                    'value' => function ($model) {
                        return $model->stock_num ?? 0;
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => '还货数量',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1', "style" => "background-color:#84bf96;"],
                    'value' => function ($_model) use ($form, $model) {
                        return $form->field($model, "goods_list[{$_model->goods_id}][goods_num]")->textInput(['value' => ''])->label(false);
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'goods.style_cate_id',
                    'value' => 'goods.styleCate.name',
                    'filter' => true,
                ],
                [
                    'attribute' => 'warehouse_id',
                    'value' => "warehouse.name",
                    'filter' => false,
                ],
                [
                    'attribute' => 'goods.material_type',
                    'value' => function ($model) {
                        return \Yii::$app->attr->valueName($model->goods->material_type ?? false) ?? '';
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'goods.material_color',
                    'value' => function ($model) {
                        return \Yii::$app->attr->valueName($model->goods->material_color ?? false) ?? '';
                    },
                    'filter' => false,
                ],
                [
                    'label' => '手寸',
                    'value' => function ($model) {
                        $finger = '';
                        if ($model->goods->finger ?? false) {
                            $finger .= \Yii::$app->attr->valueName($model->goods->finger) . '(US)';
                        }
                        if ($model->goods->finger_hk ?? false) {
                            $finger .= ' ' . \Yii::$app->attr->valueName($model->goods->finger_hk) . '(HK)';
                        }
                        return $finger;
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'goods.suttle_weight',
                    'filter' => false,
                ],
            ]
        ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>