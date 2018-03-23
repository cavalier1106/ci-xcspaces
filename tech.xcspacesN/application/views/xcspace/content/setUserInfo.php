
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div id="secondary" class="col-md-2">
                <?php include($path . "/set-userinfo-left-bar.php"); ?>
            </div>
            <!-- end #secondary -->
            <div id="main" class="settings col-md-10 form-horizontal">
                <h2 class="h3 mt30 post-title">个人资料</h2>
                <div class="row mt30">
                    <div class="col-md-4 col-md-push-8">
                        
                        <?php include($path . "/set-xcSpace-notepad.php"); ?>

                        <div class="sfad-sidebar lfs-img">
                            <?php foreach($ads_list as $k => $v): ?>
                                <div class="sfad-item">
                                    <a href="<?php echo $v['logo_href']; ?>" target="_blank">
                                    <img src="/images/<?php echo $v['logo_path']; ?>" border="0" width="100%" alt="<?php echo $v['title']; ?>">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                    <div class="col-md-8 col-md-pull-4">
                        <form action="/user/setUserInfo" method="post">
                            <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                            <div class="form-group">
                                <label for="name" class="required control-label col-sm-3">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" maxlength="32" placeholder="用户名唯一" class="form-control" value="<?php echo $user_setting['name'];?>" disabled >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nickname" class="required control-label col-sm-3">真实名</label>
                                <div class="col-sm-9">
                                    <input name="nickname" type="text" maxlength="32" placeholder="真实名" class="form-control" value="<?php echo $user_setting['nickname'];?>" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="webname" class="required control-label col-sm-3">网名</label>
                                <div class="col-sm-9">
                                    <input name="webname" type="text" maxlength="32" placeholder="网名" class="form-control" value="<?php echo $user_setting['webname'];?>" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="required control-label col-sm-3">手机</label>
                                <div class="col-sm-9">
                                    <input name="phone" type="text" maxlength="15" placeholder="手机" class="form-control" value="<?php echo $user_setting['phone'];?>" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-mail" class="required control-label col-sm-3">Email 地址</label>
                                <div class="col-sm-9">
                                    <input name="email" type="email" placeholder="hello@segmentfault.com" class="form-control mono" autocomplate="false" required="" value="<?php echo $user_setting['email'];?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="QQ" class="control-label col-sm-3">QQ</label>
                                <div class="col-sm-9">
                                    <input name="qq" type="text" maxlength="32" placeholder="QQ" class="form-control" value="<?php echo $user_setting['qq'];?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-website" class="control-label col-sm-3">个性网址</label>
                                <div class="col-sm-9">
                                    <input name="website" type="text" placeholder="个性网址" data-do="checkUserSlug" class="form-control setting-slug" value="<?php echo $user_setting['website'];?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-company" class="control-label col-sm-3">所在公司</label>
                                <div class="col-sm-9">
                                    <input name="company" type="text" placeholder="所在公司" data-do="checkUserSlug" class="form-control setting-slug" value="<?php echo $user_setting['company'];?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-career" class="control-label col-sm-3">工作职位</label>
                                <div class="col-sm-9">
                                    <input name="career" type="text" placeholder="工作职位" data-do="checkUserSlug" class="form-control setting-slug" value="<?php echo $user_setting['career'];?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-city" class="control-label col-sm-3">现居城市</label>
                                <div class="col-sm-9">
                                    <input name="city" type="text" placeholder="现居城市" data-do="checkUserSlug" class="form-control setting-slug" value="<?php echo $user_setting['city'];?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-address" class="control-label col-sm-3">通讯地址</label>
                                <div class="col-sm-9">
                                    <input name="address" type="text" placeholder="通讯地址" data-do="checkUserSlug" class="form-control setting-slug" value="<?php echo $user_setting['address'];?>" >
                                </div>
                            </div>

                            <!--div class="form-group">
                                <label class="control-label col-sm-3">性别</label>
                                <div class="col-sm-9">
                                    <label class="radio-inline"><input name="gender" type="radio" id="sex-none" value="0"> 保密</label><label class="radio-inline"><input name="gender" type="radio" id="sex-male" value="1" checked=""> 男</label><label class="radio-inline"><input name="gender" type="radio" id="sex-female" value="2"> 女</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setting-birthday" class="control-label col-sm-3">生日</label>
                                <div class="col-sm-9">
                                    <input name="birthday" id="setting-birthday" type="text" placeholder="格式 YYYY-MM-DD" value="" class="form-control">
                                </div>
                            </div-->
                            
                            <div class="form-group">
                                <label for="setting-description" class="control-label col-sm-3">自我简介</label>
                                <div class="col-sm-9">
                                    <textarea name="person_bio" id="setting-description" class="form-control mono" rows="6"><?php echo $user_setting['person_bio'];?></textarea>
                                </div>
                            </div>
                            <div class="form-action row">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button class="btn btn-xl btn-info hidden-xs" type="submit">提交</button>
                                    <button class="btn btn-xl btn-info btn-block visible-xs-block" type="submit">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>