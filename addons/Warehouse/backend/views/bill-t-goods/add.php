<?php

use unclead\multipleinput\MultipleInput;
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\StyleSexEnum;

$this->title = '新增货品';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <div class="row">
                     <div class="col-lg-3">
                        <?= $form->field($model, 'style_sn')->textInput(["placeholder"=>"请输入款号"]) ?>
                        <?= $form->field($model, 'goods_num')->textInput(["placeholder"=>"请输入数量"]) ?>
                     </div>
                 </div>
               <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
