<ul class="nav nav-pills nav-stacked xcspace-profile-nav">
<li class="<?php if( $content == 'memberTopics' ){ echo "active"; } ?>">
<a href="/user/memberTopics/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的话题
</span><span class="count"> </span>
</a>
</li>
<li class="<?php if( $content == 'memberReplys' ){ echo "active"; } ?>">
<a href="/user/memberReplys/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的回复
</span><span class="count"> </span>
</a>
</li>
<?php if($is_login): ?>
<?php if($one_user_info['id']==$user_info['id']): ?>
<li class="<?php if( $content == 'memberNotepad' ){ echo "active"; } ?>">
<a href="/user/memberNotepad/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的笔记
</span><span class="count"> </span>
</a>
</li>
<?php endif; ?>
<?php endif; ?>
<li class="<?php if( $content == 'collectionNodes' ){ echo "active"; } ?>">
<a href="/collectionNodes/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的收藏空间
</span><span class="count"> </span>
</a>
</li>
<li class="<?php if( $content == 'collectionTopics' ){ echo "active"; } ?>">
<a href="/collectionTopics/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的收藏话题
</span><span class="count"> </span>
</a>
</li>
<li class="<?php if( $content == 'focusMember' ){ echo "active"; } ?>">
<a href="/focusMember/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的关注用户
</span><span class="count"> </span>
</a>
</li>
<li class="<?php if( $content == 'focusMemberTopics' ){ echo "active"; } ?>">
<a href="/focusMemberTopics/<?php echo $one_user_info['id']; ?>"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{echo "他";} ?>的关注用户话题
</span><span class="count"> </span>
</a>
</li>

<li role="separator" class="divider"><a></a></li>

<li class="<?php if( $content == 'myCoin' ){ echo "active"; } ?>">
<a href="/coinRecord/myCoin/<?php echo $one_user_info['id']; ?>">
<span>威望记录</span>
</a>
</li>
<?php if(isset($user_info)):?>
	<?php if($one_user_info['id']==$user_info['id']):  ?>
	<li class="<?php if( $content == 'daily' ){ echo "active"; } ?>">
	<a href="/coinRecord/daily">
	<span>每日登录奖励</span>
	</a>
	</li>
	<li class="<?php if( $content == 'notifications' ){ echo "active"; } ?>">
	<a href="user/notifications?uid=<?php echo $user_info['id']; ?>">
	<span>系统提醒</span>
	</a>
	</li>
	<?php endif;?>
<?php endif;?>

</ul>