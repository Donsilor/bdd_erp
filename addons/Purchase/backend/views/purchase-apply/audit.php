<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Style\common\enums\AttrModuleEnum;
use addons\Style\common\enums\JintuoTypeEnum;

$this->title = '审批流程';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  .time-line li div.mbox{
      border: 1px solid #f0f0f0;
      margin-left: 20px;
      line-height: 25px;
      padding: 10px;
      min-height: 60px;
  }
  .time-line li div.mbox .left{
      float: left;
  }
  .time-line li div.mbox .right{
      float: right;
  }
  .time-line li div.mbox .clear{
      clear: both;
  }
  .time-line li.grey div{
    color: grey;
  }



</style>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">基本信息</h4>
            </div>
            <div class="box-body">
                <div class="col-md-12 changelog-info">
                    <ul class="time-line">
                        <?php
                         $user_id = \Yii::$app->user->identity->getId();
                         foreach ($flow_detail as $flow){
                             $flow_current = $current_detail_id == '' || $flow->id == $current_detail_id;
                             if(in_array($user_id, $current_users_arr) && $user_id === $flow->user_id && $flow_current){
                        ?>
                        <li>
                            <div class="mbox">
                                <div>
                                    <div class="left">审&nbsp;&nbsp;核&nbsp;&nbsp;人:  <?= $flow->member->username ?? ''?></div>
                                </div>
                                <div class="clear">
                                    <div class="left">审核状态：</div><div class="left"><?= $form->field($model, 'audit_status')->radioList(\common\enums\AuditStatusEnum::getAuditMap())->label(false); ?></div>
                                </div>
                                <div class="clear">
                                    <div class="left">备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：</div><div class="left"><?= $form->field($model, 'audit_remark')->textArea(['cols'=>60])->label(false); ?></div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </li>
                         <?php }else{ ?>
                            <li class="grey">
                                <div class="mbox">
                                    <div class="one">
                                        <div class="left">审&nbsp;&nbsp;核&nbsp;&nbsp;人:  <?= $flow->member->username ?? ''?></div>
                                        <?php if($flow->audit_status == \common\enums\AuditStatusEnum::PASS){ ?>
                                            <div class="right">审核时间：<?= \Yii::$app->formatter->asDatetime($flow->audit_time) ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="clear">
                                        审核状态：<?= \common\enums\AuditStatusEnum::getValue($flow->audit_status);?>
                                    </div>
                                    <div>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：<?= $flow->audit_remark?></div>
                                </div>
                            </li>
                         <?php } ?>
                       <?php } ?>



                    </ul>
                    <!-- /.widget-user -->
                </div>
            </div>
            <?php
                if(in_array($user_id, $current_users_arr)){
            ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
            <?php } ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

