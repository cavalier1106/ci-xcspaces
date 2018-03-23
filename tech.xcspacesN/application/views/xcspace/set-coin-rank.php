<div class="widget-box">
    <h2 class="widget-box-space">
    威望排行榜
    <span class="userstab text-muted pull-right ">
    <!-- <a href="javascript:;">更多</a> -->
    </span>
    </h2>
    <ol id="usersDaily" class="widget-top10">
        <?php foreach($UserCoinRank as $k => $v): ?>
            <li class="text-muted">
            <a href="/user/memberTopics/<?php echo $v['id']; ?>">
            <img class="avatar-24" src="/images/<?php if(!empty($v['imgNormal'])){echo 'avatar/' . $v['id'].'/'.$v['id'].'_normal.png';}else{echo $v['img_path'];} ?>">
            </a>
            <!--span class="text-muted">+100</span-->
            <a href="/user/memberTopics/<?php echo $v['id']; ?>" class="ellipsis">
            <?php if($v['webname']){echo $v['webname'];}else{echo $v['name'];} ?>
            </a>
            <span class="text-muted pull-right"><?php echo getCoinUnit($v['coin']); ?></span>
            </li>
        <?php endforeach; ?>
    </ol>
</div>