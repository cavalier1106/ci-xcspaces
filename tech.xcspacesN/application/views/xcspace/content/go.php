
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-9 main">
                <section class="tag-info tag__info mt20 ">
                <div>
                    <img class="pull-left avatar-square-24 xcspace-tag-info-thumb" src="/images/<?php echo $node_info['img_path']; ?>">
                    <a class="h5" href="./?tab=<?php echo $node_info['f_tab_name']; ?>"><?php echo $node_info['f_name']; ?></a> 
                    <span class="h5">&nbsp;/&nbsp;</span> 
                    <span class="h5 xcspace-tag-info-title"><?php echo $node_info['name']; ?></span>
                    
                    <div class="xcspace-tag-info-total pull-right">
                        <span class="snow">话题总数</span> 
                        <strong><?php echo $titles_count; ?></strong>
                        <?php if( $is_login ): ?>
                            <span>&nbsp;•&nbsp;</span>
                            <?php if( $is_favorite ){ ?>
                            <a href="/unfavorite/<?php echo $node_info['zid']; ?>?t=nodes_str"><span class="glyphicon glyphicon-heart" aria-hidden="true"></span></a>
                            <?php }else{ ?>
                            <a href="/favorite/<?php echo $node_info['zid']; ?>?t=nodes_str"><span class="glyphicon glyphicon-heart love-color" aria-hidden="true"></span></a>
                            <?php } ?>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="xcspace-tag-info-desc">
                <?php echo $node_info['summary']; ?>
                </p>
                </section>

                <div class="stream-list question-stream">

                <?php foreach($titles as $k=>$v): ?>
                <section class="xcspace-topic-list-item">
                    <div class="blog-rank">
                        <div class="votes">
                            <a href="/user/memberTopics/<?php echo $v['create_userid']; ?>">
                                <img class="avatar-24 mr10" src="/images/<?php echo stripslashes($v['avatar']); ?>" alt="<?php echo $v['create_username']; ?>">
                            </a>
                        </div>
                        <div class="views hidden-xs">
                            <?php echo getClickUnit((int)$v['clicks']); ?><small>阅读</small>
                        </div>
                    </div>
                    <div class="summary">
                        <h2 class="title"><a href="/detail/<?php echo $v['hashId']; ?>" <?php if($v['topic_type']==2){ ?> target="_blank" <?php } ?>><?php echo $v['title']; ?></a></h2>               
                        <p class="excerpt wordbreak hidden-xs"><?php echo strCut($v['content'], 150); ?></p>
                        <ul class="author list-inline">
                            <li class="pull-right" title="">
                                <?php if($v['topic_type']==1){ ?>
                                    <?php if( $v['reply_counts'] > 0 ){ ?>
                                        <i class="fa fa-commenting"></i> <?php echo $v['reply_counts']; ?>
                                    <?php }else{ ?>
                                        <i class="fa fa-commenting-o"></i> 0
                                    <?php } ?>
                                <?php }else{ ?>
                                    <i class="fa fa-arrow-circle-o-right"></i>
                                <?php } ?>
                            </li>
                            <li>
                            <a href="/user/memberTopics/<?php echo $v['create_userid']; ?>"><?php echo $v['create_username']; ?></a>
                            创建于 
                            <?php echo xcspaces_format_date( $v['time'] ); ?>
                            </li>
                        </ul>
                    </div>
                </section>
                <?php endforeach; ?>

                <?php if( !$titles ): ?>
                <div class="widget-invite mt20 mb30 text-center">
                <p>
                <span class="text-muted">尚无话题</span>
                </p>
                </div>
                <?php endif; ?>

                </div>

                <?php if( $page ): ?>
                <div class="text-center">
                    <ul class="pagination">
                        <?php echo $page;?>
                    </ul>
                </div>
                <?php endif; ?>


            <!-- /空间页面 -->
            <!-- <?php if( isset( $msg ) ): ?> -->
                <!-- <div class="alert alert-warning" id="error_message" ><?php echo $msg['msg']; ?></div> -->
            <!-- <?php endif; ?> -->
            
            <div class="alert alert-warning" id="error_message" ></div>

            <?php if( isset($is_login) && $is_login ): ?>
            <div style="padding: 20px 10px;">
                <form class="editor-wrap">
                    <div class="editor">
                        <h4>话题标题</h4>
                        <div class="form-group">
                        <input name="title" id="title" type="text" maxlength="200" placeholder="输入标题" class="form-control" value="">
                        </div>
                        <h4>话题内容</h4>
                        <div id="content" ></div>
                    </div>
                    <div id="answerSubmit" class="clearfix">
                        <button type="button" class="getContent btn btn-info hidden-xs" ><li class="fa fa-paper-plane"></li> 创建话题</button>
                        <button type="button" class="getContent btn btn-info btn-block visible-xs-block" ><li class="fa fa-paper-plane"></li> 创建话题</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            </div>
            <!-- /.main -->
            <div class="col-xs-12 col-md-3 side mt20">
                
                <!-- <?php include($path . "/set-xcSpace-right.php"); ?> -->
                <div class="xcspace-sidebar-ranking mb50">
                    <?php include($path . "/set-xcSpaces-jiathis.php"); ?>
                </div>

                <?php if( !empty( $ads_list ) ): ?>
                <div class="sfad-sidebar lfs-img widget-box">

                    <?php foreach($ads_list as $k => $v): ?>
                        <div class="sep2" ></div>
                        <div class="sfad-item">
                            <a href="<?php echo $v['logo_href']; ?>" target="_blank">
                            <img src="/images/<?php echo $v['logo_path']; ?>" border="0" width="100%" alt="<?php echo $v['title']; ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="xcspace-sidebar-ranking">
                    <?php include($path . "/set-topic-rank.php"); ?>
                </div>

                <div class="widget-box">
                    <h2 class="h4 widget-box-space">关联空间</h2>
                    <ul class="xcspace-taglist-inline multi">
                    <?php foreach($relate_nodes as $k => $v): ?>
                        <li><a class="tag" href="/go/<?php echo $v['tab_name']; ?>" ><?php echo $v['name']; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    <?php if($is_login): ?>

    $(function () {
        // 阻止输出log
        // wangEditor.config.printLog = false;
        
        var editor = new wangEditor('content');

        // 上传图片
        editor.config.uploadImgUrl = '/xcspace/upload';
        editor.config.uploadParams = {
            'SESSIONID':'<?php echo session_id() ?>',
            'folder':'upload/topics',
            'zid':'<?php echo $node_info['zid']; ?>',
            '<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'
        };
         // 自定义load事件
        editor.config.uploadImgFns.onload = function (resultText, xhr) {
            // resultText 服务器端返回的text
            // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
            // console.log(resultText);
            // 如果 resultText 是图片的url地址，可以这样插入图片：
            editor.command(null, 'insertHtml', '<img src="' + resultText + '" style="max-width:100%;" />');
            // 如果不想要 img 的 max-width 样式，也可以这样插入：
            // editor.command(null, 'InsertImage', resultText);
        };

        // 自定义菜单
        editor.config.menus = [
            'source',
            'bold',
            'underline',
            'italic',
            'strikethrough',
            'eraser',
            'img',
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
        editor.$txt.html('请输入内容...');

        /* create_topic */

        $('.getContent').click(function () {
            var title = $('#title').val();
            var content = editor.$txt.html();
            if( title == "" )
            {
                alert('请输入标题!');
            }
            if( content == "" )
            {
                alert('请输入内容!');
            }
            // console.log(content);
            var p = {'nid':'<?php echo $node_info['fid'].','.$node_info['zid']; ?>', 'title':title, 'content':content , '<?php echo $this->token_name ?>':'<?php echo $this->token_hash ?>', 'SESSIONID':'<?php echo session_id() ?>'};
            $.post('/go_ajax', p, function(d){
                var data = $.parseJSON(d);
                // console.log(data);
                $('#error_message').text(data.msg);
                $('#error_message').show();
                gotohref();
            });
        });

    });

    <?php endif; ?>

    function gotohref()
    {
        setTimeout(function(){
            location.href = "go/<?php echo $node_info['tab_name'] ;?>"; 
        },3000);
    }

    

</script>