<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['to-factory','id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">生产出厂</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
            <div class="row"><div class="col-lg-12"><?= $form->field($model, 'status',['onchange'=>'getFollower()'])->radioList(\addons\Supply\common\enums\QcTypeEnum::getMap())?></div></div>
            <div class="row">
                <div class="col-lg-4"><?= $form->field($model,'shippent_num')->textInput()->hint('')?></div>
                <div class="col-lg-4"><?= $form->field($model,'failed_num')->textInput()->hint('')?></div>
                <div class="col-lg-4"><?= $form->field($model,'failed_reason')->textInput()->hint('')?></div>
            </div>
            <div class="row">
                <div class="col-lg-4"><?= $form->field($model,'nopass_num')->textInput()->hint('')?></div>
                <div class="col-lg-4"><?= $form->field($model, 'nopass_type')->dropDownList(\addons\Supply\common\enums\NopassTypeEnum::getMap(),['prompt'=>'请选择']);?></div>
                <div class="col-lg-4"><?= $form->field($model, 'nopass_reason')->dropDownList(\addons\Supply\common\enums\NopassReasonEnum::getMap(),['prompt'=>'请选择']);?></div>
            </div>
            <div class="row"><div class="col-lg-12"><?= $form->field($model,'remark')->textInput()->hint('')?></div></div>

        </div>
        <!-- /.tab-content -->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script>
    function getFollower() {
        var supplier_id = $("#tofactoryform-supplier_id").val();
        var html = '<option>请选择</option>';
        $.ajax({
            url: '<?= \yii\helpers\Url::to(["get-follower"]) ?>',
            type: 'post',
            dataType: 'json',
            data: {supplier_id: supplier_id},
            success: function (msg) {
                console.log(msg.data)
                $.each(msg.data, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#tofactoryform-follower_id").html(html);
            }
        })
    }
    
</script>
