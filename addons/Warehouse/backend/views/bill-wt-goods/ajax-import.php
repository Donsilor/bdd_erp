<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;
$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-import', 'bill_id' => Yii::$app->request->get('bill_id')]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12">       
        <?= $form->field($model, 'file')->fileInput() ?>
        <?= Html::a("下载数据导入模板", ['ajax-import','download' => 1], ['style' => "text-decoration:underline;color:#3c8dbc"]).'<br/>' ?>        
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>