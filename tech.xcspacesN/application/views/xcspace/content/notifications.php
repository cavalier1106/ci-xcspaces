
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
                    <h4 class="xcspace-profile-heading">
                        <span>系统提醒</span>
                        <span class="pull-right">总数&nbsp;<?php echo $notifications_replys_count;?></span>
                    </h4>

                    <?php foreach($datas as $k => $v): ?>
                    <section class="xcspace-topic-list-item">
                    <div class="summary">
                        <ul class="author list-inline">
                            <li class="pull-right" title="">
                             <?php echo date('Y年m月d日', $v['time']);?>
                            </li>
                            <li>
                            <?php echo $v['title'];?>
                            </li>
                        </ul>
                        <h2 class="reply-title"><?php echo $v['content'];?></h2>
                    </div>
                    </section>
                    <?php endforeach; ?>

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