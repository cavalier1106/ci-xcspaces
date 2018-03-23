
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-9 main">
                <ul class="nav nav-tabs nav-tabs-zen mt30">
                    <li class="<?php if($t=='new') echo "active"; ?>"><a href="/more/new">最新话题</a></li>
                    <li class="<?php if($t=='hot') echo "active"; ?>" ><a href="/more/hot">热门话题</a></li>
                </ul>
            <div class="stream-list question-stream">

                <?php foreach($titles as $k=>$v): ?>
                <section class="xcspace-topic-list-item">
                    <div class="blog-rank">
                        <div class="votes">
                            <a href="/user/memberTopics/<?php echo $v['create_userid']; ?>">
                                <img class="avatar-24 mr10" src="/images/<?php echo $v['avatar']; ?>" alt="<?php echo $v['create_username']; ?>">
                            </a>
                        </div>
                        <div class="views hidden-xs">
                            <?php echo getClickUnit((int)$v['clicks']); ?><small>阅读</small>
                        </div>
                    </div>
                    <div class="summary">
                        <h2 class="title"><a href="/detail/<?php echo $v['hashId']; ?>" <?php if($v['topic_type']==2){ ?> target="_blank" <?php } ?> ><?php echo strCut($v['title'], 200); ?></a></h2>               
                        <p class="excerpt wordbreak hidden-xs"><?php $ct = strCut($v['content'], 150);if($ct){ echo $ct; }else{ echo '...';} ?></p>
                        <ul class="author list-inline">
                            <li class="pull-right" title="">
                                <?php if($v['topic_type']==1){ ?>
                                    <?php if( $v['reply_counts'] > 0 ){ ?>
                                        <i class="fa fa-commenting"></i> <?php echo $v['reply_counts']; ?>
                                    <?php }else{ ?>
                                        <i class="fa fa-commenting-o"></i> 0
                                    <?php } ?>
                                <?php }else{ ?>
                                    <i class="fa fa-arrow-circle-o-right"></i>
                                <?php } ?>
                            </li>
                            <li>
                            <a href="./go/<?php echo $v['tab_name']; ?>"><?php echo $v['n_name']; ?></a>
                            <span class="split"></span>
                            <a href="/user/memberTopics/<?php echo $v['create_userid']; ?>"><?php echo $v['create_username']; ?></a>
                            创建于 
                            <?php echo xcspaces_format_date( $v['time'] ); ?>
                            </li>
                        </ul>
                    </div>
                </section>
                <?php endforeach; ?>

            </div>

            <?php if( $page ): ?>
            <div class="text-center">
                <ul class="pagination">
                    <?php echo $page;?>
                </ul>
            </div>
            <?php endif; ?>

            </div>
            <!-- /.main -->
            <div class="col-xs-12 col-md-3 side mt30">
                
                <div class="sfad-sidebar lfs-img">
                    <div class="sfad-item">
                        <a href="<?php echo $ads['logo_href']; ?>" target="_blank">
                        <img src="./images/<?php echo $ads['logo_path']; ?>" border="0" alt="阿里云" class="img-responsive" >
                        </a>
                    </div>
                </div>

                <div class="xcspace-sidebar-ranking">
                    <?php include($path . "/set-topic-rank.php"); ?>
                </div>

                <div class="widget-box">
                   <?php include($path . "/set-xcSpaces-jiathis.php"); ?>
                </div>
                
            </div>
        </div>
    </div>
</div>