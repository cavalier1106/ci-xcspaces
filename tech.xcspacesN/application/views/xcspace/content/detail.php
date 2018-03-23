
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container mt30">
        <div class="row">
            <div class="col-xs-12 col-md-9 main">
                <a class="lfs-member-icon" href="/user/memberTopics/<?php echo $one_topic['u_id'];?>">
                    <img class="avatar-32" src="/images/<?php echo $one_topic['u_avatar'];?>" alt="">
                </a>
                <div class="xcspaces-topicheader-info">
                    <h1 class="h3 xcspace-topic-title" id="topicTitle">
                        <a href="javascript:;"><?php echo $one_topic['title'];?></a>
                    </h1>
                    <div class="question__author">
                        <a href="/user/memberTopics/<?php echo $one_topic['u_id'];?>" class="mr5"><strong><?php echo $one_topic['u_name'];?></strong></a>&nbsp;•&nbsp;
                        <?php echo xcspaces_format_date( $one_topic['time'] ); ?> 
                        &nbsp;•&nbsp;
                        <?php echo getClickUnit((int)$one_topic['clicks']);?>  阅读
                        
                        <?php if( $is_login ): ?>

                        <span class="snow">&nbsp;•&nbsp;</span>
                        <?php if( $is_favorite ){ ?> 
                        <a href="/unfavorite/<?php echo $hashId; ?>?t=titles_str&create_userid=<?php echo $one_topic['u_id'];?>"><span class="glyphicon glyphicon-heart" aria-hidden="true"></span></a>
                        <?php }else{ ?>
                        <a href="/favorite/<?php echo $hashId; ?>?t=titles_str&create_userid=<?php echo $one_topic['u_id'];?>"><span class="glyphicon glyphicon-heart love-color" aria-hidden="true"></a>
                        <?php } ?>
                        
                        <!--自己发表的话题不能操作-->
                        <?php if( $one_topic['create_userid'] != $user_info['id'] ): ?>
                        <span class="snow">&nbsp;•&nbsp;</span>
                        <?php if( !$is_thanks_topic ){ ?>
                        <a href="javascript:;" onclick="thanks_topic('<?php echo $hashId; ?>','<?php echo $one_topic['u_id'];?>')">感谢</a>
                        <?php }else{ ?>
                        <span>已感谢</span>
                        <?php } ?>
                        <?php endif; ?>

                        <?php if( $is_current_user_create_topic && $user_info['coin'] >= config_item('userCoin') ): ?>
                        <!--如果是当前用户的话题才显示-->
                        <span class="snow">&nbsp;•&nbsp;</span>
                        <a href="javascript:;" onclick="if (confirm('你确定要删除当前话题？')) { location.href='/delTopic/<?php echo $one_topic['id'];?>'; }">删除</a>
                        <?php endif; ?>

                        <?php endif; ?>

                        <span class="hidden-xs">
                        </span>
                    </div>
                </div>
                <div class="mt30 mb30 bb1" ></div>
                <div class="post-offset">
                    <div class="question fmt">
                        <!-- <?php echo str_replace('<p><br></p>', '', nl2br( $one_topic['content'] ));?> -->
                        <?php echo nl2br( stripslashes( $one_topic['content'] ) );?>
                    </div>
                    <div class="row">
                        <div class="post-opt col-md-8">
                            <ul class="list-inline mb0">
                                <li></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-comments -->
                </div>
                <!-- end .post-offset -->
                <div class="widget-answers">

                <div class="btn-group pull-right zindex-pro" role="group">
                    <a href="<?php echo $REQUEST_URI;?>/?p=&type=coin" class="btn btn-default btn-xs <?php if($order_type=='coin') echo "active";?>">默认排序</a>
                    <a href="<?php echo $REQUEST_URI;?>/?p=&type=time" class="btn btn-default btn-xs <?php if($order_type=='time') echo "active";?>">时间排序</a>
                </div>

                <h2 class="title h4 mt30 mb20 post-title" id="answers-title"><?php echo count($topic_reply); ?> 个回复</h2>

<?php foreach($topic_reply as $k => $v): ?>
<article class="clearfix widget-reply-item">
    <div class="post-offset">
        <div class="answer fmt">
           <!-- <?php echo str_replace('<p><br></p>', '', nl2br( $v['content'] )); ?> -->
           <?php echo nl2br( $v['content'] );?>
        </div>
        <div class="row reply-info-row">
            <div class="post-opt col-md-8 col-sm-8 col-xs-10">
                <ul class="list-inline mb0">
                    <li> <?php echo xcspaces_format_date( $v['time'] ); ?> 回复 </li>
                    <?php if( $is_login ): ?>

                        <!--用户自己的回复不能操作-->
                        <?php if( $v['reply_userid'] != $user_info['id'] ): ?>

                        <li><a href="javascript:void(0);" onclick="replyOne('<?php echo $v['reply_username']; ?>');" class="comments"> 评论 </a></li>

                        <?php if( !is_thanks_reply_uid($v['thanks_reply_topic_str'], $user_info['id']) ){ ?>
                        <li><a href="javascript:void(0);" class="comments" onclick="thanks_reply_topic('<?php echo $hashId; ?>','<?php echo $v['reply_userid'];?>','<?php echo $v['id'];?>')"> 感谢 </a>
                        </li>
                        <?php }else{ ?>
                            <span>已感谢</span>
                        <?php } ?>

                        <?php endif; ?>

                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-2 col-sm-2 col-xs-2 xcspace-reply-info-author-avatar">
                <a class="mr10" href="/user/memberTopics/<?php echo $v['u_id']; ?>"><img class="avatar-32" src="/images/<?php echo $v['u_avatar']; ?>" alt=""></a>
            </div>
            <div class="col-md-2 col-sm-2 hidden-xs xcspace-reply-info-author">
                <div class=" xcspace-reply-info-author-warp">
                    <a class="xcspace-reply-info-author-name" title="chuyao" href="/user/memberTopics/<?php echo $v['u_id']; ?>"><?php echo $v['reply_username']; ?></a><span class="xcspace-reply-info-author-rank"><?php echo $v['coin']; ?> 威望</span>
                </div>
            </div>
        </div>
        <!-- /.widget-comments -->
    </div>
</article><!-- /article -->
<?php endforeach; ?>

<?php if( !$topic_reply ): ?>
<div class="widget-invite mt30 mb30 text-center">
<p>
<span class="text-muted">尚无回复</span>
</p>
</div>
<?php endif; ?>

                    <div class="text-center">
                    </div>
                </div>
                <!-- /.widget-answers -->
                <?php if( $is_login ): ?>
                <div class="alert alert-warning" id="error_message" ></div>
                <div style="padding: 20px 0px;">
                    <h4>话题回复</h4>
                    <form action="/detail/<?php echo $hashId; ?>" method="post" class="editor-wrap">
                        <div class="form-group">
                        <div class="editor">
                            <div class="form-group" id="content" ></div>
                        </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="SaveContent btn btn-info hidden-xs">回复</button>
                            <button type="button" class="SaveContent btn btn-info btn-block visible-xs-block">回复</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <!-- /.main -->

            <div class="col-xs-12 col-md-3 side">

                <!-- <div class="side-ask alert alert-warning">
                    <p>
                        SegmentFault 是一个专注于解决编程问题，提高开发技能的社区。
                    </p>
                    <div class="mt10 side-system-notice">
                        <i class="fa fa-bullhorn pull-left"></i><a class="side-system-notice--title" href="javascript:;">今天，你应该学会用 rm -rf /</a>
                    </div>
                </div> -->
                <!-- <?php include($path . "/set-xcSpace-right.php"); ?> -->

                <div class="xcspace-sidebar-ranking mb50">
                    <?php include($path . "/set-xcSpaces-jiathis.php"); ?>
                </div>

                <div class="sfad-sidebar lfs-img">

                    <?php foreach($ads_list as $k => $v): ?>
                        <div class="sep2" ></div>
                        <div class="sfad-item">
                            <a href="<?php echo $v['logo_href']; ?>" target="_blank">
                            <img src="/images/<?php echo $v['logo_path']; ?>" border="0" width="100%" alt="<?php echo $v['title']; ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="xcspace-sidebar-ranking">
                    <?php include($path . "/set-topic-rank.php"); ?>
                </div>
                
                <!-- /.side -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var editor;

    $('.SaveContent').click(function () {
        var content = editor.$txt.html();
        var f = true;
        if( content == "" )
        {
            f = false;
            alert('请输入内容!');
        }

        if( f )
        {
            var p = {'hashId':'<?php echo $hashId; ?>', 'uname':'<?php if(isset($uname)) echo $uname; ?>', 'content':content,'<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'};
            $.post('/detail_ajax',p,function(d){
                var data = $.parseJSON(d);
                console.log(data);
                $('#error_message').text(data.msg);
                $('#error_message').show();
                gotohref();
            });
        }
        
    });

    function wangEditor_init()
    {
        // 阻止输出log
        // wangEditor.config.printLog = false;
        
        editor = new wangEditor('content');

        // 自定义菜单
        editor.config.menus = [
            'source',
            'bold',
            'underline',
            'italic',
            'strikethrough',
            'eraser',
            'forecolor',
            'bgcolor',
            'link',
            'unlink',
            'undo',
            'redo',
            'fullscreen',
        ];

        editor.create();

        // 初始化编辑器的内容
        editor.$txt.html('');
    }

    <?php if($is_login): ?>
    $(function () {
        wangEditor_init();
    });
    <?php endif; ?>
    
    function gotohref()
    {
        setTimeout(function(){
            $('#error_message').hide();
            editor.$txt.html('');
        },3000);
    }

    function thanks_topic(hashId, u_id){
        if (confirm('你确定要向本话题创建者发送谢意?')) {
            var thanks_topic_url = '/favorite/'+hashId+'?t=thanks_topic_str&create_userid='+u_id;
            set_href(thanks_topic_url);
        }
    }

    function thanks_reply_topic(hashId, reply_userid, id){
        if (confirm('你确定要向本话题回复者发送谢意?')) { 
            var thanks_reply_topic_url = '/thanksReply/'+hashId+'?t=thanks_reply_topic_str&reply_userid='+reply_userid+'&rid='+id;
            set_href(thanks_reply_topic_url); 
        }
    }

    function set_href(url){
        //先判断是否coin(威望)值大于5
        var p = {'<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'};
        $.post('/coin_ajax', p, function(d){
            var data = $.parseJSON(d);
            console.log(data);
            if(data.code==1)
            {
                alert(data.msg);
            }
            else
            {
                location.href = url;
            }
        });
    }

</script>