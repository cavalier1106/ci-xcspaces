
<?php include($path . "/topicNodes.php"); ?>

<div id="Wrapper">
        <div class="container">
            <div class="row">

            <div class="col-xs-12 col-md-9 main">
            <br>
            <ol class="breadcrumb">
              <li><a href="/">xcSpaces</a></li>
              <li>创作笔记</li>
            </ol>

            <div class="alert alert-warning" id="error_message" ><?php if( isset( $cnt_msg ) ) echo $cnt_msg['msg'] ; ?></div>

            <form>
                <div class="form-group">
                <label for="笔记标题">笔记标题</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="请输入标题">
                </div>
                <div class="form-group">
                <label for="正文">笔记正文</label>
                <div id="content" ></div>
                </div>
                <button type="button" class="btn btn-info hidden-xs saveNotepad"><i class="fa fa-paper-plane"></i> 创建笔记</button>
                <button type="button" class="btn btn-info btn-block visible-xs-block saveNotepad"><i class="fa fa-paper-plane"></i> 创建笔记</button>
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
        //处理：当前笔记用户已经发布过！
        $('#error_message').css('display', 'block');
    });
</script>
<?php endif; ?>

<script type="text/javascript">
    /*
     * 发布笔记
     */
    function publishTopic() {
        var errors = 0;
        var em = $("#error_message");
        
        // var content = $("#content").val();
        
        var title = $("#topic_title").val();

        if (title.length == 0) 
        {
            errors++;
            em.html("笔记标题不能为空");
            em.show();
        } 
        else if (title.length > 120) 
        {
            errors++;
            em.html("笔记标题不能超过 120 个字符");
            em.show();
        }

        return errors;
    }

    $(function () {
        // 阻止输出log
        // wangEditor.config.printLog = false;
        
        var editor = new wangEditor('content');
        // var zid = $('#zid').val();

        // 上传图片
        editor.config.uploadImgUrl = '/xcspace/upload';
        editor.config.uploadParams = {
            'SESSIONID':'<?php echo session_id() ?>',
            'folder':'upload/notepad',
            '<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'
        };
         // 自定义load事件
        editor.config.uploadImgFns.onload = function (resultText, xhr) {
            // resultText 服务器端返回的text
            // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
            // console.log(resultText);
        
            if( resultText == 0 )
            {
                // alert('请先选择笔记空间,在上传图片!');
            }

            // 如果 resultText 是图片的url地址，可以这样插入图片：
            editor.command(null, 'insertHtml', '<p class="text-center"><img src="' + resultText + '" style="max-width:100%;" /></p>');
            // 如果不想要 img 的 max-width 样式，也可以这样插入：
            // editor.command(null, 'InsertImage', resultText);
        };

        // 自定义菜单
        // http://www.kancloud.cn/wangfupeng/wangeditor2/113975
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
            'aligncenter',
            'fullscreen',
        ];

        editor.create();

        // 初始化编辑器的内容
        editor.$txt.html('请输入内容...');

        /* create_topic */
        $('.saveNotepad').click(function () {
            var title = $('#title').val();
            var content = editor.$txt.html();
            if( title == "" )
            {
                alert('请输入标题!');
                return false;
            }
            else if( content == "" )
            {
                alert('请输入正文!');
                return false;
            }
            console.log(content);
            var p = {'title':title, 'content':content, '<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>', 'SESSIONID':'<?php echo session_id() ?>'};
            $.post('/createNotepad_ajax',p,function(d){
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