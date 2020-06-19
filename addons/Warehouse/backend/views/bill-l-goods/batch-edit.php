<?php
use yii\widgets\ActiveForm;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin(['action'=>['batch-edit']]); ?>
            <div class="box-body" >
                <div class="row">
                    <div class="col-sm-2 text-right"><label class="control-label" for="stylestone-stone_type"><?= $model->getAttributeLabel($name)?></label></div>
                    <div class="col-sm-8">
                        <select id="stylestone-stone_type" class="form-control" name="value">
                            <option value="">请选择</option>
                            <?php foreach ($attr_arr as $key=>$val){ ?>
                                <option value="<?= $key?>"><?= $val?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="ids" value="<?= $ids?>"/>
                    <input type="hidden" name="name" value="<?= $name?>"/>
                </div>
               <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
