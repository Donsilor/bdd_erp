<div class="row" style="padding:0">
	<div class="col-xs-12">
          <div class="box">
            <div class=" table-responsive" >
                <table class="table table-hover">
                    <thead>
                    	<tr><th>物流时间</th><th>内容</th></tr>
                    </thead>                    
                    <tbody>
                        <?php foreach ($logistics->list ?? [] as $log) {?>
                    	<tr>
                    		<td><?= $log['datetime'] ?></td>
                    		<td><?= $log['remark'] ?></td>              	
                    	</tr>
                    	<?php }?>
                    </tbody>
                </table>                
            </div>
         </div>
      </div>
 </div>
