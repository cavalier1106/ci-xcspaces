
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div id="secondary" class="col-md-2">
                <?php include($path . "/set-userinfo-left-bar.php"); ?>
            </div>
            <!-- end #secondary -->
            <div id="main" class="settings col-md-10 form-horizontal">
                <form method="post" action="/user/avatar" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                    <h2 class="h3 mt30 post-title">头像修改</h2>
                    <div class="form-group mt30">
                        <label class="control-label col-sm-2">当前头像</label>
                        <div class="col-sm-8">
                            <img style="width: 73px" src="/images/<?php if(!empty($user_info['user_avatar'])){echo 'avatar/' . $user_info['id'].'/'.$user_info['id'].'_normal.png';}else{echo $user_info['img_path'];} ?>" class="avatar img-rounded img-reponsive" border="0" align="default">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">选择图片</label>
                        <div class="col-sm-8">
                            <input type="file" name="avatar" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2"></label>
                        <div class="col-sm-8">
                            支持 2MB 以内的 PNG / JPG / GIF 文件
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2"></label>
                        <div class="col-sm-8">
                            <input type="hidden" value="<?php echo $user_info['id']; ?>" name="uid">
                            <input type="submit" class="btn btn-info" value="开始上传">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>