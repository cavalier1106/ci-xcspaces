
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
                    <h4 class="xcspace-profile-heading"><span>每日登录奖励</span></h4>
    
    <?php if( isset( $msg ) ){ ?>

    <div class="alert alert-info" ><?php echo $msg['msg']; ?></div>
    <input type="button" class="btn btn-info" value="查看我的威望" onclick="location.href = '/coinRecord/myCoin/<?php echo $user_info['id']; ?>'">

    <?php }else{ ?>
        <?php if( $is_get_daily_coin ){ ?>

    <div class="alert alert-info">领取每日登录奖励</div>
    <input type="button" class="btn btn-info" value="领取 5 威望" onclick="location.href = '/coinRecord/daily?uid=<?php echo $user_info['id']; ?>';">
    <?php }else{ ?>
    <div class="alert alert-info" >每日登录奖励已领取</div>
    <input type="button" class="btn btn-info" value="查看我的威望" onclick="location.href = '/coinRecord/myCoin/<?php echo $user_info['id']; ?>'">
         <?php } ?>

    <?php } ?>
    
                </div>
            </div>
        </div>
    </div>
</div>