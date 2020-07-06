<?php
use common\helpers\Html;

$col =  12 / $level;
?>

<div class="row">
    <?php if ($level >= 1){ ?>
        <div class="col-lg-<?= $col; ?>">
            <?= $form->field($model, $countryName)->dropDownList(Yii::$app->services->country->getCityMapByPid(), [
                    'prompt' => '-- 请选择国家 --',
                    'onchange' => 'widget_country(this, 1,"' . Html::getInputId($model, $provinceName) . '","' . Html::getInputId($model, $cityName) . '")',
                ]); ?>
        </div>
    <?php }?>
    <?php if ($level >= 2){ ?>
        <div class="col-lg-<?= $col; ?>">
            <?= $form->field($model, $provinceName)->dropDownList(Yii::$app->services->country->getCityMapByPid($model->$countryName, 2), [
                    'prompt' => '-- 请选择省份 --',
                    'onchange' => 'widget_country(this,2,"' . Html::getInputId($model, $cityName) . '","'.Html::getInputId($model, $cityName) . '")',
                ]); ?>
        </div>
    <?php }?>
    <?php if ($level >= 3){ ?>
        <div class="col-lg-<?= $col; ?>">
            <?= $form->field($model, $cityName)->dropDownList(Yii::$app->services->country->getCityMapByPid($model->$provinceName, 3), [
                'prompt' => '-- 请选择城市 --',
            ]) ?>
        </div>
    <?php }?>
</div>

<script>
    function widget_country(obj, type_id, provinceId, cityId) {
        $(".form-group.field-" + cityId).hide();
        var pid = $(obj).val();
        $.ajax({
            type :"get",
            url : "<?= $url; ?>",
            dataType : "json",
            data : {type_id:type_id, pid:pid},
            success: function(data){
                if (type_id == 2) {
                    $(".form-group.field-"+cityId).show();
                }

                $("select#"+provinceId+"").html(data);
            }
        });
    }
</script>