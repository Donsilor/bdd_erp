<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Finance\common\enums\FinanceStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '银行支付单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">银行支付单详情 - <?php echo $model->finance_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content" >
        <div class="col-xs-12">
            <div class="box">
                <div class=" table-responsive" >
                    <div class="col-xs-6">
                        <table class="table table-hover">
                            <tr>
                                <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('finance_no') ?>：</td>
                                <td><?= $model->finance_no ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('oa_no') ?>：</td>
                                <td><?= $model->oa_no ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('apply_user') ?>：</td>
                                <td><?= $model->apply_user ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('dept_id') ?>：</td>
                                <td><?= $model->department->name ?? ''?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('project_name') ?>：</td>
                                <td><?= \addons\Finance\common\enums\ProjectEnum::getValue($model->project_name)?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('budget_year') ?>：</td>
                                <td><?= $model->budget_year ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('budget_type') ?>：</td>
                                <td><?= \addons\Finance\common\enums\BudgetTypeEnum::getValue($model->budget_type)?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_amount') ?>：</td>
                                <td><?= $model->pay_amount ;  ?> <?= $model->currency ;?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('payee_company') ?>：</td>
                                <td><?= $model->payee_company; ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('payee_account') ?>：</td>
                                <td><?= $model->payee_account?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('payee_bank') ?>：</td>
                                <td><?= $model->payee_bank  ?></td>
                            </tr>

                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('usage') ?>：</td>
                                <td><?= nl2br($model->usage) ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('finance_status') ?>：</td>
                                <td>
                                    <?php
                                    $audit_name_str = '';
                                    if($model->targetType && $model->finance_status == \common\enums\FlowStatusEnum::GO_ON){
                                        $audit_name = Yii::$app->services->flowType->getCurrentUsersName($model->targetType,$model->id);
                                        $audit_name_str = $audit_name ? "({$audit_name})" : "";
                                    }
                                    echo FinanceStatusEnum::getValue($model->finance_status).$audit_name_str;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                                <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                                <td><?= $model->auditor ? $model->auditor->username:''  ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                                <td><?= \Yii::$app->formatter->asDatetime($model->audit_time) ?></td>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                                <td><?= $model->audit_remark ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-xs-6">
                        <div class="box">
                            <h4>附件列表</h4>
                            <div>
                                <?php
                                $annex_file = !empty($model->annex_file)?explode(',', $model->annex_file):null;
                                if($annex_file){
                                    echo common\widgets\webuploader\Files::widget([
                                        'type'=>'files',
                                        'theme'=>'show',
                                        'value'=> $annex_file,
                                        'name'=>'format_video',
                                    ]);
                                }

                                ?>
                            </div>
                        </div>
                        <div class="box">
                            <h4>关联列表</h4>
                            <div>
                                <dl>
                                    <?php
                                     $flow_ids = explode('|',$model->flow_ids ) ?? [];
                                     $flow_list = Yii::$app->services->flow->getFlows($flow_ids);
                                     foreach ($flow_list as $flow){
                                    ?>
                                    <dd><?= $flow['flow_name']?> :  <?= Html::a($flow['target_no'], $flow['url'], ['style'=>"text-decoration:underline;color:#3c8dbc"])?></dd>
                                    <?php } ?>
                                </dl>
                                <p>
                                    <?php
                                    if($model->finance_status == FinanceStatusEnum::SAVE) {
                                        echo Html::create(['select-flow', 'id' => $model->id], '新增/编辑相关表单', [
                                            'class' => 'btn btn-primary btn-xs openIframe',
                                            'data-width' => '90%',
                                            'data-height' => '90%',
                                            'data-offset' => '20px',
                                        ]);
                                    }
                                    ?>


                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="box-footer text-center">
                    <?php
                        if($model->finance_status == FinanceStatusEnum::SAVE) {
                            echo Html::edit(['edit', 'id' => $model->id], '编辑', [
                                'class' => 'btn btn-primary btn-ms',
                            ]);
                        }
                    ?>

                    <?php
                    if($model->finance_status == FinanceStatusEnum::SAVE){
                        echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                            'class'=>'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                        ]);
                    }
                    ?>
                    <?php
                    if($model->targetType){
                        $isAudit = Yii::$app->services->flowType->isAudit($model->targetType,$model->id);
                    }else{
                        $isAudit = true;
                    }
                    if($model->finance_status == FinanceStatusEnum::PENDING && $isAudit){
                        echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                        ]);
                    }
                    ?>
                    <?php
                        if($model->finance_status == FinanceStatusEnum::CONFORMED){
                            echo Html::edit(['confirm','id'=>$model->id], '确认', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交确认", "确定确认吗？");return false;',
                            ]);
                        }
                    ?>
                    <?= Html::a('打印',['print','id'=>$model->id],[
                        'target'=>'_blank',
                        'class'=>'btn btn-info btn-ms',
                    ]); ?>
<!--                    --><?//= Html::button('导出', [
//                        'class'=>'btn btn-success btn-ms',
//                        'onclick' => 'batchExport()',
//                    ]);?>
                </div>
            </div>
        </div>

        <div id="flow">

        </div>
        <!-- box end -->
</div>
<!-- tab-content end -->
</div>
<script>
    function batchExport() {
        window.location.href = "<?= \common\helpers\Url::buildUrl('export',[],['ids'])?>?ids=<?php echo $model->id ?>";
    }
    $("#flow").load("<?= \common\helpers\Url::to(['../common/flow/audit-view','flow_type_id'=> $model->targetType,'target_id'=>$model->id])?>")

</script>