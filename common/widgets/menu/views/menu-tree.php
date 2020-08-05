<?php

use common\helpers\Url;
use common\enums\StatusEnum;
?>
<?php 
$menuOpens = [];
foreach ($menus as $k=>$item) {     
    if(empty($menuOpens[$item['cate_id']]) || count($menuOpens[$item['cate_id']]) < 1) {
        $menuOpen = true;
        $menuOpens[$item['cate_id']][] = true;
    }else{
        $menuOpen = false;
    }
    ?>
    <li class="treeview hide<?= $menuOpen ?' menu-open':''?> rfLeftMenu <?= (isset($item['cate']['is_default_show']) && $item['cate']['is_default_show'] == StatusEnum::ENABLED) ? 'is_default_show' : ''; ?> rfLeftMenu-<?= $item['cate_id']; ?>">
        <?php if (!empty($item['-'])) { ?>
            <a href="#">
                <i class="fa <?= $level == 1 ? $item['icon'] : ''; ?> rf-i"></i> <span><?= $item['title']; ?></span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu" <?= $menuOpen ?'style="display: block"':''?>>
                <?= $this->render('menu-tree', [
                    'menus' => $item['-'],
                    'level' => $level + 1,
                ]) ?>
            </ul>
        <?php } else { ?>
            <a class="J_menuItem" href="<?= $item['fullUrl'] == '#' ? '' : Url::to($item['fullUrl']); ?>">
                <i class="fa <?= $level == 1 ? $item['icon'] : ''; ?> rf-i"></i>
                <span><?= $item['title']; ?></span>
            </a>
        <?php } ?>
    </li>
<?php } ?>