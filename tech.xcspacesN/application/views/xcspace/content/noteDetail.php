
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container mt30">
        <div class="row">
            <div class="col-xs-12 col-md-9 main">
                <a class="lfs-member-icon" href="/user/memberTopics/<?php echo $one_notepad['u_id'];?>">
                    <img class="avatar-32" src="/images/<?php echo $one_notepad['u_avatar'];?>" alt="">
                </a>
                <div class="xcspaces-topicheader-info">
                    <h1 class="h3 xcspace-topic-title" id="topicTitle">
                        <a href="javascript:;"><?php echo $one_notepad['title'];?></a>
                    </h1>
                    <div class="question__author">
                        <a href="/user/memberTopics/<?php echo $one_notepad['u_id'];?>" class="mr5">
                        <strong><?php echo $one_notepad['u_name'];?></strong></a> · 
                        <?php echo xcspaces_format_date( $one_notepad['time'] ); ?> 
                    </div>
                </div>
                <div class="mt30 mb30 bb1" ></div>
                <div class="post-offset">
                    <div class="question fmt">
                        <?php echo nl2br( stripslashes( $one_notepad['content'] ) );?>
                    </div>
                    <!-- /.widget-comments -->
                </div>
                <!-- end .post-offset -->

            </div>
            <!-- /.main -->

            <div class="col-xs-12 col-md-3 side">

                <div class="xcspace-sidebar-ranking mb50">
                    <?php include($path . "/set-xcSpaces-jiathis.php"); ?>
                </div>

                <div class="xcspace-sidebar-ranking">
                    <?php include($path . "/set-topic-rank.php"); ?>
                </div>

                <div class="xcspace-sidebar-ranking mt30">
                    <?php include($path . "/set-coin-rank.php"); ?>
                </div>
                
                <!-- /.side -->
            </div>
        </div>
    </div>
</div>