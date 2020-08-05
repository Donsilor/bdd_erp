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

            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-user"></i> 审批流程</h3>
            </div>
            <div class="box-body">
                <div class="col-md-12 changelog-info">
                    <ul class="time-line">
                        <?php
                         foreach ($flow_detail as $flow){
                        ?>
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

                    </ul>
                    <!-- /.widget-user -->
                </div>
            </div>

        </div>
    </div>
</div>

