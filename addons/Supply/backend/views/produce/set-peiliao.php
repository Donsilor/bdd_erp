<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['set-peiliao','id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
            <div class="col-lg-6">
                <?= $form->field($model, 'peishi_type')->dropDownList(\addons\Supply\common\enums\PeishiTypeEnum::getMap(),['prompt'=>'请选择','disabled'=>$model->is_inlay == \addons\Style\common\enums\InlayEnum::No]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'peiliao_type')->dropDownList(\addons\Supply\common\enums\PeiliaoTypeEnum::getMap(),['prompt'=>'请选择'])->label("配料类型(只允许黄金/铂金/银进行配料)") ?>
            </div>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
