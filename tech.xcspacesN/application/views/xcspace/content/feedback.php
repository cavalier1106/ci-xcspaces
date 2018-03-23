
<?php include($path . "/topicNodes.php"); ?>

<div id="Wrapper">
        <div class="container">
            <div class="row">

            <div class="col-xs-12 col-md-9 main">
            <br>
            <ol class="breadcrumb">
              <li><a href="/">xcSpaces</a></li>
              <li>反馈</li>
            </ol>
            
            <?php if( isset( $msg ) ): ?>
                <div class="alert alert-warning"><?php echo $msg['msg'] ; ?></div>
            <?php endif; ?>

            <form method="post" action="/feedback" >
                <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
                <div class="form-group">
                    <label for="反馈正文">反馈正文</label>
                    <textarea maxlength="20000" id="editor" class="form-control" name="content" rows="10" placeholder="请输入正文..."></textarea>
                </div>
                <button type="submit" class="btn btn-info hidden-xs" >
                    <li class="fa fa-paper-plane"></li> 发送反馈
                </button>
                <button type="submit" class="btn btn-info btn-block visible-xs-block" >
                    <li class="fa fa-paper-plane"></li> 发送反馈
                </button>
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

                <div class="xcspace-sidebar-ranking mt30">
                    <?php include($path . "/set-coin-rank.php"); ?>
                </div>

            </div>

        </div>
    </div>
</div>