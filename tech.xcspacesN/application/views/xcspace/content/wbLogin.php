<div class="wrap">
    <div class="container">

        <div class="bordert">

            <div class="col-md-6 col-md-offset-3 bg-white login-wrap mt30">
                
                <h1 class="h4 text-center text-muted login-title mb30">完善资料</h1>
                <p class="title-description">
                  您正在使用 <span class="icon-sn-weibo"></span>
                  <a href target="_blank">新浪微博 注册登录</a>
                </p>
                <div>
                    <?php if( isset($errMsg) ): ?>
                    <div class="alert alert-warning alert-dismissible mb10 mt10 fmt comment-helper" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="right:0;">
                            <span aria-hidden="true">×</span>
                        </button>
                        <?php echo $errMsg['msg'];?>
                    </div>
                    <?php endif; ?>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">绑定新账号</a></li>
                        <li role="presentation" class=""><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">绑定已有账号</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab1">
                            <form method="POST" role="form" action="/wbLogin/bindNoUser" >
                                <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                                <div class="form-group nameInput mt10">
                                    <label for="name" class="required">用户名</label>
                                    <input type="text" class="form-control" name="uname" value="" required="" placeholder="英文、数字、下划线6-20位字符组成">
                                </div>
                                <div class="form-group">
                                    <label for="mail" class="required">Email</label>
                                    <input type="email" class="form-control" name="email" value="" required="" placeholder="xcspaceswork@Gmail.com">
                                </div>
                                <div class="form-group nameInput">
                                    <label for="name" class="required">密码</label>
                                    <input type="password" class="form-control" name="passwd" value="" required="" placeholder="不少于 6 位">
                                </div>
                                <div class="form-group policy">
                                    <!-- <span class="left">同意并接受 <a target="_blank" href="/tos">《服务条款》</a></span> -->
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-block btn-lg">确定</button>
                                </div>
                            </form>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab2">
                            <form method="POST" action="/wbLogin/bindHaveUser" role="form">
                                <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                                <div class="form-group mt10">
                                    <label for="mail" class="required">Email</label>
                                    <input type="email" class="form-control" name="email" value="" required="" placeholder="xcspaceswork@Gmail.com">
                                </div>
                                <!-- <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input name="havePassword" id="havePassword" type="checkbox">我知道密码
                                        </label>
                                    </div>
                                </div> -->
                                <div class="form-group policy"></div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-block btn-lg">确定</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>