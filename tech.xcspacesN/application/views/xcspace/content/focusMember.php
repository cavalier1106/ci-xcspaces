
<?php include($path . "/topicNodes.php"); ?>

<div class="profile">
    <header class="xcspace-profile-com-heading">
    <div class="container">
        <div class="row">
            <?php include($path . "/set-usercenter-top.php"); ?>
        </div>
    </div>
    </header>
    <div class="wrap mt30">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                   <?php include($path . "/set-usercenter-left-bar.php"); ?>
                </div>
                <div class="col-md-10 profile-mine">
                    <h4 class="xcspace-profile-heading"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{ echo "他";} ?>的关注用户</span><span class="pull-right">总数&nbsp;<?php echo $focus_member_count;?></span>
                    </h4>
                    <div class="board pt20">
                        <ul class="list-unstyled">
                            <?php foreach($datas as $k => $v): ?>
                                <li class="mb20 col-md-3 focusMember">
                                    <a href="/user/memberTopics/<?php echo $v['id'];?>">
                                        <img class="avatar-24" src="/images/<?php echo $v['img_path'];?>" alt="<?php echo $v['name'];?>"/>
                                        <span class="ellipsis"><?php echo $v['name'];?></span>
                                    </a>
                                    <?php if(isset($user_info)): ?>
                                    <?php if($one_user_info['id']==$user_info['id']): ?>
                                    <a href="javascript:;" onclick="if (confirm('你确定要取消关注？')) { location.href = '/unfavorite/<?php echo $v['id'];?>?t=users_str'; }" >
                                    <span class="pull-right">取消关注</span>
                                    </a>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if( $page ): ?>
                    <div class="text-center">
                        <ul class="pagination">
                            <?php echo $page;?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>