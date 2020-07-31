<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['pandian','id' => $model->id]),
        'fieldConfig' => [
                
        ]
]);
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">配件盘点 - <?php echo $model->bill_no?></h2>
    <div class="box">
        <div class=" table-responsive">
            <table class="table table-hover">
                <tr>
                    <td class="col-xs-1 text-right no-border-top">配件编号：</td>
                    <td class="col-xs-4 text-left no-border-top"><?= $form->field($model, 'parts_sn')->textInput()->label(false)?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right no-border-top">配件件数：</td>
                    <td class="col-xs-4 text-left no-border-top"><?= $form->field($model, 'parts_num')->textInput()->label(false)?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right no-border-top">配件总重：</td>
                    <td class="col-xs-4 text-left no-border-top"><?= $form->field($model, 'parts_weight')->textInput()->label(false)?></td>
                    <td class="text-left no-border-top"><button class="btn btn-primary" type="submit">盘点</button></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">盘点仓库：</td>
                    <td><?= $model->toWarehouse->name ??'' ?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">盘点配件：</td>
                    <td><?= Yii::$app->attr->valueName($model->billW->parts_type) ??'' ?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">应盘数量：</td>
                    <td style='color:green'><?= $model->billW->should_num ?? 0;?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">实盘数量：</td>
                    <td style='color:red'> <?= $model->billW->actual_num ?? 0;?> </td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">应盘件数：</td>
                    <td style='color:green'><?= $model->billW->should_grain ?? 0;?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">实盘件数：</td>
                    <td style='color:red'> <?= $model->billW->actual_grain ?? 0;?> </td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">应盘重量(g)：</td>
                    <td style='color:green'><?= $model->billW->should_weight ?? 0;?></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">实盘重量(g)：</td>
                    <td style='color:red'> <?= $model->billW->actual_weight ?? 0;?> </td>
                </tr>
            </table>
        </div>                
    </div>
</div>
<?php ActiveForm::end(); ?>
