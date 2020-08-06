<?php

use addons\Style\common\enums\AttrIdEnum;
use yii\widgets\ActiveForm;
use common\helpers\Url;
use kartik\date\DatePicker;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <div class="col-sm-12">
            <?= $form->field($model, 'parts_info')->widget(unclead\multipleinput\MultipleInput::class, [
                'max' => 3,
                'value' => $parts_list,
                'columns' => [
                    [
                        'name' => 'style_sn',
                        'title' => '配件款号',
                        'enableError' => false,
                        'options' => [
                            'class' => 'input-priority',
                            'style' => 'width:120px'
                        ]
                    ],
                    [
                        'name' => 'parts_name',
                        'title' => '配件名称',
                        'enableError' => false,
                        'options' => [
                            'class' => 'input-priority',
                            'style' => 'width:120px'
                        ]
                    ],
                    [
                        'name' => "material_type",
                        'title' => '配件材质',
                        'enableError' => false,
                        'type' => 'dropDownList',
                        'options' => [
                            'class' => 'input-priority',
                            'style' => 'width:100px',
                            'prompt' => '请选择',
                        ],
                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE)
                    ],
                    [
                        'name' => 'parts_weight',
                        'title' => '配件金重',
                        'enableError' => false,
                        'defaultValue' => '0.000',
                        'options' => [
                            'class' => 'input-priority',
                            'type' => 'number',
                            'style' => 'width:80px'
                        ]
                    ],
                    [
                        'name' => 'parts_price',
                        'title' => '配件金额',
                        'enableError' => false,
                        'defaultValue' => '0.00',
                        'options' => [
                            'class' => 'input-priority',
                            'type' => 'number',
                            'style' => 'width:80px'
                        ]
                    ]
                ]
            ])->label("");
            ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>