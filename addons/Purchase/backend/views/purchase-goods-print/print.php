<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<style type="text/css">
			div,span,h2,img,ul,ol,li{
				padding: 0;
				margin: 0;
			}
			.fl {
				float: left;
			}
			
			.fr {
				float: right;
			}
			
			.clf::after {
				display: block;
				content: '.';
				opacity: 0;
				height: 0;
				visibility: hidden;
				clear: both;
			}
			
			ul,
			li {
				list-style: none;
			}
			h2{
				font-size: 100%;
			}
			img {
				width: 100%;
				/*height: 100%;*/
			}
			html{
				font-size: 14px;
			}
			
			table,tr,td{
				table-layout:fixed;
				box-sizing: border-box;
			}
			
			.container{
				width: 1310px;
				border: 1px solid #777;
				margin: 10px auto;
				padding: 10px;
			}
			
			.height-4rem{
				height: 4rem;
			}
			.height-3rem{
				height: 3rem;
			}
			.height-5rem{
				height: 5rem;
			}
			.blod{
				font-weight: bold;
			}
			
			.factory-info{
				width: 650px;
				/* border: 2px solid #ccc; */
			}
			.title{
				height: 4rem;
				width: 100%;
				background-color: #ccc;
				text-align: center;
				line-height: 4rem;
				/* border: 2px solid #ccc; */
				box-sizing: border-box;
				font-size: 1.8rem;
				color: #333;
			}
			table{
				width: 100%;
				border-collapse: collapse;
			}
			tr{
				height: 2rem;
			}
			td{
				padding: 0.2rem 6px;
				box-sizing: border-box;
			}
			.table1 tr td:nth-child(1),
			.table1 tr td:nth-child(3){
				font-weight: bold;
				text-align: left;
			}
			.table1 tr td:nth-child(2),
			.table1 tr td:nth-child(4){
				text-align: center;
			}
			
			.table2 tr td:nth-child(1){
				text-align: left;
				font-weight: bold;
			}
			.table2 tr td:nth-child(2){
				text-align: center;
			}
			.table2 tr:nth-child(1) td:nth-child(2){
				text-align: left;
				font-weight: bold;
			}
			.table2 tr:nth-child(1) td:nth-child(1){
				text-align: left;
				font-weight: bold;
				border: none;
			}
			.table2 tr:nth-child(1) td:nth-child(3){
				text-align: center;
			}
			
			.table2 tr:first-child td:first-child{
				padding: 4px;
				box-sizing: border-box;
			}
			
			.table3 td:first-child{
				font-weight: bold;
			}
			
			.goods-info{
				width: 650px;
				/* border: 2px solid #ccc; */
				margin-left: 10px;
			}
			
			.table4{
				margin-top: 4rem;
			}
			
			.table4 td{
				text-align: center;
			}
			
			.table4 tr:last-child td:nth-child(2n-1){
				font-weight: bold;
			}
			
			.table5{
				text-align: center;
			}
			
			.table6 td{
				height: 3rem;
			}
			
			.table6 td:first-child{
				text-align: center;
				font-weight: bold;
			}
			
			td>div{
				/* display: inline-block; */
				/* white-space: wrap; */
				/* word-break: break-all; */
				max-height: 2rem;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}
			.height-4rem td>div{
				max-height: 4rem;
				box-sizing: border-box;
				display: -webkit-box;
				-webkit-box-orient: vertical;
				-webkit-line-clamp: 2;
				overflow: hidden;
				white-space: inherit;
			}

            .btn-ms {
                display: inline-block;
                color: #fff;
                font-weight: 400;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                touch-action: manipulation;
                cursor: pointer;
                background-image: none;
                padding: 6px 12px;
                font-size: 14px;
                line-height: 1.42857143;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                -ms-touch-action: manipulation;
                user-select: none;
                margin-bottom: 3px !important;
                border-radius: 3px;
                -webkit-box-shadow: none;
                box-shadow: none;
                color: #fff;
                background-color: #0bb2d4;
                border-color: #0bb2d4;
                border: 1px solid transparent;



            }

            .text-center{
                padding: 20px 15px 20px 20px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
                border-bottom-right-radius: 3px;
                border-bottom-left-radius: 3px;
                border-top: 1px solid #f4f4f4;
                padding: 10px;
                background-color: #fff;
                text-align: center;
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
        <style media="print">
            .Noprint {   DISPLAY:   none;}
            .PageNext {   PAGE-BREAK-AFTER:   always   }
        </style>
	</head>
	<body>

       <?php
       foreach ($lists as $model){
       ?>
		<!--startprint1-->
		<div class="container clf">
			<!-- 左 -->
			<div class="factory-info fl">
				<h2 class="title">中央生产制造单【<?= $model->supplier_name?>】</h2>
				
				<table class="table1" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="0" width="100%">
					<tr class="height-4rem">
						<td width="14%">
							<div>采购组：</div>
						</td>
						<td width="52%">
							<div><?= $model->purchase_group?></div>
						</td>
						<td width="14%">
							<div>采购单号：</div>
						</td>
						<td width="20%">
							<div><?= $model->purchase_sn?></div>
						</td>
					</tr>
					<tr class="height-4rem">
						<td>
							<div>物料描述：</div>
						</td>
						<td>
							<div><?= $model->goods_name?></div>
						</td>
						<td>
							<div>发单时间：</div>
						</td>
						<td>
							<div><?= $model->created_at?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>处理次序：</div>
						</td>
						<td>
							<div><?= $model->processing_order?></div>
						</td>
						<td rowspan="2">
							<div>交货时间：</div>
						</td>
						<td rowspan="2">
							<div><?= $model->delivery_time?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>金料颜色：</div>
						</td>
						<td>
							<div><?= $model->material?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>件数：</div>
						</td>
						<td>
							<div><?= $model->goods_num?></div>
						</td>
						<td>
							<div>物料号：</div>
						</td>
						<td>
							<div><?= $model->item_number?></div>
						</td>
					</tr>
				</table>
				
				<table class="table2" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="0" width="100%">
					<tr class="height-4rem">
						<td rowspan="10" width="calc(54% + 2px)">
							<img src="<?= $model->image?>" alt="">
						</td>
						<td width="14%" height="">
							<div>工厂模号：</div>
						</td>
						<td width="20%">
							<div><?= $model->factory_model?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>单类：</div>
						</td>
						<td>
							<div><?= $model->cate?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>后加工：</div>
						</td>
						<td>
							<div><?= $model->processing?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>出数客户：</div>
						</td>
						<td>
							<div><?= $model->customers?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>项目号：</div>
						</td>
						<td>
							<div><?= $model->number?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>镶法：</div>
						</td>
						<td>
							<div><?= $model->mounting_method?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>圈口：</div>
						</td>
						<td>
							<div><?= $model->circle?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>可改蜡最大:</div>
						</td>
						<td>
							<div><?= $model->maximum?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>可改蜡最小:</div>
						</td>
						<td>
							<div><?= $model->minimum?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>重量：</div>
						</td>
						<td>
							<div><?= $model->weight?></div>
						</td>
					</tr>
				</table>
				
				<table class="table3" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="0" width="100%">
					<tr style="height: 5rem;">
						<td width="14%">
							<div>工艺描述：</div>
						</td>
						<td width="86%">
                            <?= $model->process_desc?>
						</td>
					</tr>
					<tr>
						<td>
							<div>特殊工艺：</div>
						</td>
						<td>
							<div><?= $model->special_process?></div>
						</td>
					</tr>
					<tr class="height-4rem">
						<td>
							<div>字印要求：</div>
						</td>
						<td>
							<div><?= $model->printing_req?></div>
						</td>
					</tr>
					<tr class="height-4rem">
						<td>
							<div>尺寸要求：</div>
						</td>
						<td>
							<div><?= $model->size_req?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>形式：</div>
						</td>
						<td>
							<div><?= $model->form?></div>
						</td>
					</tr>
					<tr class="height-2rem">
						<td>
							<div>配件要求：</div>
						</td>
						<td>
							<div><?= $model->accessories_req?></div>
						</td>
					</tr>
				</table>
			</div>
			
			<!-- 右 -->
			<div class="goods-info fl">
				<table class="table4" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="0" width="100%">
					<tr class="height-4rem blod">
						<td width="13%">
							<div>附件模号</div>
						</td>
						<td width="11%">
							<div>石类</div>
						</td>
						<td width="13%">
							<div>粒数</div>
						</td>
						<td width="11%">
							<div>分数</div>
						</td>
						<td width="20%">
							<div>主石优先次序</div>
						</td>
						<td width="20%">
							<div>镶口范围</div>
						</td>
						<td width="12%">
							<div>直径</div>
						</td>
					</tr>
					
					<tr>
						<td rowspan="2" class="blod">
							<div>主石</div>
						</td>
						<td style="height: 3rem;">
							<div><?= $model->main_stone_type?></div>
						</td>
						<td>
							<div><?= $model->main_stone_num?></div>
						</td>
						<td>
							<div><?= $model->dia_carat?></div>
						</td>
						<td>
							<div><?= $model->main_stone_priority?></div>
						</td>
						<td>
							<div><?= $model->main_socket_range?></div>
						</td>
						<td>
							<div><?= $model->main_diameter?></div>
						</td>
					</tr>
					
					<tr class="height-5rem">
						<td colspan="6">
                            <?= $model->main_stone_remark?>
						</td>
					</tr>
					
					<tr class="blod">
						<td>
							<div>附件模号</div>
						</td>
						<td>
							<div>石类</div>
						</td>
						<td>
							<div>粒数</div>
						</td>
						<td>
							<div>分数</div>
						</td>
						<td>
							<div>主石优先次序</div>
						</td>
						<td>
							<div>镶口范围</div>
						</td>
						<td>
							<div>直径</div>
						</td>
					</tr>
					
					<tr>
						<td rowspan="2" class="blod">
							<div>辅石</div>
						</td>
						<td style="height: 3rem;">
							<div><?= $model->side_stone1_type?></div>
						</td>
						<td>
							<div><?= $model->side_stone1_num?></div>
						</td>
						<td>
							<div><?= $model->side_stone1_weight?></div>
						</td>
						<td>
							<div><?= $model->vice_stone_priority?></div>
						</td>
						<td>
							<div><?= $model->vice_socket_range?></div>
						</td>
						<td>
							<div><?= $model->vice_diameter?></div>
						</td>
					</tr>
					
					<tr>
						<td colspan="6">
							<div><?= $model->vice_stone_remark?></div>
						</td>
					</tr>
					
					<tr>
						<td>
							<div>总数：</div>
						</td>
						<td>
							<div><?= $model->sum_num?></div>
						</td>
						<td>
							<div>主石形状：</div>
						</td>
						<td>
							<div><?= $model->main_stone_shape?></div>
						</td>
						<td>
							<div>配石重量区间：</div>
						</td>
						<td colspan="2">
							<div><?= $model->stone_weight_range?></div>
						</td>
					</tr>
				</table>
				
				<table class="table5" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="0" width="100%">
					<tr class="blod height-4rem">
						<td width="57.1%">
							<div>主石颜色净度优先次序</div>
						</td>
						<td>
							<div>配石要求备注</div>
						</td>
					</tr>
					<tr>
						<td style="height: 180px;">
							<div><?= $model->main_stone_spec?></div>
						</td>
						<td>
							<div><?= $model->accessories_remark?></div>
						</td>
					</tr>

				</table>
				
				<table class="table6" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td width="14.28%">
							<div>生产要求：</div>
						</td>
						<td>
							<div><?= $model->product_req?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>货品描述：</div>
						</td>
						<td>
							<div><?= $model->product_desc?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>订单类型：</div>
						</td>
						<td>
							<div><?= $model->order_type?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>配石要求：</div>
						</td>
						<td>
							<div><?= $model->with_stone_req?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>发单要求：</div>
						</td>
						<td>
							<div><?= $model->billing_req?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div>备注：</div>
						</td>
						<td>
							<div><?= $model->remark?></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<!--endprint1-->
       <div class="PageNext"></div>
       <?php } ?>
		
		<!-- 打印按钮 -->
        <div class="text-center">
            <!-- 打印按钮 -->
            <button type="button" class="btn-ms" target="_blank" onclick="preview(1)">打印</button>
        </div>

	</body>
</html>
