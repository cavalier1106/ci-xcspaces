<div class="widget-box">
    <h2 class="widget-box-space">
    话题排行榜
    <span class="userstab text-muted pull-right ">
    <a href="/more">更多</a>
    </span>
    </h2>
    <ol id="usersDaily" class="widget-top10">
        <?php foreach($hot_titles as $k => $v): ?>
            <li class="text-muted">
            <a href="/user/memberTopics/<?php echo $v['uid']; ?>">
            <img class="avatar-24" src="/images/<?php echo $v['avatar']; ?>">
            </a>
            <!--span class="text-muted">+100</span-->
            <a href="/detail/<?php echo $v['hashId']; ?>" <?php if($v['topic_type']==2){ ?> target="_blank" <?php } ?> class="ellipsis">
            <?php echo strCut($v['title'],90) ?>
            </a>
            </li>
        <?php endforeach; ?>
    </ol>
</div>