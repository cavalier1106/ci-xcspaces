<div class="widget-box widget-none">
    <h2 class="widget-box-space">
    我的笔记
    <span class="userstab text-muted pull-right ">
    <a href="/more">更多</a>
    </span>
    </h2>
    <ol id="usersDaily" class="widget-top10">
        <?php foreach($notepad as $k => $v): ?>
            <li class="text-muted">
	            <a href="/noteDetail/<?php echo $v['hashId']; ?>" class="ellipsis">
	            	<?php echo strCut($v['title'], 90) ?>
	            </a>
            </li>
        <?php endforeach; ?>
        <?php if( !$notepad ): ?>
        <div class="widget-invite mt30 mb30 text-center">
            <p>
                <span class="text-muted">尚无笔记</span>
            </p>
        </div>
        <?php endif; ?>
    </ol>
</div>