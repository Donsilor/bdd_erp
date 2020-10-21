<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-upload', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div>",
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
        <?= $form->field($model, 'file')->fileInput()->label("<span style='color: red'>*</span> 文件上传") ?>
        <?= Html::a("下载”通用产品”导入模板", ['ajax-upload', 'bill_id' => $bill->id, 'download' => 1, 'download_type' => 1], ['style' => "text-decoration:underline;color:#3c8dbc"]) ?> |
        <?= Html::a("下载“素金产品”导入模板", ['ajax-upload', 'bill_id' => $bill->id, 'download' => 1, 'download_type' => 2], ['style' => "text-decoration:underline;color:#45b97c"]) ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button id="save" class="btn btn-primary" type="submit" data-loading-text="保存中...">保存</button>
</div>
<?php ActiveForm::end(); ?>
<script>
    $(function () {
        $("#save").click(function () {
            $(this).button('loading').delay(1000).queue(function () {
                // $(this).button('reset');
                // $(this).dequeue();
            });
        });
    });
</script>
