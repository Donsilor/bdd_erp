<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['edit','id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">盘点 - <?php echo $model->bill_no?></h2>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class=" table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right">货号：</td>
                            <td><?= $form->field($model, 'goods_id')->textArea()?></td>
                        </tr>
                        
                        <tr>
                            <td class="col-xs-1 text-right">应盘数量：</td>
                            <td><?= $model->goods_num ?></td>
                        </tr> 
                        <tr>
                            <td class="col-xs-1 text-right">实盘数量：</td>
                            <td> 0 </td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right">总金额：</td>
                            <td><?= $model->total_cost ?></td>
                        </tr>                        
                    </table>
                </div>                
            </div>
        </div>

    <!-- box end -->
</div>
<!-- tab-content end -->
</div>