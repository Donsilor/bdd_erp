<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

?>
<style>

</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-bars"></i> <?= $model->title ?></h3>
            </div>
            <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="table-responsive">
                            <div class="work_content">
                                <?= nl2br($model->content) ?>
                            </div>

                        </div>


                    </div>
                </div>

            </div>

        </div>
    </div>


</div>
