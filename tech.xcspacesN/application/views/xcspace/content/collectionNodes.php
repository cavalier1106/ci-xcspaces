
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
                    <h4 class="xcspace-profile-heading"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{ echo "他";} ?>的收藏空间</span><span class="pull-right">总数&nbsp;<?php echo $nodes_count;?></span>
                    </h4>
                    <div class="board pt20 ">
                        <ul class="list-unstyled">
                            <?php foreach($datas as $k => $v): ?>
                            <li class="tagPopup mb20 col-md-2">
                                <img src="./images/<?php echo $v['img_path'];?>" class="img-circle img-reponsive" width="24">
                                <a class="tag" href="/go/<?php echo $v['tab_name'];?>" >
                                <?php echo $v['name'];?>
                                </a>
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