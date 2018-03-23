<div class="wrap">
    <div class="container">

        <div class="bordert">
        
            <div class="col-md-4 col-sm-12 col-md-push-7 login-wrap mt30">
                <?php include($path . "/set-xcSpaces-wb-qq-login.php"); ?>
            </div>

            <div class="login-vline hidden-xs hidden-sm"></div>

            <div class="col-md-4 col-md-pull-3 col-sm-12 login-wrap mt30">
                <?php if( isset( $errMsg ) && !empty( $errMsg ) ): ?>
                <div class="side-ask alert alert-info">请解决以下问题然后再提交：
                    <ul>
                        <li class="errMsg" ><?php echo $errMsg['msg']; ?></li>
                    </ul>
                </div>
                <?php endif; ?>
                
                <h1 class="h4 text-muted login-title text-center">用户登录</h1>
                <form action="/loginreg/login" method="POST" role="form" class="mt30">
                    <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                    <div class="form-group">
                        <label class="control-label">用户名</label>
                        <input type="text" class="form-control" name="uname" value="<?php if(isset($post_data['uname'])) echo $post_data['uname'];?>" required placeholder="用户名">
                    </div>
                    <div class="form-group">
                        <label class="control-label">密码</label>
                        <input type="password" class="form-control" name="passwd" value="<?php if(isset($post_data['passwd'])) echo $post_data['passwd'];?>" required placeholder="密码">
                    </div>
                    <div class="form-group clearfix">
                        <div class="checkbox pull-left">
                            <!-- <a href="javascript:;" class="ml5">找回密码</a>&nbsp; -->
                            <a href="/loginreg/register" class="ml5">还未注册?</a>
                        </div>
                        <button type="submit" class="btn btn-info pull-right pl20 pr20" >登录</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>