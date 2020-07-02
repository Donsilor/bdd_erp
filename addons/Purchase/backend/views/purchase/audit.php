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
  .time-line li.red1 div.mbox{
      border: 1px solid red;
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
                        <li class="red1">
                            <div class="mbox">
                                <div>
                                    <div class="left">审&nbsp;&nbsp;核&nbsp;&nbsp;人:  高朋</div>
                                    <div class="right">审核时间：2020-07-02 13:12:55</div>
                                </div>
                                <div class="clear">
                                    <div class="left">审核状态：</div><div class="left"><?= $form->field($model, 'audit_status')->radioList(\common\enums\AuditStatusEnum::getAuditMap())->label(false); ?></div>
                                </div>
                                <div class="clear">
                                    <div class="left">备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：</div><div class="left"><?= $form->field($model, 'audit_remark')->textArea()->label(false); ?></div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </li>
                        <li class="grey">
                            <div class="mbox">
                                <div class="one">
                                    <div class="left">审&nbsp;&nbsp;核&nbsp;&nbsp;人:  高朋</div>
                                    <div class="right">审核时间：2020-07-02 13:12:55</div>
                                </div>
                                <div class="clear">
                                    审核状态：未审核
                                </div>
                                <div>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：高朋高朋高朋高朋</div>
                            </div>
                        </li>
                        <li>
                            <div class="mbox">
                                <div class="one">
                                    <div class="left">审&nbsp;&nbsp;核&nbsp;&nbsp;人:  高朋</div>
                                    <div class="right">审核时间：2020-07-02 13:12:55</div>
                                </div>
                                <div class="clear">
                                    审核状态：未审核
                                </div>
                                <div>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：高朋高朋高朋高朋</div>
                            </div>
                        </li>



                    </ul>
                    <!-- /.widget-user -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

