
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
                    <h4 class="xcspace-profile-heading"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{ echo "他";} ?>的回复</span><span class="pull-right">总数&nbsp;<?php echo $member_info_count;?></span>
                    </h4>
                    <?php foreach($datas as $k => $v): ?>
                    <section class="xcspace-topic-list-item">
                    <div class="summary">
                        <ul class="author list-inline">
                            <li class="pull-right" title="">
                             <?php echo date('Y年m月d日', $v['time']);?>
                            </li>
                            <li>
                            <a href="/user/memberTopics/<?php echo $v['create_userid'];?>">
                            <img src="/images/avatar/<?php echo $v['create_userid'].'/'.$v['create_userid'].'_normal.png';?>" class="img-circle" border="0" width='24' align="default">
                            </a>
                            回复了 <a href="/user/memberTopics/<?php echo $v['create_userid'];?>"><?php echo $v['create_username'];?></a> 创建的主题
                            <span class="chevron">›</span> 
                            <a href="/detail/<?php echo $v['hashId'];?>"><?php echo strCut($v['title'], 100);?></a>
                            </span>
                            </li>
                        </ul>
                        <h2 class="reply-title"><?php echo strCut($v['content'], 150);?></h2>
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