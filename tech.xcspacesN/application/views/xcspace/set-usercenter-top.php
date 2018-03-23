<div class="col-md-2">
    <a class="text-left hidden-xs" href="/user/memberTopics/<?php echo $one_user_info['id'];?>">
    <img class="avatar-128 xcspace-profile-heading-avatar" src="/images/<?php if(!empty($one_user_info['user_avatar'])){echo 'avatar/' . $one_user_info['id'].'/'.$one_user_info['id'].'_large.png';}else{echo $one_user_info['img_path'];} ?>" alt="papersnake">
    </a>
</div>
<div class="col-md-6">
    <h2 class="xcspace-profile-heading-name"><?php echo $one_user_info['name']; ?></h2>
    <div class="xcspace-profile-heading-desc js-show xcspace-profile-heading-avatar-desc-auto">
        <p>
            <?php echo $one_user_info['person_bio'];?>
        </p>
    </div>
    <div class="xcspace-profile-heading-social">
        <span class="xcspace-profile-heading-social-item">个人网站：
        <a class="xcspace-profile-heading-social-item-link" href="<?php if( $one_user_info['website'] == '~%20主人还没有填写博客地址%20~'){echo $one_user_info['website'];} else {echo 'javascript:;';}?>" target="_blank"><?php echo $one_user_info['website'];?></a>
        </span>
    </div>
</div>
<div class="col-md-4">
    <div class="mt10">
        <?php if( $is_login ): ?>
            <?php if( $one_user_info['id'] != $user_info['id']){ ?>
                <?php if( !$is_favorite ){ ?>
                <input type="button" value="加入关注" onclick="location.href = '/favorite/<?php echo $one_user_info['id'];?>?t=users_str';" class="btn mr10 btn-info">
                <?php }else{ ?>
                <input type="button" value="取消关注" onclick="location.href = '/unfavorite/<?php echo $one_user_info['id'];?>?t=users_str';" class="btn mr10 btn-default">
                <?php } ?>
            <?php }else{ ?>
                <div class="mt10">
                    <a class="btn mr10 btn-warning" href="/user/setUserInfo">编辑资料</a>
                    <!--a href="/u/cavalier/profile" class="btn btn-default"><i class="fa fa-user"></i> 创建档案</a-->
                </div>
            <?php } ?>
        <?php endif; ?>
    </div>
    <div class="xcspace-profile-heading-info row">
        <div class="col-md-3 col-xs-4">
            <a href="/coinRecord/myCoin/<?php echo $one_user_info['id']; ?>"><span class="h3"><?php echo getCoinUnit($one_user_info['coin']);?></span><span>威望值</span></a>
        </div>
         <div class="col-md-3 col-xs-4">
            <a href="/collectionNodes/<?php echo $one_user_info['id'];?>"><span class="h3"><?php echo $one_user_info['nodes_str_nums'];?></span><span>空间收藏</span></a>
        </div>
        <div class="col-xs-3">
            <a href="/collectionTopics/<?php echo $one_user_info['id'];?>"><span class="h3"><?php echo $one_user_info['titles_str_nums'];?></span><span>话题收藏</span></a>
        </div>
        <div class="col-xs-3">
            <a href="/focusMember/<?php echo $one_user_info['id'];?>"><span class="h3"><?php echo $one_user_info['users_str_nums'];?></span><span>特别关注</span></a>
        </div>
    </div>
</div>