<?php
use common\helpers\Html;
use yii\helpers\Url;
?>

<?php if ($level >= 1){ ?>
    <?= $form->field($model, $countryName)->dropDownList(Yii::$app->services->country->getProvinceMapByPid(), [
            'prompt' => '-- 请选择国家 --',
            'onchange' => 'widget_country(this, 1,"' . Html::getInputId($model, $countryName) . '","' . Html::getInputId($model, $cityName) . '")',
        ]); ?>
<?php }?>
<?php if ($level >= 2){ ?>
    <?= $form->field($model, $cityName)->dropDownList(Yii::$app->services->country->getProvinceMapByPid($model->$countryName, 2), [
            'prompt' => '-- 请选择省份 --',
            'onchange' => 'widget_country(this,2,"' . Html::getInputId($model, $cityName) . '","' . Html::getInputId($model, $cityName) . '")',
        ]); ?>
<?php }?>
<?php if ($level >= 3){ ?>
    <?= $form->field($model, $cityName)->dropDownList(Yii::$app->services->country->getProvinceMapByPid($model->$cityName, 3), [
        'prompt' => '-- 请选择城市 --',
    ]) ?>
<?php }?>

<script>
    function widget_country(obj, type_id, cityId, cityId) {
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

                $("select#"+cityId+"").html(data);
            }
        });
    }
</script>
