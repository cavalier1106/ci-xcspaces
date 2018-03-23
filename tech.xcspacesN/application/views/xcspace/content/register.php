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

                <h1 class="h4 text-muted login-title text-center">创建账号</h1>
                <form action="/loginreg/register" method="POST" role="form" class="mt30">
                    <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                    <div class="form-group">
                        <label for="name" class="control-label">用户名</label>
                        <input type="text" class="form-control" name="uname" value="<?php if(isset($post_data['uname'])) echo $post_data['uname'];?>" required placeholder="英文、数字、下划线6-20位字符组成">
                    </div>
                    <div class="form-group">
                        <label for="mail" class="control-label">Email</label>
                        <input type="email" autocomplete="off" class="form-control register-mail" name="email" required placeholder="xcspaceswork@Gmail.com" value="<?php if(isset($post_data['email'])) echo $post_data['email'];?>" >
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">密码</label>
                        <input type="password" class="form-control" name="passwd" value="<?php if(isset($post_data['passwd'])) echo $post_data['passwd'];?>" required placeholder="不少于 6 位">
                    </div>
                    <div class="form-group">
                        <label class="required control-label">验证码</label>
                        <div style="background-image: url('/captcha/img'); background-repeat: no-repeat; background-position: center; width: 420px; height: 80px; border-radius: 3px; border: 1px solid #ccc;" class="img-responsive" id="LfsCaptcha" ></div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="code" value="" autocorrect="off" spellcheck="false" autocapitalize="off" placeholder="请输入验证码">
                    </div>
                    <div class="form-group clearfix">
                        <!-- <div class="checkbox pull-left">
                            同意并接受<a href="/tos" target="_blank">《服务条款》</a>
                        </div> -->
                        <button type="submit" class="btn btn-info btn-block pl20 pr20 hidden-xs">注册</button>
                        <button type="submit" class="btn btn-info btn-block pl20 pr20 visible-xs-block">注册</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>