
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
        <link href="/backend/resources/css/print.css" rel="stylesheet">
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
        <style media="print">
            .Noprint {   DISPLAY:   none;}
            .PageNext {   PAGE-BREAK-AFTER:   always   }
        </style>
	</head>
	<body>
		<div class="container" id="wdf">
			<!--startprint1-->
			<div class="title"><?= Yii::$app->formatter->asDatetime(time(),'Y年M月') ?>采购订单表</div>
			
			<!-- 基础信息 -->
			<div class="order-info">
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">采购订单：</div>
						<div class="child-val fl"><?= $model->purchase_sn ?></div>
					</div>
					
					<div class="child fl clf">
						<div class="child-attr fl">采购下单员：</div>
						<div class="child-val fl"><?= $model->creator->username ?? '' ?></div>
					</div>
				</div>
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">委托供应商：</div>
						<div class="child-val fl"><?= $model->supplier->supplier_name ?? '' ?></div>
					</div>
				</div>
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">供应商地址：</div>
						<div class="child-val fl"><?= $model->supplier->address ?? '' ?> </div>
					</div>
					
					<div class="child fl clf">
						<div class="child-attr fl">联系方式：</div>
						<div class="child-val fl"><?= $model->supplier->telephone ?? $model->supplier->mobile ?? '' ?></div>
					</div>
					
					<div class="fr settle-accounts">
						结价:按款*件数+改图费
					</div>
				</div>
				<div class="list bold">
					<span>订单类型：</span>
					<span><?= \addons\Purchase\common\enums\OrderTypeEnum::getValue($model->order_type) ?></span>

				</div>
			</div>
			
			<!-- 订单列表 -->
			<table class="table" border="1" cellspacing="0" cellpadding="0" width="100%" >
				<!-- 列表头部 -->
				<tr class="t-head">
					<td rowspan="2">
						<div>序号</div>
					</td>
					<td rowspan="2">
						<div>图片</div>
					</td>
					<td rowspan="2">
						<div>款号</div>
					</td>
					<td rowspan="2">
						<div>品类</div>
					</td>
					<td rowspan="2">
						<div>产品线</div>
					</td>
					<td rowspan="2">
						<div>货品名称</div>
					</td>
					<td rowspan="2">
						<div>件数</div>
					</td>
					<td rowspan="2">
						<div>材质</div>
					</td>
					<td rowspan="2">
						<div>货品外部颜色</div>
					</td>
					<td rowspan="2">
						<div>手寸</div>
					</td>
					<td rowspan="2">
						<div>成品尺寸</div>
					</td>
					<td class="bg-blue bold" colspan="7">
						<div>主石</div>
					</td>
					<td class="bg-blue bold" colspan="7">
						<div>副石</div>
					</td>
					<td rowspan="2">
						<div>石料信息</div>
					</td>
					<td rowspan="2">
						<div>单件连石重(g)</div>
					</td>
					<td rowspan="2">
						<div>连石总重(g)</div>
					</td>
					<td rowspan="2">
						<div>净重/单件(g)</div>
					</td>
					<td rowspan="2">
						<div>总净重(g)</div>
					</td>
					<td rowspan="2">
						<div>损耗</div>
					</td>
					<td rowspan="2">
						<div>银价</div>
					</td>
					<td rowspan="2">
						<div>单件银额</div>
					</td>
					<td rowspan="2">
						<div>金额</div>
					</td>
					<td rowspan="2">
						<div>配件信息</div>
					</td>
					<td rowspan="2">
						<div>工艺描述</div>
					</td>
					<td rowspan="2">
						<div>工费/件</div>
					</td>
					<td rowspan="2">
						<div>镶石费/件</div>
					</td>
					<td rowspan="2">
						<div>工费总额/件</div>
					</td>
					<td rowspan="2">
						<div>改图费</div>
					</td>
					<td rowspan="2">
						<div>喷蜡费</div>
					</td>
					<td rowspan="2">
						<div>单件额</div>
					</td>
					<td class="bg-orange" rowspan="2">
						<div>工厂总额</div>
					</td>
					<td class="bg-orange" rowspan="2">
						<div>公司成本总额</div>
					</td>
				</tr>
				<tr class="t-head bg-blue">
					<td>
						<div>石料名称【石头类型】</div>
					</td>
					<td>
						<div>石重ct</div>
					</td>
					<td>
						<div>数量(粒)</div>
					</td>
					<td>
						<div>石总数(粒）</div>
					</td>
					<td>
						<div>石总重ct</div>
					</td>
					<td>
						<div>石价【单价】</div>
					</td>
					<td>
						<div>金额</div>
					</td>
					<td>
						<div>石料名称【石头类型】</div>
					</td>
					<td>
						<div>石重ct</div>
					</td>
					<td>
						<div>数量(粒)</div>
					</td>
					<td>
						<div>总数量(粒）</div>
					</td>
					<td>
						<div>总重ct</div>
					</td>
					<td>
						<div>石价</div>
					</td>
					<td>
						<div>金额</div>
					</td>
				</tr>

				<!-- 列表内容 -->
                <?php
                  foreach ($lists as $key => $val){
                      $pagesize = 10;
                ?>
				<tr>
					<td>
						<div><?=$key+1 ?></div>
					</td>
					<td>
						<div><img src="<?= $val['goods_image'] ?>"/> </div>
					</td>
					<td>
						<div><?= $val['style_sn'] ?></div>
					</td>
					<td>
						<div><?= $val['style_cate_name'] ?></div>
					</td>
					<td>
						<div><?= $val['product_type_name'] ?></div>
					</td>
					<td>
						<div><?= $val['goods_name'] ?></div>
					</td>
					<td>
						<div><?= $val['goods_num'] ?></div>
					</td>
					<td>
						<div><?= $val['material'] ?></div>
					</td>
					<td>
						<div><?= $val['goods_color'] ?></div>
					</td>
					<td>
						<div><?= $val['finger'] ?></div>
					</td>
					<td>
						<div><?= $val['product_size'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_type'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_weight'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_num'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_num_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_weight_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_price'] ?></div>
					</td>
					<td>
						<div><?= $val['main_stone_price_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_type1'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_weight'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_num'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_num_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_weight_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_price1'] ?></div>
					</td>
					<td>
						<div><?= $val['second_stone_price_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['stone_info'] ?></div>
					</td>
					<td>
						<div><?= $val['single_stone_weight'] ?></div>
					</td>
					<td>
						<div><?= $val['single_stone_weight_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['gold_weight'] ?></div>
					</td>
					<td>
						<div><?= $val['gold_weight_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['gold_loss'] ?></div>
					</td>
					<td>
						<div><?= $val['gold_price'] ?></div>
					</td>
					<td>
						<div><?= $val['gold_cost_price'] ?></div>
					</td>
					<td>
						<div><?= $val['gold_amount'] ?></div>
					</td>
					<td>
						<div><?= $val['parts_info'] ?></div>
					</td>
					<td>
						<div><?= $val['face'] ?></div>
					</td>
					<td>
						<div><?= $val['jiagong_fee'] ?></div>
					</td>
					<td>
						<div><?= $val['xiangqian_fee'] ?></div>
					</td>
					<td>
						<div><?= $val['gong_fee'] ?></div>
					</td>
					<td>
						<div><?= $val['gaitu_fee'] ?></div>
					</td>
					<td>
						<div><?= $val['penla_fee'] ?></div>
					</td>
					<td>
						<div><?= $val['unit_cost_price'] ?></div>
					</td>
					<td>
						<div><?= $val['factory_cost_price_sum'] ?></div>
					</td>
					<td>
						<div><?= $val['company_unit_cost_sum'] ?></div>
					</td>
				</tr>
                <?php if(($key + 1) % $pagesize == 0){?>
                </table>
                <div class="PageNext"></div>
                <table class="table" border="1" cellspacing="0" cellpadding="0" width="100%" >
                    <tr class="t-head">
                        <td rowspan="2">
                            <div>序号</div>
                        </td>
                        <td rowspan="2">
                            <div>图片</div>
                        </td>
                        <td rowspan="2">
                            <div>款号</div>
                        </td>
                        <td rowspan="2">
                            <div>品类</div>
                        </td>
                        <td rowspan="2">
                            <div>产品线</div>
                        </td>
                        <td rowspan="2">
                            <div>货品名称</div>
                        </td>
                        <td rowspan="2">
                            <div>件数</div>
                        </td>
                        <td rowspan="2">
                            <div>材质</div>
                        </td>
                        <td rowspan="2">
                            <div>货品外部颜色</div>
                        </td>
                        <td rowspan="2">
                            <div>手寸</div>
                        </td>
                        <td rowspan="2">
                            <div>成品尺寸</div>
                        </td>
                        <td class="bg-blue bold" colspan="7">
                            <div>主石</div>
                        </td>
                        <td class="bg-blue bold" colspan="7">
                            <div>副石</div>
                        </td>
                        <td rowspan="2">
                            <div>石料信息</div>
                        </td>
                        <td rowspan="2">
                            <div>单件连石重(g)</div>
                        </td>
                        <td rowspan="2">
                            <div>连石总重(g)</div>
                        </td>
                        <td rowspan="2">
                            <div>净重/单件(g)</div>
                        </td>
                        <td rowspan="2">
                            <div>总净重(g)</div>
                        </td>
                        <td rowspan="2">
                            <div>损耗</div>
                        </td>
                        <td rowspan="2">
                            <div>银价</div>
                        </td>
                        <td rowspan="2">
                            <div>单件银额</div>
                        </td>
                        <td rowspan="2">
                            <div>金额</div>
                        </td>
                        <td rowspan="2">
                            <div>配件信息</div>
                        </td>
                        <td rowspan="2">
                            <div>工艺描述</div>
                        </td>
                        <td rowspan="2">
                            <div>工费/件</div>
                        </td>
                        <td rowspan="2">
                            <div>镶石费/件</div>
                        </td>
                        <td rowspan="2">
                            <div>工费总额/件</div>
                        </td>
                        <td rowspan="2">
                            <div>改图费</div>
                        </td>
                        <td rowspan="2">
                            <div>喷蜡费</div>
                        </td>
                        <td rowspan="2">
                            <div>单件额</div>
                        </td>
                        <td class="bg-orange" rowspan="2">
                            <div>工厂总额</div>
                        </td>
                        <td class="bg-orange" rowspan="2">
                            <div>公司成本总额</div>
                        </td>
                    </tr>
                    <tr class="t-head bg-blue">
                        <td>
                            <div>石料名称【石头类型】</div>
                        </td>
                        <td>
                            <div>石重ct</div>
                        </td>
                        <td>
                            <div>数量(粒)</div>
                        </td>
                        <td>
                            <div>石总数(粒）</div>
                        </td>
                        <td>
                            <div>石总重ct</div>
                        </td>
                        <td>
                            <div>石价【单价】</div>
                        </td>
                        <td>
                            <div>金额</div>
                        </td>
                        <td>
                            <div>石料名称【石头类型】</div>
                        </td>
                        <td>
                            <div>石重ct</div>
                        </td>
                        <td>
                            <div>数量(粒)</div>
                        </td>
                        <td>
                            <div>总数量(粒）</div>
                        </td>
                        <td>
                            <div>总重ct</div>
                        </td>
                        <td>
                            <div>石价【单价】</div>
                        </td>
                        <td>
                            <div>金额</div>
                        </td>
                    </tr>

                <?php
                    }
                }
                ?>
                <tr>
                    <td colspan="3"><div>合计</div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div><?= $total['goods_num_count']?></div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div><?= $total['main_stone_weight_count']?></div></td>
                    <td><div><?= $total['main_stone_num_count']?></div></td>
                    <td><div><?= $total['main_stone_num_sum_count']?></div></td>
                    <td><div><?= $total['main_stone_weight_sum_count']?></div></td>
                    <td></td>
                    <td><div><?= $total['main_stone_price_sum_count']?></div></td>
                    <td></td>
                    <td><div><?= $total['second_stone_weight_count']?></div></td>
                    <td><div><?= $total['second_stone_num_count']?></div></td>
                    <td><div><?= $total['second_stone_num_sum_count']?></div></td>
                    <td><div><?= $total['second_stone_weight_sum_count']?></div></td>
                    <td></td>
                    <td><div><?= $total['second_stone_price_sum_count']?></div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div><?= $total['jiagong_fee_count']?></div></td>
                    <td><div><?= $total['xiangqian_fee_count']?></div></td>
                    <td><div><?= $total['gong_fee_count']?></div></td>
                    <td><div><?= $total['gaitu_fee_count']?></div></td>
                    <td><div><?= $total['penla_fee_count']?></div></td>
                    <td><div><?= $total['unit_cost_price_count']?></div></td>
                    <td><div><?= $total['factory_cost_price_sum_count']?></div></td>
                    <td><div><?= $total['company_unit_cost_sum_count']?></div></td>
                </tr>
			</table>
			
			<!--endprint1-->
		</div>
        <div class="text-center">
            <!-- 打印按钮 -->
            <button type="button" class="btn-ms" target="_blank" onclick="preview(1)">打印</button>
        </div>

	</body>
</html>
