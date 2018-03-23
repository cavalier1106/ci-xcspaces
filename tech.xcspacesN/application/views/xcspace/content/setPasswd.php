
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div id="secondary" class="col-md-2">
                <?php include($path . "/set-userinfo-left-bar.php"); ?>
            </div>
            <div id="main" class="settings col-md-10 form-horizontal">
                <form id="setting" action="/user/setpasswd" method="post">
                    <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                    <h2 class="h3 mt30 post-title">密码修改</h2>
                    <?php if( isset( $msg ) ): ?>
                    <div class="alert alert-warning" id="" ><?php echo $msg['msg'] ; ?></div>
                    <?php endif; ?>
                    <div class="form-group mt30">
                        <label for="oldpassword" class="required control-label col-sm-2">当前密码</label>
                        <div class="col-sm-8">
                            <input name="password" type="password" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="newpassword" class="required control-label col-sm-2">新密码</label>
                        <div class="col-sm-8">
                            <input name="newPassword" type="password" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="required control-label col-sm-2">确认新密码</label>
                        <div class="col-sm-8">
                            <input name="confirmPassword" type="password" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-action row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-xl btn-info">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>