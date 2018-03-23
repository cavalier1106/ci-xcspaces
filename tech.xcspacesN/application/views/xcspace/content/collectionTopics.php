
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
                    <h4 class="xcspace-profile-heading"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{ echo "他";} ?>的收藏话题</span><span class="pull-right">总数&nbsp;<?php echo $topics_count;?></span>
                    </h4>
                    <ul class="profile-mine__content">

                        <?php foreach($datas as $k => $v): ?>
                            <li>
                                <div class="row">
                                    <div class="col-md-1">
                                        <span class="label label-warning ">
                                            <i class="fa fa-commenting-o"></i> <?php echo $v['reply_counts']; ?>
                                        </span>
                                    </div>
                                    <div class="col-md-9 profile-content-title-warp">
                                        <a class="xcspace-profile-content-title" href="/detail/<?php echo $v['hashId'];?>"><?php echo $v['title'];?></a>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="xcspace-profile-content-date"><?php echo date('Y年m月d日', $v['time']);?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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