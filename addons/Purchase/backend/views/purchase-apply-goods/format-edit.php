<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\QibanTypeEnum;

$this->title = '版式编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-12">
            <div class="box">
                <?php $form = ActiveForm::begin([]); ?>
                <div class="box-body" style="padding:20px 50px">
                        <div class="row">
                            <div class="col-lg-4">
                                <?= $form->field($model, 'format_sn')->textInput() ?>
                            </div>
                        </div>
                        <div class="row col-lg-4">
                                <?= $form->field($model, 'format_sn')->textInput() ?>
                        </div>
                        <div class="row col-lg-9">
                            <?php $model->format_images = !empty($model->format_images)?explode(',', $model->format_images):null;?>
                            <?= $form->field($model, 'format_images')->widget(common\widgets\webuploader\Files::class, [
                                'config' => [
                                    'pick' => [
                                        'multiple' => true,
                                    ],
                                ]
                            ]); ?>
                        </div>
                    <?php if($model->style_id) {?>
                        <div style="margin: 0px 0 20px 0;">
                            <h3 class="box-title"> 其他信息</h3>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <?= $form->field($model, 'stone_info')->textarea() ?>
                            </div>
                            <div class="col-lg-4">
                                <?= $form->field($model, 'parts_info')->textarea() ?>
                            </div>
                            <div class="col-lg-4">
                                <?= $form->field($model, 'remark')->textarea() ?>
                            </div>
                        </div>
                    <?php }?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
    </div>
</div>