
<?php include($path . "/topicNodes.php"); ?>

<div id="Wrapper">
        <div class="container">
            <div class="row">

            <div class="col-xs-12 col-md-9 main">
            <br>
            <ol class="breadcrumb">
              <li><a href="/">xcSpaces</a></li>
              <li>创作新话题</li>
            </ol>

            <div class="alert alert-warning" id="error_message" ><?php if( isset( $cnt_msg ) ) echo $cnt_msg['msg'] ; ?></div>

            <form>
                <div class="form-group">
                    <label for="话题类型">话题类型</label>
                    <select class="form-control" name="topic_type" id="topic_type" >
                      <option value="1" >内部话题</option>
                      <option value="2" >外部话题</option>
                    </select>
                </div>
                <div class="form-group">
                <label for="话题标题">话题标题</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="请输入标题">
                <input type="hidden" value="0" id="zid">
                </div>
                <div class="form-group set-none" id="out_link" >
                <label for="话题来源">话题来源</label>
                <input type="text" class="form-control" name="out_link" id="out_link_input" placeholder="请输入来源链接">
                </div>
                <div class="form-group" id="topic-content" >
                    <label for="正文">话题正文</label>
                    <div id="content" ></div>
                </div>
                <div class="form-group sel-node">
                    <!-- 话题空间 &nbsp;<br/> -->
                    <?php foreach($all_nodes as $k => $v): ?>
                        <?php echo $v['name'];?>空间 &nbsp;<br/>
                        <?php foreach($v['children'] as $k2 => $v2): ?>
                        <a href="javascript:void(0);" class="node" data="<?php echo $v2['zid'].','.$v2['tab_name'];?>" ><?php echo $v2['name'];?></a> &nbsp;
                        <?php endforeach; ?>
                        <br />
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-info hidden-xs SaveContent"><li class="fa fa-paper-plane"></li> 发布话题</button>
                <button type="button" class="btn btn-info btn-block visible-xs-block SaveContent"><li class="fa fa-paper-plane"></li> 发布话题</button>
            </form>

            </div>
            
            <div class="col-xs-12 col-md-3 side mt30">
                
                <div class="xcspace-sidebar-ranking mb50">
                    <?php include($path . "/set-xcSpaces-jiathis.php"); ?>
                </div>
                <!-- <?php include($path . "/set-xcSpace-right.php"); ?> -->
                <!-- <hr> -->

                <div class="xcspace-sidebar-ranking">
                    <?php include($path . "/set-topic-rank.php"); ?>
                </div>
                
                <!-- <div class="sfad-sidebar lfs-img mt20">
                    <div class="sfad-item">
                        <a href="<?php echo $ads['logo_href']; ?>" target="_blank">
                        <img src="./images/<?php echo $ads['logo_path']; ?>" border="0" width="255" height="250" alt="100offer">
                        </a>
                    </div>
                </div> -->

            </div>

        </div>
    </div>
</div>

<?php if( isset( $cnt_msg ) ): ?>
<script>
    $(function(){
        //处理：当前话题用户已经发布过！
        $('#error_message').css('display', 'block');
    });
</script>
<?php endif; ?>

<script type="text/javascript">
    /*
     * 发布话题
     */
    function publishTopic() {
        var errors = 0;
        var em = $("#error_message");
        
        // var content = $("#content").val();
        
        var title = $("#topic_title").val();

        if (title.length == 0) 
        {
            errors++;
            em.html("话题标题不能为空");
            em.show();
        } 
        else if (title.length > 120) 
        {
            errors++;
            em.html("话题标题不能超过 120 个字符");
            em.show();
        }
        // else if (content.length > 20000) 
        // {
        //     errors++;
        //     em.html("话题内容不能超过 20000 个字符");
        //     em.show();
        // }
        else if( !$(".sel").size() )
        {
            errors++;
            em.html("请选择话题空间！");
            em.show();
        }
        
        // if (errors == 0) 
        // {
        //     var form = $("#compose");
        //     return form.submit();
        // }
        return errors;
    }

    $(function () {
        $('#topic_type').change(function(){
            var topic_type = $(this).val();
            if(topic_type==2){ 
                $('#out_link').show();
                $('#topic-content').hide(); 
            }
            if(topic_type==1){ 
                $('#out_link').hide();
                $('#topic-content').show(); 
            }
        });
        // 阻止输出log
        // wangEditor.config.printLog = false;
        
        var editor = new wangEditor('content');
        // var zid = $('#zid').val();

        // 上传图片
        editor.config.uploadImgUrl = '/xcspace/upload';
        editor.config.uploadParams = {
            'SESSIONID':'<?php echo session_id() ?>',
            'folder':'upload/topics',
            'zid':$('#zid').val(),
            '<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'
        };
         // 自定义load事件
        editor.config.uploadImgFns.onload = function (resultText, xhr) {
            // resultText 服务器端返回的text
            // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
            // console.log(resultText);
        
            if( resultText == 0 )
            {
                // alert('请先选择话题空间,在上传图片!');
            }

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
        $('.SaveContent').click(function () {
            var title = $('#title').val();
            var content = editor.$txt.html();
            var topic_type = $('#topic_type').val();
            var out_link_input = $('#out_link_input').val();

            if( title == "" )
            {
                alert('请输入标题!');
                return false;
            }
            else if( content == "" && out_link_input == "" )
            {
                alert('请输入内容 && 话题来源!');
                return false;
            }
            else if( !$(".sel").size() )
            {
                alert("请选择话题空间！");
                return false;
            }
            console.log(content);
            console.log(out_link_input);
            var p = {'zid':$('#zid').val(), 'title':title, 'content':content, 'out_link':out_link_input, 'topic_type':topic_type, '<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>', 'SESSIONID':'<?php echo session_id() ?>'};
            $.post('/createNewTitle_ajax',p,function(d){
                var data = $.parseJSON(d);
                console.log(data);
                $('#error_message').text(data.msg);
                $('#error_message').show();
                if( data.code == 0 ) gotohref();
            });
        });

        $('#title').focus();

    });

    function gotohref()
    {
        setTimeout(function(){
            location.href = "/"; 
        },3000);
    }

    

</script>