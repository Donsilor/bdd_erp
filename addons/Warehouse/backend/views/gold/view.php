<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\GoldStatusEnum;

/* @var $this yii\web\View */
/* @var $model addons
/* @var $form yii\widgets\ActiveForm */

$this->title = '金料详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $model->gold_sn?> - <?= GoldStatusEnum::getValue($model->gold_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('gold_sn') ?>：</td>
                            <td><?= $model->gold_sn ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>