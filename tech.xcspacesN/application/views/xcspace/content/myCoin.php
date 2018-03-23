
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
                    <h4 class="xcspace-profile-heading"><span>威望记录</span>
                    </h4>
                    
                    <ul class="profile-mine__content record-list">
                        <?php foreach($datas as $k => $v): ?>
                        <li class="">
                        <div class="row">
                            <div class="col-md-2">
                                <span class="badge <?php if( $v['coin']<0 ){ echo "red";}else{ echo "blue";}?>"><?php echo $v['coin'];?></span><span class="profile-mine__content--text"><?php echo $op_type[$v['op_type']];?></span>
                            </div>
                            <div class="col-md-8">
                                <?php echo $v['desc'] . ' ' . getCoinUnit($v['coin']) . '威望';?> 
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