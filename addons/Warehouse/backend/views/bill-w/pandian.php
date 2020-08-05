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
    <h2 class="page-header">货品盘点 - <?php echo $model->bill_no?></h2>
    <div class="box">
        <div class=" table-responsive">
            <table class="table table-hover">
                <tr>
                    <td class="col-xs-1 text-right no-border-top">货号：</td>
                    <td class="col-xs-4 text-left no-border-top"><?= $form->field($model, 'goods_ids')->textArea(['style'=>'height:200px;'])->label(false)?></td>
                    <td class="text-left no-border-top"><button class="btn btn-primary" type="submit">盘点</button></td>
                </tr>
                <tr>
                    <td class="col-xs-1 text-right">盘点仓库：</td>
                    <td><?= $model->toWarehouse->name ??'' ?></td>
                </tr>                         
                <tr>
                    <td class="col-xs-1 text-right">应盘数量：</td>
                    <td><?= $model->billW->should_num ?? 0;?></td>
                </tr> 
                <tr>
                    <td class="col-xs-1 text-right">实盘数量：</td>
                    <td> <?= $model->billW->actual_num ?? 0;?> </td>
                </tr>                  
            </table>
        </div>                
    </div>
</div>
<?php ActiveForm::end(); ?>
