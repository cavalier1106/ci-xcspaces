
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
                    <h4 class="xcspace-profile-heading"><span><?php if(isset($user_info)){if($one_user_info['id']==$user_info['id']){ echo "我";}else{ echo "他"; }}else{ echo "他";} ?>的笔记</span><span class="pull-right">总数&nbsp;<?php echo $member_notepad_count;?></span>
                    </h4>
                    <ul class="profile-mine__content">

                        <?php foreach($datas as $k => $v): ?>
                            <li>
                                <div class="row">
                                    <?php if( $v['create_userid'] == $user_info['id'] ): ?>
                                        <div class="col-md-1">
                                            <!-- <span class="label label-warning ">
                                                <i class="fa fa-sticky-note-o"></i>
                                            </span> -->
                                            <?php if( $v['is_open'] == 1 ){ ?>
                                                <button type="button" class="opened<?php echo $v['nId'];?> btn btn-info" onclick="setNotepadIsOpen('<?php echo $v['nId'];?>')" >开&nbsp;&nbsp;&nbsp;&nbsp;放</button>
                                            <?php }else{ ?>
                                                <button type="button" class="open<?php echo $v['nId'];?> btn btn-default" onclick="setNotepadIsOpen('<?php echo $v['nId'];?>')">未开放</button>
                                            <?php } ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="<?php if( $v['create_userid'] == $user_info['id'] ){echo "col-md-9";}else{echo "col-md-10";} ?> profile-content-title-warp">
                                        <a class="xcspace-profile-content-title" href="/noteDetail/<?php echo $v['hashId'];?>"><?php echo $v['title'];?></a>
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

<script>

function setNotepadIsOpen( nId )
{
    var p = {
        'nId':nId, 
        '<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>', 
        'SESSIONID':'<?php echo session_id() ?>'
    };
    $.post('user/notepad_ajax',p,function(d){
        var d = $.parseJSON(d);
        console.log(d);
        if( d.is_open == 0 )
        {   
            alert('数据不存在/笔记本开放设置失败');
        }
        if( d.is_open == 1 )
        {   
            $('.open'+nId)
            .removeClass('btn-default'+' open'+nId)
            .addClass('btn-info'+' opened'+nId)
            .html('开&nbsp;&nbsp;&nbsp;&nbsp;放');
        }
        else if( d.is_open == 2 )
        {
            $('.opened'+nId)
            .removeClass('btn-info'+' opened'+nId)
            .addClass('btn-default'+' open'+nId)
            .html('未开放');
        }
    });
}

</script>