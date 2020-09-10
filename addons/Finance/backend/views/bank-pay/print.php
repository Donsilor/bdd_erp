<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<style type="text/css">
			div,table{
				padding: 0;
				margin: 0;
			}

			img {
				width: 100%;
			}
			html{
				font-size: 14px;
			}
			
			table,tr,td{
				table-layout:fixed;
				box-sizing: border-box;
			}

			.container{
				width: 1000px;
				margin: 0 auto;
			}
			table{
				width: 100%;
				border-collapse: collapse;
			}

			td{
				padding: 0.2rem 20px;
				height: 3.5rem;
				text-align: left;
			}

			td div{
				/*overflow : hidden;*/
				/*display: -webkit-box;*/
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
			}

			.bold{
				text-align: center;
                font-weight: bold;
			}

			.height_4{
				height: 11rem;
			}
			.title,
			.title2{
				height: 5rem;
				line-height: 5rem;
				text-align: center;
				border: 1px solid #333;
				border-bottom: 0;
				margin: 0 auto;
				box-sizing: border-box;
				font-weight: bold;
				font-size: 20px;
				letter-spacing: 2px;
			}
			.title2{
				font-size: 16px;
				border-top: 0;
				height: 4rem;
				line-height: 4rem;
				font-weight: normal;
			}

			.table2 td{
				height: 5rem;
				text-align: center;
			}
			.gray{
				color: #ccc;
			}

			.flex{
				/* display: flex !important; */
				font-size: 0;
				padding: 0;
			}
			.flex div{
				display: inline-block;
				width: 33.33%;
				height: 100%;
				font-size: 16px;
				padding: 0 10px;
				box-sizing: border-box;
				border-right: 1px solid #333;
			}
			.flex div:last-child{
				border-right: 0;
			}
		</style>
		
		<script language="javascript">
			function preview(fang) {
				if (fang < 10) {
					bdhtml = window.document.body.innerHTML; //获取当前页的html代码
					sprnstr = "<!--startprint" + fang + "-->"; //设置打印开始区域
					eprnstr = "<!--endprint" + fang + "-->"; //设置打印结束区域
					prnhtml = bdhtml.substring(bdhtml.indexOf(sprnstr) + 18); //从开始代码向后取html
					prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr)); //从结束代码向前取html
					window.document.body.innerHTML = prnhtml;
					window.print();
					window.document.body.innerHTML = bdhtml;
				} else {
					window.print();
				}
			}
		</script>
	</head>
	<body>
		<!--startprint1-->
		<div class="container">
			<h2 class="title">银行支付申请单</h2>
			<table border="1" bordercolor="#333" cellspacing="0" cellpadding="0">
				<!-- <tr class="title"> -->
					<!-- <td colspan="4">银行支付申请单</td> -->
				<!-- </tr> -->
				<tr>
					<td width="15%" class="bold">
						<div>审批单号</div>
					</td>
					<td width="35%">
						<div><?= $model->finance_no?></div>
					</td>
					<td width="15%" class="bold">
						<div>申请单状态</div>
					</td>
					<td width="35%">
						<div><?= \addons\Finance\common\enums\FinanceStatusEnum::getValue($model->finance_status)?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>填单人</div>
					</td>
					<td>
						<div><?= $model->creator->username ?? '' ?></div>
					</td>
					<td class="bold">
						<div>款项所属项目</div>
					</td>
					<td>
						<div><?= \addons\Finance\common\enums\ProjectEnum::getValue($model->project_name)?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>填单时间</div>
					</td>
					<td>
						<div><?= Yii::$app->formatter->asDate($model->created_at) ?></div>
					</td>
					<td class="bold">
						<div>预算类型</div>
					</td>
					<td>
						<div><?= \addons\Finance\common\enums\BudgetTypeEnum::getValue($model->budget_type)?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>填单人所属部门</div>
					</td>
					<td>
						<div><?= $model->creator->department->name ?? '' ?></div>
					</td>
					<td class="bold">
						<div>单据所属部门</div>
					</td>
					<td>
						<div><?= $model->creator->department->name ?? '' ?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>货币单位</div>
					</td>
					<td>
						<div><?= $model->currency?></div>
					</td>
					<td class="bold">
						<div>预算所属年度</div>
					</td>
					<td>
						<div><?= $model->budget_year ?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>付款金额</div>
					</td>
					<td>
						<div><?= \common\helpers\StringHelper::smalltoBIG($model->pay_amount)?></div>
					</td>
					<td colspan="2">
						<div><?= $model->pay_amount?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>收款单位</div>
					</td>
					<td>
						<div><?= $model->payee_company ?></div>
					</td>
					<td class="bold">
						<div>账号</div>
					</td>
					<td>
						<div><?= $model->payee_account ?></div>
					</td>
				</tr>
				<tr>
					<td class="bold">
						<div>开户行</div>
					</td>
					<td colspan="3">
						<div><?= $model->payee_bank ?></div>
					</td>
				</tr>

				<tr>
					<td class="bold height_4">用途</td>
					<td colspan="3">
						<div><?= $model->usage ?></div>
					</td>
				</tr>
			</table>
			<div class="title2">审批意见栏</div>
			<table class="table2" border="1" bordercolor="#333" cellspacing="0" cellpadding="0">
                <?php
                 $li = ['部门负责人','部门分管高管','相关部门负责人','相关部门分管高管','财务部','财务部分管高管','董事长办公室','总经理','副董事长','财务部出纳'];
                 foreach ($flow_list as $k=> $flow_detail){
                ?>
				<tr>
                    <td width="15%" class="bold">
                        <div><?= $li[$k]?></div>
                    </td>
                    <td width="20%">
                        <div><?php
                            $det_name =  $flow_detail->member->department->name ?? '';
                            echo $det_name == '' ? '' : $det_name." - "
                            ?>

                            <?= $flow_detail->member->username ?? ''?></div>
                    </td>
					<td>
						<div><?= $flow_detail->audit_remark?></div>
					</td>

                    <td width="15%">
                        <div><?= Yii::$app->formatter->asDatetime($flow_detail->audit_time)?></div>
                    </td>
				</tr>
                <?php } ?>

			</table>
		</div>
		<!--endprint1-->
		
		<!-- 打印按钮 -->
		<input style="margin: 40px;" type='button' name='button_export' title='打印1' onclick=preview(1) value='打印1'>

	</body>
</html>
