<?php

use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;
$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-import']),
        'fieldConfig' => [
                
        ]
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12">
        <?= $form->field($model, 'sale_channel_id')->widget(\kartik\select2\Select2::class, [
            'data' => Yii::$app->salesService->saleChannel->getDropDownForExternalOrder(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);?>       
        <?= $form->field($model, 'file')->fileInput() ?>
        <?= Html::a("下载数据导入模板", ['ajax-import','download' => 1], ['style' => "text-decoration:underline;color:#3c8dbc"]).'<br/>' ?>        
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>