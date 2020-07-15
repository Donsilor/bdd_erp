<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
$style_id =  Yii::$app->request->get('style_id');
$returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id'],'returnUrl'=>$returnUrl]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
            <?= $form->field($model, 'position')->dropDownList(\addons\Style\common\enums\StoneEnum::getPositionMap(),['prompt'=>'请选择']);?>
            <?= $form->field($model, 'stone_type')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE),['prompt'=>'请选择']);?>

            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
            <?= \yii\helpers\Html::activeHiddenInput($model,'style_id',array('value'=>$style_id)) ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
